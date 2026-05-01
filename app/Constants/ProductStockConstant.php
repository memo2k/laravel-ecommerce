<?php

namespace App\Constants;

class ProductStockConstant
{
    const IN_STOCK = 'in_stock';
    const LOW_STOCK = 'low_stock';
    const OUT_OF_STOCK = 'out_of_stock';

    const PRODUCT_STOCK_STATES = [
        self::IN_STOCK,
        self::LOW_STOCK,
        self::OUT_OF_STOCK,
    ];

    const PRODUCT_STOCK_STATE_LABELS = [
        self::IN_STOCK => 'In Stock',
        self::LOW_STOCK => 'Low Stock',
        self::OUT_OF_STOCK => 'Out of Stock',
    ];
}