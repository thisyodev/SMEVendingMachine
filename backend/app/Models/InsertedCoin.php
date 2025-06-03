<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InsertedCoin extends Model
{
    public $timestamps = true;

    protected $fillable = ['denomination', 'quantity'];
}
