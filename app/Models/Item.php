<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'type_id',
        'subcategory_id',
        'status_id',
        'sche_done',
        'location',
        'title',
        'sche_start',
        'sche_end',
        //  ☆☆☆☆☆☆☆☆☆☆☆☆☆☆☆☆
        //  ※要調整
        // 'sche_start_date', // 元の日付
        // 'sche_end_date',   // 元の日付
        // 'sche_start_time',
        // 'sche_end_time',
        //  ☆☆☆☆☆☆☆☆☆☆☆☆☆☆☆☆
        'memo',
        'done',            // チェックボックスの値
    ];
}
