<?php

namespace App\Constants;

class PaymentMethodConstant
{
    const CASH_ON_DELIVERY = 'cash_on_delivery';

    const CARD = 'card';

    const PAYMENT_METHODS = [
        self::CASH_ON_DELIVERY => 'Cash on delivery',
        self::CARD => 'Card',
    ];
}
