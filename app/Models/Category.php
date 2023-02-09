<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Category extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, SoftDeletes;
    protected $fillable = [
        'parent_id',
        'name',
        'slug',
    ];
    public function files()
    {
        return $this->hasMany(File::class);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('category-image')->singleFile();
    }

    public function sub_category()
    {
        return $this->belongsTo(static::class, 'parent_id');
    }
}
