<?php

namespace App\Http\Controllers;

use App\Http\Requests\ItemStoreRequest;
use App\Http\Requests\ItemUpdateRequest;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
/*use App\Http\Controllers\CalendarController;*/
use App\Models\Item;
use Illuminate\Support\Facades\Auth;

class ItemController extends Controller
{

  public function create()
    {
       

        return view('item.create');
    }
  
public function store(ItemStoreRequest $request)
{
    $validated = $request->validated(); 

    // 1. 日時の結合（HTMLのname属性に合わせる）
    $startDateStr = $request->sche_start_date . ' ' . ($request->sche_start_time ?: '00:00:00');
    $endDateStr   = $request->sche_start_date . ' ' . ($request->sche_end_time   ?: '23:59:59');

    $currentStart = \Carbon\Carbon::parse($startDateStr);
    $currentEnd   = \Carbon\Carbon::parse($endDateStr);

    // 2. 期限
    $until = $request->filled('repeat_until') 
                ? \Carbon\Carbon::parse($request->repeat_until)->endOfDay() 
                : $currentStart->copy();

    $maxLoops = 1200;//3年先までを制限にするという想定
    $count = 0;

    do {
        $item = new Item();
        $item->title = $request->title;
        $item->user_id = auth()->id();
        $item->location = $request->location; 
        $item->memo = $request->memo;

        // スケジュールかタスクかの判定
        if ($request->type === 'schedule') {
            $item->subcategory_id = 1;
            $item->sche_start = $currentStart->toDateTimeString();
            $item->sche_end   = $currentEnd->toDateTimeString();
            // 終日判定（HTMLのID chk_all_day に合わせる）
            $item->type_id = $request->has('all_day') ? 2 : 1; 
        } else if ($request->type === 'task') {
            $item->subcategory_id = 2;
            $item->sche_start = $request->sche_done; // タスク期限
            $item->sche_end = $request->sche_done;
            $item->type_id = $request->has('all_day') ? 2 : 1; 
        }

        // ステータス（HTMLの name="status_id" に合わせる）
        $item->status_id = $request->has('status_id') ? 2 : 1; 

        // ★ここで保存
        $item->save();

       // --- 繰り返し判定を修正 ---
    // repeatのvalueが文字列か数値か、HTMLと合わせる
    if ($request->repeat == '0' || $count >= $maxLoops) {
        break;
    }

    // --- 加算処理（ここが重要：1=毎週, 2=毎月, 3=毎年 になっているか確認） ---
    if ($request->repeat == '1') {
        $currentStart->addWeek();
        $currentEnd->addWeek();
    } elseif ($request->repeat == '2') {
        $currentStart->addMonth();
        $currentEnd->addMonth();
    } elseif ($request->repeat == '3') {
        $currentStart->addYear();
        $currentEnd->addYear();
    } else {
        // 想定外の値が来た場合も無限ループ防止で抜ける
        break; 
    }

    $count++;
// 次の予定が期限内であれば続行
} while ($currentStart->lte($until));

    return redirect('calendar')->with('success', '登録しました');
}
public function update(Request $request, $id)
{
    $item = Item::findOrFail($id);
    
    $item->title = $request->title;
    $item->location = $request->location;
    $item->memo = $request->memo;
    $item->status_id = $request->has('status_id') ? 2 : 1;

    // all_dayチェックの有無で、使う値を明示的に分ける
    if ($request->has('all_day')) {
        // 終日ONの場合
        $item->sche_start = $request->sche_start_date . ' 00:00:00';
        $item->sche_end   = $request->sche_end_date . ' 23:59:59';
        $item->type_id = 2;
    } else {
        // 終日OFF（時間指定）の場合
        $item->sche_start = $request->sche_start_date . ' ' . $request->sche_start_time;
        $item->sche_end   = $request->sche_start_date . ' ' . $request->sche_end_time;
        $item->type_id = 1;
    }

    $item->save();
    return redirect()->route('calendar.events.show', [
            'date' => \Carbon\Carbon::parse($item->sche_start)->format('Y-m-d')
        ])->with('success', '更新しました');
    }

    public function delete($id)
    {
      $item = Item::findOrFail($id);
    
    // 論理削除（status_idを99に更新）
    $item->status_id = 99;
    $item->save();

    // 削除後に当日詳細ー一覧へリダイレクト
    return redirect()->route('calendar.events.show', [
        'date' => \Carbon\Carbon::parse($item->sche_start)->format('Y-m-d')
    ])->with('success', '削除しました');
    }


    public function edit($id){
       // 1) 編集対象のデータ（id）を取得
    $item = Item::where('id', $id) 
        ->where('user_id', auth()->id()) // ★本人の予定だけ
        ->where('status_id', '<>', 99)//削除済みのものは出さない
        ->firstOrFail();

     return view('item.edit', compact('item'));

    }
   
}