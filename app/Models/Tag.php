<?php

namespace App\Models;

use App\Traits\ObservTag;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tag extends Model
{
    use HasFactory, SoftDeletes, ObservTag;

    protected $fillable = [
        'name', 'slug', 'id'
    ];

    public function files()
    {
        return $this->belongsToMany(File::class, 'file_has_tags');
    }
}
