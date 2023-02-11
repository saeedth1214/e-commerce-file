<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Attribute extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'slug', 'type_id'];

    public function files()
    {
        return $this->belongsToMany(File::class, 'attributes_values', 'attribute_id', 'file_id')->withPivot(['value']);
    }
}
