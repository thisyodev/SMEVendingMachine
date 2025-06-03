<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'total_paid',
        'change_given',
    ];

    protected $casts = [
        'change_given' => 'array',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
