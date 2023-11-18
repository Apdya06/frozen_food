<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Brands extends Model{
    protected $fillable= array('manufacturer_name','brand_name','founded_year', 'origin', 'country_origin', 'address', 'email' );
}