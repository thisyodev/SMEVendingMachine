<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashInMachine extends Model
{
    public $timestamps = false;
    protected $primaryKey = 'denomination';
    public $incrementing = false;

    protected $fillable = ['denomination', 'quantity'];
}
