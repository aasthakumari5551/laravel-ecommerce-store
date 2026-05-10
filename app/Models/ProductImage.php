<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class ProductImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'path',
        'alt_text',
        'is_primary',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_primary'  => 'boolean',
            'sort_order'  => 'integer',
        ];
    }

    // ── Relationships ─────────────────────────────────────────

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    // ── Accessors ─────────────────────────────────────────────

    public function getUrlAttribute(): string
    {
        return Storage::url($this->path);
    }

    // ── Helpers ───────────────────────────────────────────────

    public function makePrimary(): void
    {
        $this->product->images()->update(['is_primary' => false]);
        $this->update(['is_primary' => true]);
    }
}