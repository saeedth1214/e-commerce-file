<?php

namespace App\Models;

use App\Traits\ObservCategory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasFactory,SoftDeletes,ObservCategory;
    protected $fillable = [
        'parent_id',
        'name',
        'slug',
    ];
    public function files()
    {
        return $this->hasMany(File::class);
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
