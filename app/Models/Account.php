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
        'repeats_id',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    // app/Models/Account.php
public function repeat() { return $this->belongsTo(Repeat::class, 'repeats_id'); }

}
