<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanySetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'tagline',
        'legal_name',
        'ruc',
        'email',
        'phone',
        'whatsapp_number',
        'whatsapp_default_message',
        'address',
        'schedule_weekdays',
        'schedule_weekends',
        'yape_number',
        'yape_holder',
        'yape_qr',
        'plin_number',
        'plin_holder',
        'plin_qr',
        'bank_name',
        'bank_account',
        'bank_cci',
        'bank_holder',
        'logo',
        'favicon',
        'facebook_url',
        'instagram_url',
        'tiktok_url',
        'youtube_url',
        'enable_whatsapp_button',
        'enable_dark_mode',
    ];

    protected $casts = [
        'enable_whatsapp_button' => 'boolean',
        'enable_dark_mode' => 'boolean',
    ];

    public static function current(): self
    {
        return static::firstOrCreate([], [
            'name' => 'NEXORA',
            'tagline' => 'Tecnología sin límites',
            'legal_name' => 'NEXORA TECHNOLOGY S.A.C.',
            'ruc' => '20681254567',
            'email' => 'ventas@nexora.com',
            'phone' => '+51 987 654 321',
            'whatsapp_number' => '+51987654321',
            'whatsapp_default_message' => '¡Hola! Quisiera información sobre sus productos de tecnología.',
            'address' => 'Av. Los Héroes 123, Moyobamba, San Martín, Perú',
            'schedule_weekdays' => 'Lun - Sáb: 8:00 a.m. - 7:00 p.m.',
            'schedule_weekends' => 'Dom: 9:00 a.m. - 2:00 p.m.',
            'yape_number' => '987 654 321',
            'yape_holder' => 'NEXORA TECHNOLOGY S.A.C.',
            'plin_number' => '987 654 321',
            'plin_holder' => 'NEXORA TECHNOLOGY S.A.C.',
            'bank_name' => 'Banco de Crédito del Perú (BCP)',
            'bank_account' => '191-12345678-0-72',
            'bank_cci' => '002-191-0012345678072-55',
            'bank_holder' => 'NEXORA TECHNOLOGY S.A.C.',
        ]);
    }
}
