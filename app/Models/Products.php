<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Brands extends Model{
    protected $fillable= array(
        'manufacturer_name', 'origin','brand_name','product_name',
        'product_code', 'category','shelf_life', 'price', 'stock', 'rating'
    );
}
