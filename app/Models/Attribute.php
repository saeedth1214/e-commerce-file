<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Attribute extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'slug'];

    protected $created_at = null;
    protected $updated_at = null;
    public function files()
    {

        return $this->belongsToMany(File::class, 'attributes_values', 'attribute_id', 'file_id');
    }
}
