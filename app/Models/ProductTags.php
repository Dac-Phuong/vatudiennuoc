<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductTags extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'slug'];
    public function products()
    {
        return $this->belongsToMany(Products::class, 'product_tags', 'tag_id', 'product_id');
    }
}
