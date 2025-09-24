<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductReview extends Model
{
    use HasFactory;
    final protected $fillable = ['product_id', 'user_id', 'rating', 'comment'];
    public function products()
    {
        return $this->belongsTo(Products::class, 'product_id');
    }
}
