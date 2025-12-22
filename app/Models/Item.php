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
        'memo',
    ];
}