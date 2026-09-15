<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Category;
use App\Models\CompanySetting;
use App\Models\Product;
use App\Services\QuoteService;
use App\Services\WhatsAppService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatalogAndQuoteTest extends TestCase
{
    use RefreshDatabase;

    protected Product $product;

    protected Category $category;

    protected Brand $brand;

    protected function setUp(): void
    {
        parent::setUp();

        CompanySetting::current();

        $this->category = Category::create([
            'name' => 'Laptops',
            'slug' => 'laptops',
            'icon' => 'laptop',
            'is_active' => true,
        ]);

        $this->brand = Brand::create([
            'name' => 'Lenovo',
            'slug' => 'lenovo',
            'is_active' => true,
        ]);

        $this->product = Product::create([
            'category_id' => $this->category->id,
            'brand_id' => $this->brand->id,
            'name' => 'Laptop Lenovo IdeaPad 3',
            'slug' => 'laptop-lenovo-ideapad-3',
            'sku' => 'LAP-LEN-001',
            'price' => 2499.00,
            'stock' => 12,
            'min_stock' => 2,
            'is_active' => true,
            'is_featured' => true,
        ]);
    }

    public function test_homepage_loads_successfully(): void
    {
        $response = $this->get(route('home'));

        $response->assertStatus(200);
        $response->assertSee('NEXORA');
        $response->assertSee('Laptop Lenovo IdeaPad 3');
    }

    public function test_catalog_filters_by_category_and_search(): void
    {
        $response = $this->get(route('catalog', ['categoria' => 'laptops']));
        $response->assertStatus(200);
        $response->assertSee('Laptop Lenovo IdeaPad 3');

        $searchResponse = $this->get(route('catalog', ['q' => 'IdeaPad']));
        $searchResponse->assertStatus(200);
        $searchResponse->assertSee('LAP-LEN-001');
    }

    public function test_product_detail_page_loads_with_details_and_whatsapp(): void
    {
        $response = $this->get(route('product.detail', $this->product->slug));

        $response->assertStatus(200);
        $response->assertSee($this->product->name);
        $response->assertSee('Comprar por WhatsApp');
        $response->assertSee('Agregar al Carrito');
    }

    public function test_quote_creation_generates_correct_cot_number(): void
    {
        $quoteService = app(QuoteService::class);

        $data = [
            'customer_name' => 'Ing. Roberto Silva',
            'customer_email' => 'rsilva@empresa.com',
            'customer_phone' => '+51 987 111 222',
            'company_name' => 'Silva Asociados S.A.C.',
            'company_ruc' => '20601234567',
            'message' => 'Cotización de 3 laptops para oficina',
        ];

        $items = [
            [
                'product_id' => $this->product->id,
                'product_name' => $this->product->name,
                'product_sku' => $this->product->sku,
                'quantity' => 3,
                'unit_price' => 2499.00,
            ],
        ];

        $quote = $quoteService->createQuote($data, $items);

        $this->assertEquals('COT-000001', $quote->quote_number);
        $this->assertEquals('pending', $quote->status);
        $this->assertEquals(7497.00, $quote->total);
        $this->assertCount(1, $quote->items);
    }

    public function test_whatsapp_service_generates_proper_peru_phone_link(): void
    {
        $whatsAppService = app(WhatsAppService::class);
        $link = $whatsAppService->productInquiryLink($this->product);

        $this->assertStringContainsString('https://wa.me/51987654321', $link);
        $this->assertStringContainsString('LAP-LEN-001', urldecode($link));
    }
}
