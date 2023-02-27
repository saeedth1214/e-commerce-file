<?php

namespace App\Models;

use App\Enums\CommentStatusEnum;
use App\Traits\ObservComment;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Qirolab\Laravel\Reactions\Contracts\ReactableInterface;
use Qirolab\Laravel\Reactions\Traits\Reactable;


class Comment extends Model implements ReactableInterface
{
    use HasFactory, Reactable, SoftDeletes, ObservComment;

    protected $fillable = [
        'user_id',
        'parent_id',
        'content',
        'status',

    ];
    protected $attributes = [
        'status' => 0,
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function file()
    {
        return $this->belongsTo(File::class);
    }

    public function replies()
    {
        return $this->hasMany(self::class, 'parent_id');
    }


    public function acceptedReplies()
    {
        return $this->replies()->where('status', CommentStatusEnum::ACCEPT);
    }
    public function scopeOrderby(Builder $query, string $orderBy = 'desc')
    {
        return $query->when(
            $orderBy = 'desc',
            fn ($query)
            =>
            $query->latest()->whereNull('parent_id')->skip(0)->take(10)
        )
            ->when(
                $orderBy = 'asc',
                fn ($query)
                =>
                $query->oldest()->whereNull('parent_id')->skip(0)->take(10)
            );
    }
}
