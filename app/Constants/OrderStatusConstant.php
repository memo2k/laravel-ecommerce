<?php

namespace App\Constants;

class OrderStatusConstant
{
    const PENDING = 'Pending';
    const PROCESSING = 'Processing';
    const SHIPPED = 'Shipped';
    const DELIVERED = 'Delivered';
    const CANCELLED = 'Cancelled';

    const ORDER_STATUSES = [
        self::PENDING,
        self::PROCESSING,
        self::SHIPPED,
        self::DELIVERED,
        self::CANCELLED,
    ];
}