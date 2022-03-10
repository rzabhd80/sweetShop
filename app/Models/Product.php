<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function images()
    {
        return $this->morphMany(Image::class, "imageable");
    }
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }
}
