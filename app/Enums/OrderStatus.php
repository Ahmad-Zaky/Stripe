<?php

namespace App\Enums;

class OrderStatus extends Enum
{
    const UNPAID = "unpaid";
    const PAID = "paid";
    const CANCELED = "canceled";
    const REFUNDED = "refunded";
    const PENDING = "pending";
}