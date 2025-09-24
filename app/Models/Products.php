<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Products extends Model
{
    use HasFactory;
    protected $fillable = ['name',
        'slug',
        'sku',
        'content',
        'short_desc',
        'price',
        'discount',
        'quantity',
        'status',
        'type',
        'category_id',
        'brand_id',
        'thumbnail',
    ];
    // Quan hệ: 1 sản phẩm thuộc 1 danh mục
    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    // Quan hệ: 1 sản phẩm thuộc 1 thương hiệu
    public function brand()
    {
        return $this->belongsTo(ProductBrand::class, 'brand_id');
    }

    // Quan hệ: 1 sản phẩm có nhiều ảnh
    public function images()
    {
        return $this->hasMany(ProductImage::class, 'product_id');
    }
    // Quan hệ: 1 sản phẩm có nhiều biến thể (size, màu…)
    public function variants()
    {
        return $this->hasMany(ProductVariant::class, 'product_id');
    }

    // Quan hệ: 1 sản phẩm có nhiều review
    public function reviews()
    {
        return $this->hasMany(ProductReview::class, 'product_id');
    }
    // Quan hệ: 1 sản phẩm có 1 SEO
    public function seo()
    {
        return $this->hasMany(ProductSeo::class, 'product_id');
    }
}
