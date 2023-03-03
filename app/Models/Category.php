<?php

namespace App\Models;

use App\Traits\ObservCategory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Category extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, SoftDeletes,ObservCategory;
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

    public function subCategories()
    {
        return $this->hasMany(self::class, 'parent_id');
    }
    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }
}
