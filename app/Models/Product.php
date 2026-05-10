<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'category_id',
        'name',
        'slug',
        'sku',
        'short_description',
        'description',
        'price',
        'compare_price',
        'cost_price',
        'stock',
        'low_stock_threshold',
        'track_inventory',
        'is_active',
        'is_featured',
        'meta_title',
        'meta_description',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'price'               => 'decimal:2',
            'compare_price'       => 'decimal:2',
            'cost_price'          => 'decimal:2',
            'stock'               => 'integer',
            'low_stock_threshold' => 'integer',
            'track_inventory'     => 'boolean',
            'is_active'           => 'boolean',
            'is_featured'         => 'boolean',
            'sort_order'          => 'integer',
        ];
    }

    // ── Boot ─────────────────────────────────────────────────

    protected static function booted(): void
    {
        static::creating(function (Product $product) {
            if (empty($product->uuid)) {
                $product->uuid = (string) Str::uuid();
            }
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }
        });
    }

    // ── Relationships ─────────────────────────────────────────

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function primaryImage(): HasOne
    {
        return $this->hasOne(ProductImage::class)->where('is_primary', true);
    }

    // ── Scopes ───────────────────────────────────────────────

    public function scopeActive($query): void
    {
        $query->where('is_active', true);
    }

    public function scopeFeatured($query): void
    {
        $query->where('is_featured', true);
    }

    public function scopeInStock($query): void
    {
        $query->where('stock', '>', 0);
    }

    public function scopeLowStock($query): void
    {
        $query->whereColumn('stock', '<=', 'low_stock_threshold')
              ->where('stock', '>', 0);
    }

    public function scopeOrdered($query): void
    {
        $query->orderBy('sort_order')->orderBy('name');
    }

    // ── Helpers ───────────────────────────────────────────────

    public function isOnSale(): bool
    {
        return ! is_null($this->compare_price)
            && $this->compare_price > $this->price;
    }

    public function discountPercentage(): int
    {
        if (! $this->isOnSale()) {
            return 0;
        }

        return (int) round(
            (($this->compare_price - $this->price) / $this->compare_price) * 100
        );
    }

    public function isLowStock(): bool
    {
        return $this->track_inventory
            && $this->stock > 0
            && $this->stock <= $this->low_stock_threshold;
    }

    public function isOutOfStock(): bool
    {
        return $this->track_inventory && $this->stock <= 0;
    }

    public function getRouteKeyName(): string
    {
        return 'uuid'; // URLs use /products/{uuid} — never expose internal IDs
    }
}