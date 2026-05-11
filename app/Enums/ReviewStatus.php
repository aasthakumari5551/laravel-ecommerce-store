<?php

namespace App\Enums;

enum ReviewStatus: string
{
    case Pending  = 'pending';   // default — awaits moderation
    case Approved = 'approved';  // visible publicly
    case Rejected = 'rejected';  // hidden, notified

    public function label(): string
    {
        return match($this) {
            self::Pending  => 'Pending',
            self::Approved => 'Approved',
            self::Rejected => 'Rejected',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::Pending  => 'yellow',
            self::Approved => 'green',
            self::Rejected => 'red',
        };
    }
}