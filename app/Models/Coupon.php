<?php

namespace App\Models;

use App\Enums\DiscountType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Coupon extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'code', 'description', 'discount_type', 'discount_value',
        'minimum_order_amount', 'maximum_discount',
        'usage_limit', 'usage_limit_per_user', 'used_count',
        'is_active', 'starts_at', 'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'discount_type'         => DiscountType::class,
            'discount_value'        => 'decimal:2',
            'minimum_order_amount'  => 'decimal:2',
            'maximum_discount'      => 'decimal:2',
            'is_active'             => 'boolean',
            'starts_at'             => 'datetime',
            'expires_at'            => 'datetime',
        ];
    }

    // ── Boot ─────────────────────────────────────────────────

    protected static function booted(): void
    {
        static::creating(function (Coupon $coupon) {
            $coupon->code = strtoupper($coupon->code);
        });
    }

    // ── Relationships ─────────────────────────────────────────

    public function usages(): HasMany
    {
        return $this->hasMany(CouponUsage::class);
    }

    // ── Scopes ───────────────────────────────────────────────

    public function scopeActive($query): void
    {
        $query->where('is_active', true)
              ->where(fn ($q) => $q->whereNull('starts_at')->orWhere('starts_at', '<=', now()))
              ->where(fn ($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>=', now()));
    }

    // ── Helpers ───────────────────────────────────────────────

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function isExhausted(): bool
    {
        return $this->usage_limit && $this->used_count >= $this->usage_limit;
    }

    public function usagesByUser(int $userId): int
    {
        return $this->usages()->where('user_id', $userId)->count();
    }

    /**
     * Calculate the actual discount amount for a given subtotal.
     * Respects maximum_discount cap for percentage coupons.
     */
    public function calculateDiscount(float $subtotal): float
    {
        $discount = match($this->discount_type) {
            DiscountType::Fixed      => min((float) $this->discount_value, $subtotal),
            DiscountType::Percentage => $subtotal * ((float) $this->discount_value / 100),
            DiscountType::FreeShip   => 0.0, // handled separately in CheckoutService
        };

        // Cap percentage discounts
        if ($this->maximum_discount && $discount > (float) $this->maximum_discount) {
            $discount = (float) $this->maximum_discount;
        }

        return round($discount, 2);
    }
}