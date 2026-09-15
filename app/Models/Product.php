<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'subcategory_id',
        'brand_id',
        'name',
        'slug',
        'sku',
        'model',
        'short_description',
        'description',
        'technical_specs',
        'price',
        'original_price',
        'offer_price',
        'min_price',
        'stock',
        'min_stock',
        'main_image',
        'is_featured',
        'is_offer',
        'is_new',
        'is_active',
        'rating',
        'reviews_count',
        'views_count',
    ];

    protected $casts = [
        'technical_specs' => 'array',
        'price' => 'decimal:2',
        'original_price' => 'decimal:2',
        'offer_price' => 'decimal:2',
        'min_price' => 'decimal:2',
        'stock' => 'integer',
        'min_stock' => 'integer',
        'is_featured' => 'boolean',
        'is_offer' => 'boolean',
        'is_new' => 'boolean',
        'is_active' => 'boolean',
        'rating' => 'decimal:2',
        'reviews_count' => 'integer',
        'views_count' => 'integer',
    ];

    protected $appends = [
        'effective_price',
        'discount_percentage',
        'is_low_stock',
        'is_in_stock',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function subcategory(): BelongsTo
    {
        return $this->belongsTo(Subcategory::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class)->latest();
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    protected function effectivePrice(): Attribute
    {
        return Attribute::make(
            get: fn () => ($this->is_offer && $this->offer_price && $this->offer_price > 0)
                ? (float) $this->offer_price
                : (float) $this->price,
        );
    }

    protected function discountPercentage(): Attribute
    {
        return Attribute::make(
            get: function () {
                $orig = (float) ($this->original_price ?? 0);
                $eff = (float) $this->effective_price;
                if ($orig > $eff && $orig > 0) {
                    return (int) round((($orig - $eff) / $orig) * 100);
                }

                return 0;
            },
        );
    }

    protected function isInStock(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->stock > 0,
        );
    }

    protected function isLowStock(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->stock > 0 && $this->stock <= $this->min_stock,
        );
    }

    public function formattedPrice(): string
    {
        return 'S/ '.number_format($this->effective_price, 2);
    }

    public function formattedOriginalPrice(): ?string
    {
        return $this->original_price ? 'S/ '.number_format($this->original_price, 2) : null;
    }
}
