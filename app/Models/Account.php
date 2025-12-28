<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'subcategory_id',
        'account_category_id',
        'type_id',
        'status_id',
        'date',
        'title',
        'amount',
        'memo',
    ];

    protected $casts = [
        'date' => 'date',
    ];
}
