<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Repeat extends Model
{
    use HasFactory;
    // app/Models/Repeat.php
    public function items() { 
        return $this->hasMany(Item::class, 'repeats_id'); }
    public function accounts() { 
        return $this->hasMany(Account::class, 'repeats_id'); }

}
