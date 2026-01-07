<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product_sizes extends Model
{
    //

    protected $fillable = [
        'productId',
        'productName',
        'size',

    ];
}
