<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductSize extends Model
{
    //

    protected $fillable = [
        'productId',
        'productName',
        'size',

    ];

    protected $table = 'product_sizes';

    public function product()
    {
        return $this->belongsTo(Products::class, 'productId');
    }

}
