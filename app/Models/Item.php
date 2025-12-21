<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

     protected $fillable = [
    // --- ブラウザ（フォーム）から直接送られてくる項目 ---
    'title',
    'memo',
    'location',
    'sche_start_date', // 元の日付
    'sche_end_date',   // 元の日付
    'sche_start_time',
    'sche_end_time',
    'type',            // ラジオボタンの値 (schedule等)
    'done',            // チェックボックスの値

    // --- Requestのmerge()で追加した項目 ---
    'type_id',         // これが抜けると General error: 1364 になります
    'status_id',       // ステータス判定用
    'sche_start',      // 加工後の日時
    'sche_end',        // 加工後の日時

    // --- その他システムでセットする項目 ---
    'user_id',         // ログインユーザーID
];
}
