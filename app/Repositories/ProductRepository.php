<?php

namespace App\Repositories;

use App\Models\AttributeOption;
use App\Models\Product;

class ProductRepository
{
    public static function getProducts($search, $selectedCategory, $minPrice, $maxPrice, $sortBy, $selectedAttributeOptions)
    {
        $productsQuery = Product::query()
            ->with('productCategory')
            ->where('is_active', true);

        if ($search !== '') {
            $productsQuery->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('productCategory', function ($categoryQuery) use ($search) {
                        $categoryQuery->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if (!empty($selectedCategory)) {
            $productsQuery->where('product_category_id', $selectedCategory);
        }

        if (!empty($selectedAttributeOptions)) {
            $productsQuery->whereHas('attributeOptions', function ($query) use ($selectedAttributeOptions) {
                $query->whereIn('product_attribute_option.attribute_option_id', $selectedAttributeOptions);
            });
        }

        if ($minPrice !== null && $minPrice !== '') {
            $productsQuery->where('price', '>=', (float) $minPrice);
        }

        if ($maxPrice !== null && $maxPrice !== '') {
            $productsQuery->where('price', '<=', (float) $maxPrice);
        }

        switch ($sortBy) {
            case 'price_asc':
                $productsQuery->orderBy('price');
                break;
            case 'price_desc':
                $productsQuery->orderByDesc('price');
                break;
            case 'name_asc':
                $productsQuery->orderBy('name');
                break;
            case 'newest':
            default:
                $productsQuery->latest();
                break;
        }

        return $productsQuery->get();
    }
}