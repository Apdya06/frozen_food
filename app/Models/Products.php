<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Brands extends Model{
    protected $fillable= array('product_name','category','shelf_life', 'price', 'stock', 'review', 'email' );
}