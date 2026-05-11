<?php

namespace App\Models;

use App\Enums\ReviewStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Review extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'product_id', 'user_id', 'order_id',
        'rating', 'title', 'body',
        'status', 'rejection_reason', 'is_verified_purchase',
    ];

    protected function casts(): array
    {
        return [
            'rating'               => 'integer',
            'status'               => ReviewStatus::class,
            'is_verified_purchase' => 'boolean',
        ];
    }

    // ── Relationships ─────────────────────────────────────────

    public function product(): BelongsTo { return $this->belongsTo(Product::class); }
    public function user(): BelongsTo    { return $this->belongsTo(User::class); }
    public function order(): BelongsTo   { return $this->belongsTo(Order::class)->withTrashed(); }

    // ── Scopes ───────────────────────────────────────────────

    public function scopeApproved($query): void
    {
        $query->where('status', ReviewStatus::Approved->value);
    }

    public function scopePending($query): void
    {
        $query->where('status', ReviewStatus::Pending->value);
    }
}