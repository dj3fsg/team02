<?php

namespace App\Http\Controllers;

use App\Http\Requests\ItemStoreRequest;
use App\Http\Requests\ItemUpdateRequest;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
/*use App\Http\Controllers\CalendarController;*/
use App\Models\Item;
use App\Models\Repeat;
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

    // =========================
    // 1) 起点日時を type で分岐
    // =========================
    if ($request->type === 'schedule') {

        // schedule：日付結合
        if ($request->has('all_day')) {
            $startDateStr = $request->sche_start_date . ' 00:00:00';
            $endDateStr   = $request->sche_end_date   . ' 23:59:59';
        } else {
            $startDateStr = $request->sche_start_date . ' ' . $request->sche_start_time;
            $endDateStr   = $request->sche_start_date . ' ' . $request->sche_end_time;
        }

        $originStart = \Carbon\Carbon::parse($startDateStr);
        $originEnd   = \Carbon\Carbon::parse($endDateStr);

    } else {
        // scheduleでない場合（task）：sche_done を起点にする
        $originStart = \Carbon\Carbon::parse($request->sche_done);
        $originEnd   = \Carbon\Carbon::parse($request->sche_done);
    }

    $baseStart    = $originStart->copy();
    $baseEnd      = $originEnd->copy();
    $currentStart = $originStart->copy();
    $currentEnd   = $originEnd->copy();

    // =========================
    // 2) 繰り返し期限
    // =========================
    $until = $request->filled('repeat_until')
        ? \Carbon\Carbon::parse($request->repeat_until)->endOfDay()
        : $originStart->copy();

    $maxLoops = 1200;
    $count    = 1;
    $repeatId = null;

    // =========================
    // 3) repeats 作成
    // =========================
    if ($request->repeat !== '0') {
        $repeat   = Repeat::create([]);
        $repeatId = $repeat->id;
    }

    // =========================
    // 4) 登録ループ
    // =========================
    do {
        $item = new Item();
        $item->title      = $request->title;
        $item->user_id    = auth()->id();
        $item->repeats_id = $repeatId;
        $item->location   = $request->location;
        $item->memo       = $request->memo;
        $item->status_id  = 1; // 未完了

        if ($request->type === 'schedule') {

            // ---- schedule ----
            $item->subcategory_id = 1;
            $item->sche_start     = $currentStart->toDateTimeString();
            $item->sche_end       = $currentEnd->toDateTimeString();
            $item->type_id        = $request->has('all_day') ? 2 : 1;

        } else {

            // ---- task ----
            $item->subcategory_id = 2;
            $item->sche_done      = $currentStart->toDateTimeString(); // ★ここに保存
            $item->type_id        = 1;
        }

        $item->save();

        // 繰り返しなし or 上限到達
        if ($request->repeat == '0' || $count >= $maxLoops) {
            break;
        }

        // 加算用の差分
        $durationSeconds = $baseEnd->diffInSeconds($baseStart);

        // =========================
        // 5) 日付加算
        // =========================
        if ($request->repeat == '1') {
            $currentStart = $baseStart->copy()->addWeeks($count);
        } elseif ($request->repeat == '2') {
            $currentStart = $baseStart->copy()->addMonthsNoOverflow($count);
        } elseif ($request->repeat == '3') {
            $currentStart = $baseStart->copy()->addYearsNoOverflow($count);
        } else {
            break;
        }

        $currentEnd = $currentStart->copy()->addSeconds($durationSeconds);
        $count++;

    } while ($currentStart->lte($until));

    return redirect('calendar')->with('success', '登録しました');
}

 public function update(Request $request, $id)
    {

        $item = Item::findOrFail($id);

        // scope: single / future / all （フォームから来る想定）
        $scope = $request->input('scope', 'single');

        // シリーズ無し → 単体更新
        if (empty($item->repeats_id)) {
            return $this->updateSingleNoSeries($request, $item);
        }

        // シリーズ有り → 個別 or 一括
        if ($scope === 'single') {
            return $this->updateSingleInSeries($request, $item);
        }

        // future / all
        return $this->updateBulkInSeries($request, $item, $scope);
    }

    // =========================================================
    // A. シリーズ無し（repeats_id = null） → この1件だけ更新
    // =========================================================
    private function updateSingleNoSeries(Request $request, Item $item)
    {
        $this->applyUpdateFromRequest($item, $request);
        $item->save();

        return $this->redirectToDay($item);
    }

    // =========================================================
    // B. シリーズ有りの個別更新 → 例外化（シリーズから外してこの1件だけ更新）
    //    ※「この予定のみ変更」を、一括更新に上書きされないようにする定番仕様
    // =========================================================
    private function updateSingleInSeries(Request $request, Item $item)
    {
       
        DB::transaction(function () use ($request, $item) {
           
            $this->applyUpdateFromRequest($item, $request);
            $item->save();
        });

        return $this->redirectToDay($item);
    }

    // =========================================================
    // C. シリーズ有りの一括更新 → 再附番してシリーズ分割
    //    scope=future: この日以降
    //    scope=all   : シリーズ全部
    // =========================================================
    private function updateBulkInSeries(Request $request, Item $item, string $scope)
    {
        DB::transaction(function () use ($request, $item, $scope) {

            $oldRepeatId = $item->repeats_id;

            // ★再附番：更新対象用の新しいシリーズIDを発行
            $newRepeatId = Repeat::create([])->id;

            // 対象取得（同一ユーザーに限定）
            $q = Item::where('repeats_id', $oldRepeatId)
                ->where('user_id', $item->user_id);

            if ($scope === 'future') {
                if($item->subcategory==1){
                    $q->where('sche_start', '>=', $item->sche_start);

                }else{
                    $q->where('sche_done', '>=', $item->sche_done);
                }
                
            } elseif ($scope === 'all') {
                // そのまま全件
            } else {
                // 想定外は安全側で単体扱い
                $q->where('id', $item->id);
            }

            $targets = $q->orderBy('sche_start')->lockForUpdate()->get();

            foreach ($targets as $t) {
                $this->applyUpdateFromRequestWithoutDate($t, $request);

                // ★ここが肝：対象範囲だけ repeats_id を新しいものに差し替え
                $t->repeats_id = $newRepeatId;

                $t->save();
            }

            // （任意）oldRepeatId が誰にも参照されなくなったら repeats を掃除したいならここで検討
        });

        return $this->redirectToDay($item);
    }

    // =========================
    // 共通：Request → Item 反映
    // =========================
        private function applyUpdateFromRequest(Item $item, Request $request): void
    {
        $item->title    = $request->title;
        $item->location = $request->location;
        $item->memo     = $request->memo;

        // checkboxの有無で判定（完了：2 / 未完了：1）
        $item->status_id = $request->has('status_id') ? 2 : 1;

        // subcategory_id: 1=スケジュール, それ以外=タスク想定
        if ($item->subcategory_id == 1) {
            if ($request->has('all_day')) {
                // 画面の入力（date）を優先
                $item->sche_start = $request->sche_start;
                $item->sche_end   = $request->sche_end;
                $item->type_id    = 2; // 終日
            } else {
                $item->sche_start = $request->sche_start_date . ' ' . $request->sche_start_time;
                $item->sche_end   = $request->sche_start_date . ' ' . $request->sche_end_time;
                $item->type_id    = 1; // 時刻あり
            }
        } else {
            $item->sche_done = $request->sche_done;
        }
    }

        private function applyUpdateFromRequestWithoutDate(Item $item, Request $request): void
    {
        $item->title     = $request->title;
        $item->location  = $request->location;
        $item->memo      = $request->memo;
        $item->status_id = $request->has('status_id') ? 2 : 1;

      
        // type_id も「終日/時間」を一括で変えたくないなら触らないのが安全
        // もし揃えたいなら下の1行だけ入れる：
        // $item->type_id = $request->has('all_day') ? 2 : 1;
    }


    // =========================
    // 共通：日付に戻す
    // =========================
    private function redirectToDay(Item $item)
    {
        return redirect()->route('calendar.events.show', [
            'date' => Carbon::parse($item->sche_start)->format('Y-m-d'),
        ])->with('success', '更新しました');
    }
    public function delete(Request $request, $id)
{
    $item  = Item::findOrFail($id);
    $scope = $request->input('scope', 'single'); // single / future / all

    // A. シリーズ無し
    if (empty($item->repeats_id)) {
        return $this->deleteSingleNoSeries($item);
    }

    // B. シリーズ有り・個別
    if ($scope === 'single') {
        return $this->deleteSingleInSeries($item);
    }

    // C. シリーズ有り・一括
    return $this->deleteBulkInSeries($item, $scope);
}

private function deleteSingleNoSeries(Item $item)
{
    $date = $item->sche_start;
    $item->delete();

    return redirect()->route('calendar.events.show', [
        'date' => \Carbon\Carbon::parse($date)->format('Y-m-d'),
    ])->with('success', '削除しました');
}

private function deleteSingleInSeries(Item $item)
{
    $date = $item->sche_start;
    $item->delete();

    return redirect()->route('calendar.events.show', [
        'date' => \Carbon\Carbon::parse($date)->format('Y-m-d'),
    ])->with('success', 'この予定のみ削除しました');
}

private function deleteBulkInSeries(Item $item, string $scope)
{
    $date = $item->sche_start;

    $q = Item::where('repeats_id', $item->repeats_id)
        ->where('user_id', $item->user_id);

    if ($scope === 'future') {
        $q->where('sche_start', '>=', $item->sche_start);
    } elseif ($scope === 'all') {
        // 全件
    } else {
        $q->where('id', $item->id);
    }

    $q->delete();

    return redirect()->route('calendar.events.show', [
        'date' => \Carbon\Carbon::parse($date)->format('Y-m-d'),
    ])->with('success', '予定を削除しました');
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