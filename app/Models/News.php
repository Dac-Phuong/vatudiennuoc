<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    use HasFactory;
    protected $fillable = [
        'category_id',
        'author_id',
        'title',
        'content',
        'views',
        'slug',
        'is_show',
        'is_pin',
        'thumbnail',
        'short_description',
    ];
    public const STATUS_ACTIVE = 1;
    public const STATUS_HIDE   = 0;
    public const STATUS_PIN    = 1;
    public const STATUS_UNPIN  = 0;
    /**
     * Get the author of the news.
     */
    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }
    public function category()
    {
        return $this->belongsTo(NewsCategory::class, 'category_id');
    }
    public function tags()
    {
        return $this->belongsToMany(Tags::class, 'news_tags', 'news_id', 'tags_id');
    }
}
