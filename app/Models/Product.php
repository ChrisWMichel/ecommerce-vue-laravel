<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Product extends Model
{
    use HasFactory;
    use HasSlug;
    use SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'price',
        'published',
        'quantity',
        'created_by',
        'updated_by',
    ];

    public function getSlugOptions() : SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('title')
            ->saveSlugsTo('slug');
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    // public function category()
    // {
    //     return $this->belongsTo(Category::class);
    // }

    public function images()
    {
        return $this->hasMany(ProductImage::class)->orderBy('position');
    }

    public function getImageAttribute()
    {
        return $this->images->count() > 0 ? $this->images->get(0)->url : null;
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'product_categories'); 
    }
}
