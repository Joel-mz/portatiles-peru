<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'customer_name',
        'customer_email',
        'customer_phone',
        'customer_document_type',
        'customer_document_number',
        'department',
        'province',
        'district',
        'address',
        'reference',
        'shipping_type',
        'notes',
        'subtotal',
        'discount',
        'igv',
        'total',
        'payment_method',
        'payment_status',
        'order_status',
        'payment_proof',
        'proof_uploaded_at',
        'paid_at',
        'verified_by_user_id',
        'admin_notes',
        'rejection_reason',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'igv' => 'decimal:2',
        'total' => 'decimal:2',
        'proof_uploaded_at' => 'datetime',
        'paid_at' => 'datetime',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by_user_id');
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function isPaid(): bool
    {
        return $this->payment_status === 'paid';
    }

    public function isPendingReview(): bool
    {
        return $this->payment_status === 'pending_review';
    }

    public function statusBadge(): array
    {
        return match ($this->payment_status) {
            'paid' => [
                'label' => 'Pago Confirmado',
                'bg' => 'bg-emerald-500/10 text-emerald-500 border border-emerald-500/30',
                'icon' => 'check-circle',
            ],
            'pending_review' => [
                'label' => 'En Revisión (Comprobante Subido)',
                'bg' => 'bg-amber-500/10 text-amber-500 border border-amber-500/30',
                'icon' => 'clock',
            ],
            'rejected' => [
                'label' => 'Rechazado',
                'bg' => 'bg-rose-500/10 text-rose-500 border border-rose-500/30',
                'icon' => 'x-circle',
            ],
            'cancelled' => [
                'label' => 'Cancelado',
                'bg' => 'bg-slate-500/10 text-slate-400 border border-slate-500/30',
                'icon' => 'slash',
            ],
            'refunded' => [
                'label' => 'Reembolsado',
                'bg' => 'bg-purple-500/10 text-purple-400 border border-purple-500/30',
                'icon' => 'refresh-cw',
            ],
            default => [
                'label' => 'Pendiente de Pago',
                'bg' => 'bg-cyan-500/10 text-cyan-400 border border-cyan-500/30',
                'icon' => 'credit-card',
            ],
        };
    }
}
