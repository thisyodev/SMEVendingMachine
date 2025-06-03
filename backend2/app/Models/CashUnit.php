<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashUnit extends Model
{
    protected $fillable = ['value', 'quantity'];

    public static function addUnit($value, $count = 1)
    {
        $unit = self::where('denomination', $value)->first();

        if ($unit) {
            $unit->quantity += $count;
            $unit->save();
        } else {
            self::create([
                'denomination' => $value,
                'quantity' => $count
            ]);
        }

        return true;
    }
}
