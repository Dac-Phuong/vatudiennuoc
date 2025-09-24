<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductBrand extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'slug', 'logo', 'is_show', 'is_pin'];
    public function products()
    {
        return $this->hasMany(Products::class, 'brand_id');
    }
}
