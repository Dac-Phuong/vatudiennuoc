<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewsTags extends Model
{
    use HasFactory;
    protected $fillable = [
        'news_id',
        'tags_id',
    ];
    public function news()
    {
        return $this->belongsTo(News::class);
    }
    public function tags()
    {
        return $this->belongsTo(Tags::class);
    }
}
