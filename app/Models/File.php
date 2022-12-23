<?php

namespace App\Models;

use App\Enums\CommentStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Qirolab\Laravel\Reactions\Contracts\ReactableInterface;
use Qirolab\Laravel\Reactions\Traits\Reactable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class File extends Model implements HasMedia, ReactableInterface
{
    use HasFactory, InteractsWithMedia, Reactable, SoftDeletes;

    protected $fillable = [
        'id',
        'title',
        'description',
        'sale_as_single',
        'percentage',
        'amount',
        'rebate',
        'download_count',
        'category_id',
        'link'
    ];


    public static function boot(): void
    {
        parent::boot();
        static::deleting(function ($file) {
            $file->comments()->delete();
        });
    }

    public function plans()
    {
        return $this->belongsToMany(Plan::class, 'plan_has_files');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_has_files')->withPivot(['amount', 'bought_at']);
    }

    public function scopeCategoryId(Builder $query, $value)
    {
        return $query->whereHas('category', fn ($query) => $query->where('id', $value));
    }
    public function scopeCategoryName(Builder $query, $value)
    {
        return $query->whereHas('category', fn ($query) => $query->where('name', $value));
    }

    public function scopeBanners(Builder $query, $value)
    {
        return $query->whereNotNull('rebate')->orderbyDesc('rebate')->take(2);
    }
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('file-image')->singleFile();
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'file_has_tags', 'tag_id', 'file_id');
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'model');
    }
    public function acceptedMainComments()
    {
        return $this->comments()->whereNull('parent_id')->where('status', CommentStatusEnum::ACCEPT);
    }
    public function mainComments()
    {
        return $this->comments()->whereNull('parent_id');
    }
    public function orders()
    {
        return $this->belongsToMany(Order::class, 'order_has_files')->withPivot([
            'amount',
            'rebate',
            'amount_after_rebate'
        ]);
    }

    public function scopeTagId(Builder $query, $tagId)
    {
        return $query->whereHas('tags', fn ($query) => $query->where('tag_id', $tagId));
    }
    public function scopeUserId(Builder $query, int $user_id)
    {
        return $query->whereHas('users', fn ($query) => $query->where('user_id', $user_id));
    }
}
