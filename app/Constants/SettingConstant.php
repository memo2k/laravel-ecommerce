<?php

namespace App\Constants;

class SettingConstant
{
    const PRODUCT_LOW_STOCK_THRESHOLD = 'product_low_stock_threshold';
    const SHIPPING_AMOUNT = 'shipping_amount';
    const FREE_SHIPPING_MIN_ORDER_AMOUNT = 'free_shipping_min_order_amount';
    const STORE_NAME = 'store_name';
    const STORE_EMAIL = 'store_email';
    const STORE_PHONE = 'store_phone';
    const STORE_ADDRESS = 'store_address';
    const STORE_CITY = 'store_city';
    const STORE_STATE = 'store_state';
    const STORE_ZIP = 'store_zip';
    const STORE_COUNTRY = 'store_country';
    const STORE_CURRENCY = 'store_currency';

    const SETTING_LABELS = [
        self::PRODUCT_LOW_STOCK_THRESHOLD => 'Product Low Stock Threshold',
        self::SHIPPING_AMOUNT => 'Shipping Amount',
        self::FREE_SHIPPING_MIN_ORDER_AMOUNT => 'Free Shipping Min Order Amount',
        self::STORE_NAME => 'Store Name',
        self::STORE_EMAIL => 'Store Email',
        self::STORE_PHONE => 'Store Phone',
        self::STORE_ADDRESS => 'Store Address',
        self::STORE_CITY => 'Store City',
        self::STORE_STATE => 'Store State',
        self::STORE_ZIP => 'Store Zip',
        self::STORE_COUNTRY => 'Store Country',
        self::STORE_CURRENCY => 'Store Currency',
    ];

    const SETTINGS = [
        'Store' => [
            self::STORE_NAME => 'Shop Demo',
            self::STORE_EMAIL => 'info@shopdemo.com',
            self::STORE_PHONE => '+1234567890',
            self::STORE_ADDRESS => '123 Main St, Anytown, USA',
            self::STORE_CITY => 'Anytown',
            self::STORE_STATE => 'CA',
            self::STORE_ZIP => '12345',
            self::STORE_COUNTRY => 'USA',
            self::STORE_CURRENCY => 'USD',
        ],
        'Shipping' => [
            self::SHIPPING_AMOUNT => 5,
            self::FREE_SHIPPING_MIN_ORDER_AMOUNT => 100,
        ],
        'Product' => [
            self::PRODUCT_LOW_STOCK_THRESHOLD => 10,
        ],
    ];
}