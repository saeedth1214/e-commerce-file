<?php

namespace App\Models;

use App\Enums\AttributeTypeEnum;
use App\Enums\CommentStatusEnum;
use App\Enums\FileFormatEnum;
use App\Traits\ObservFile;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Redis;
use Qirolab\Laravel\Reactions\Contracts\ReactableInterface;
use Qirolab\Laravel\Reactions\Traits\Reactable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class File extends Model implements HasMedia, ReactableInterface
{
    use HasFactory, InteractsWithMedia, Reactable, SoftDeletes, ObservFile;

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
        return $this->belongsToMany(User::class, 'user_has_files')
            ->withPivot(['total_amount', 'voucher_id', 'bought_at']);
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
        return $this->belongsToMany(Tag::class, 'file_has_tags');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'file_id');
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
        return $this->morphToMany(Order::class, 'orderable');
    }

    public function scopeTagId(Builder $query, $tagId)
    {
        return $query->whereHas('tags', fn ($query) => $query->where('tag_id', $tagId));
    }
    public function scopeTagName(Builder $query, $name)
    {
        return $query->whereHas('tags', fn ($query) => $query->where('name', $name));
    }
    public function scopeType(Builder $query, $name)
    {
        return $query->whereHas('category', fn ($query) => $query->where('name', $name));
    }
    public function scopeUserId(Builder $query, int $user_id)
    {
        return $query->whereHas('users', fn ($query) => $query->where('user_id', $user_id));
    }

    public function scopeMostVisited()
    {

        $files = Redis::zRange('view-counter', 0, 6, true);
        $grouped = collect($files)->maptoGroups(function ($view, $key) {
            return [
                $key => [
                    'title' => Redis::hGet($key, 'title'),
                    'category_name' => Redis::hGet($key, 'category_name'),
                    'media_url' => Redis::hGet($key, 'media_url'),
                    'views' => $view
                ],
            ];
        })->map(fn ($group) => $group->first());

        return $grouped->toArray();
    }

    public function attributes()
    {
        return $this->belongsToMany(Attribute::class, 'attributes_values', 'file_id', 'attribute_id')->withPivot(['value']);
    }

    public function scopeFormat(Builder $query, $format)
    {
        return $query->join('attributes_values', 'files.id', '=', 'attributes_values.file_id')
            ->join('attributes', 'attributes.id', '=', 'attributes_values.attribute_id')
            ->select('files.*', 'attributes.type', 'attributes_values.value')
            ->where('type', AttributeTypeEnum::FORMAT)
            ->where('value', $format);
    }

    public function format()
    {
        $fileFormat = $this->attributes()->where('type', AttributeTypeEnum::FORMAT)->first();
        return $fileFormat ?  FileFormatEnum::asString($fileFormat->pivot->value) : FileFormatEnum::asString(FileFormatEnum::PSD);
    }
}
