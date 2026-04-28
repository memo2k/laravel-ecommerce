<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Attribute extends Model
{
    protected $fillable = ['name', 'description'];

    public function attributeOptions(): HasMany
    {
        return $this->hasMany(AttributeOption::class);
    }

    public function productCategories(): BelongsToMany
    {
        return $this->belongsToMany(
            ProductCategory::class,
            'product_category_attribute',
            'attribute_id',
            'product_category_id'
        );
    }
}
