<?php

namespace App\Enums;

enum DiscountType: string
{
    case Fixed      = 'fixed';       // ₹X off
    case Percentage = 'percentage';  // X% off
    case FreeShip   = 'free_ship';   // waive shipping

    public function label(): string
    {
        return match($this) {
            self::Fixed      => 'Fixed Amount (₹)',
            self::Percentage => 'Percentage (%)',
            self::FreeShip   => 'Free Shipping',
        };
    }
}