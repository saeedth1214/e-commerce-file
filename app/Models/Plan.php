<?php

namespace App\Models;

use App\Enums\CommentStatusEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Plan extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, SoftDeletes;

    protected $fillable = [
        'id',
        'title',
        'description',
        'amount',
        'rebate',
        'percentage',
        'daily_download_limit_count',
        'activation_at',
        'activation_days',
    ];

    public static function boot(): void
    {
        parent::boot();
        static::deleting(function ($plan) {
            $plan->comments()->delete();
        });
    }
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_has_plans');
    }

    public function transaction()
    {
        return $this->morphOne(Transaction::class, 'model');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('plan-image')->singleFile();
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

        return $this->hasMany(Order::class);
    }

    public function scopePlanComments(Builder $query, int $per_page = 15)
    {
        return $query->with('comments.user')->paginate($per_page);
    }
}
