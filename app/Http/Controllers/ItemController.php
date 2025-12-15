<?php

namespace App\Http\Controllers;

use App\Models;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use app\Http\Controllers\CalendarController;

class ItemController extends Controller
{

  /**TODO:メソッドの形を改造する、メソッドの選定はこれでOKのはず */
  
    public function store(Request $request)
    {
       Item::create($request->validated());
        

        return redirect('onecal/list')->with('success', '登録しました');
    }

    public function update(Request $request, $id)
    {

        $item = Item::findOrFail($id);
        Item::update($request->validated());
       

        return redirect('onecal/list')->with('success', '更新しました');
    }

    public function delete($id)
    {
        DB::transaction(function () use ($id): void {

            $item = Item::findOrFail($id);

            // スケジュール・タスクを論理削除
            $item->update([
                'status_id' => 99,
            ]);

        });

        return redirect('npb/games')->with('success', '試合を削除しました');
    }

    public function edit($id){
       // 1) 編集対象の試合データ（game）を取得
    $game = Game::with(['homeTeam', 'visiterTeam', 'stadiums', 'batting_orders'])
        ->findOrFail($id);

    // 2) セレクトボックスに使う全データを取得
    $teams     = Team::orderBy('id')->get();
    $stadiums  = Stadium::orderBy('id')->get();
    $positions = Position::orderBy('id')->get();
    $players   = Player::orderBy('id')->get();

    // 3) edit.blade.php に全部渡す
    return view(
        'npb.edit',
        compact('game', 'teams', 'stadiums', 'positions', 'players')
    );

    }
}   
}