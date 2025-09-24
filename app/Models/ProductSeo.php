<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductSeo extends Model
{
    use HasFactory;
    protected $fillable = ['meta_title', 'meta_description', 'meta_keywords'];
    public function product()
    {
        return $this->belongsTo(Products::class, 'product_id');
    }
}
