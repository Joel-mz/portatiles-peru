<?php

namespace App\Services;

use App\Models\CompanySetting;
use App\Models\Order;
use App\Models\Product;
use App\Models\Quote;

class WhatsAppService
{
    protected CompanySetting $settings;

    public function __construct()
    {
        $this->settings = CompanySetting::current();
    }

    /**
     * Clean phone number for WhatsApp link
     */
    public function getCleanPhone(?string $phone = null): string
    {
        $raw = $phone ?: $this->settings->whatsapp_number;
        $digits = preg_replace('/\D/', '', $raw);

        // Add 51 prefix if missing for Peru
        if (strlen($digits) === 9 && str_starts_with($digits, '9')) {
            $digits = '51'.$digits;
        }

        return $digits;
    }

    /**
     * Generate link for a single product inquiry
     */
    public function productInquiryLink(Product $product): string
    {
        $storeName = $this->settings->name;
        $price = number_format($product->effective_price, 2);
        $url = url('/producto/'.$product->slug);

        $text = "¡Hola {$storeName}!\n\n"
            ."Estoy interesado en el siguiente producto de su catálogo:\n"
            ."📌 *{$product->name}*\n"
            ."• SKU: `{$product->sku}`\n"
            ."• Precio: *S/ {$price}*\n"
            ."• Enlace: {$url}\n\n"
            .'¿Tienen disponibilidad y stock para entrega inmediata?';

        return $this->buildUrl($text);
    }

    /**
     * Generate link for cart order inquiry
     */
    public function cartOrderLink(array $cartItems, float $subtotal, float $total): string
    {
        $storeName = $this->settings->name;

        $text = "¡Hola {$storeName}!\n\n"
            ."Deseo realizar la siguiente consulta/pedido desde su tienda web:\n\n";

        foreach ($cartItems as $item) {
            $itemSub = number_format($item['price'] * $item['quantity'], 2);
            $text .= "• {$item['quantity']}x {$item['name']} (SKU: {$item['sku']}) - S/ {$itemSub}\n";
        }

        $text .= "\n*Total a Pagar:* S/ ".number_format($total, 2)."\n\n"
            .'¿Podrían confirmarme la disponibilidad y los pasos para el pago/envío? ¡Gracias!';

        return $this->buildUrl($text);
    }

    /**
     * Generate link for an existing order with payment reference
     */
    public function orderPaymentNoticeLink(Order $order): string
    {
        $storeName = $this->settings->name;
        $total = number_format($order->total, 2);
        $method = strtoupper($order->payment_method);

        $text = "¡Hola {$storeName}!\n\n"
            ."Acabo de registrar el pedido *#{$order->order_number}* en su catálogo web.\n\n"
            ."👤 *Cliente:* {$order->customer_name}\n"
            ."📱 *Teléfono:* {$order->customer_phone}\n"
            ."💳 *Método de Pago:* {$method}\n"
            ."💰 *Monto Total:* S/ {$total}\n"
            ."📍 *Destino:* {$order->department}, {$order->address}\n\n"
            .'Adjunto en este chat mi comprobante para que puedan verificarlo y programar el envío. ¡Muchas gracias!';

        return $this->buildUrl($text);
    }

    /**
     * Generate link for a formal quote
     */
    public function quoteInquiryLink(Quote $quote): string
    {
        $storeName = $this->settings->name;
        $total = number_format($quote->total, 2);

        $text = "¡Hola {$storeName}!\n\n"
            ."He generado la solicitud de cotización formal *#{$quote->quote_number}*.\n\n"
            ."👤 *Solicitante:* {$quote->customer_name}\n"
            .($quote->company_name ? "🏢 *Empresa:* {$quote->company_name} (RUC: {$quote->company_ruc})\n" : '')
            ."💰 *Monto Estimado:* S/ {$total}\n\n"
            .'¿Podrían brindarme mayor información técnica y condiciones de pago comercial?';

        return $this->buildUrl($text);
    }

    /**
     * General contact floating button
     */
    public function generalContactLink(): string
    {
        $text = $this->settings->whatsapp_default_message
            ?: "¡Hola {$this->settings->name}! Deseo consultar sobre productos tecnológicos, ofertas o cotizaciones empresariales.";

        return $this->buildUrl($text);
    }

    protected function buildUrl(string $text): string
    {
        $phone = $this->getCleanPhone();

        return 'https://wa.me/'.$phone.'?text='.urlencode($text);
    }
}
