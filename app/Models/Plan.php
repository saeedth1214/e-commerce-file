<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Plan extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'id',
        'title',
        'description',
        'amount',
        'rebate',
        'percentage',
        'daily_download_limit_count',
        'daily_free_download_limit_count',
        'type',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_has_plans');
    }

    public function transaction()
    {
        return $this->morphOne(Transaction::class, 'model');
    }



    public function scopeUserId(Builder $query, int $user_id)
    {
        return $query->whereHas('users', fn ($query) => $query->where('user_id', $user_id));
    }
}
