<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Quote extends Model
{
    use HasFactory;

    protected $fillable = [
        'quote_number',
        'customer_name',
        'customer_email',
        'customer_phone',
        'company_name',
        'company_ruc',
        'message',
        'status',
        'subtotal',
        'total',
        'valid_until',
        'admin_notes',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'total' => 'decimal:2',
        'valid_until' => 'date',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(QuoteItem::class);
    }

    public function statusBadge(): array
    {
        return match ($this->status) {
            'accepted' => [
                'label' => 'Aceptada',
                'bg' => 'bg-emerald-500/10 text-emerald-500 border border-emerald-500/30',
            ],
            'sent' => [
                'label' => 'Enviada al Cliente',
                'bg' => 'bg-cyan-500/10 text-cyan-400 border border-cyan-500/30',
            ],
            'reviewing' => [
                'label' => 'En Revisión Comercial',
                'bg' => 'bg-blue-500/10 text-blue-400 border border-blue-500/30',
            ],
            'rejected' => [
                'label' => 'Rechazada',
                'bg' => 'bg-rose-500/10 text-rose-500 border border-rose-500/30',
            ],
            'expired' => [
                'label' => 'Vencida',
                'bg' => 'bg-slate-500/10 text-slate-400 border border-slate-500/30',
            ],
            default => [
                'label' => 'Pendiente',
                'bg' => 'bg-amber-500/10 text-amber-400 border border-amber-500/30',
            ],
        };
    }
}
