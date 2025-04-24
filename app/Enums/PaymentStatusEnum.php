<?php

namespace App\Enums;

enum PaymentStatusEnum: string
{
    case Pending = 'pending';
    case Paid = 'paid';
    case Failed = 'failed';
    case Cancelled = 'cancelled';
    case Refunded = 'refunded';
    case Expired = 'expired';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::Paid => 'Paid',
            self::Failed => 'Failed',
            self::Cancelled => 'Cancelled',
            self::Refunded => 'Refunded',
            self::Expired => 'Expired',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Pending   => 'warning',   // yellow
            self::Paid      => 'success',   // green
            self::Failed    => 'danger',    // red
            self::Cancelled => 'secondary', // gray
            self::Refunded  => 'info',      // light blue
            self::Expired   => 'dark',      // dark gray
        };
    }

    public function badge(): string
    {
        return sprintf('<span class="badge bg-%s">%s</span>', $this->color(), $this->label());
    }
}
