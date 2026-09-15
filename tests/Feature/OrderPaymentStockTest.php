<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\User;
use App\Services\InventoryService;
use App\Services\OrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class OrderPaymentStockTest extends TestCase
{
    use RefreshDatabase;

    protected Product $product;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $category = Category::create([
            'name' => 'Laptops',
            'slug' => 'laptops',
            'icon' => 'laptop',
            'is_active' => true,
        ]);

        $brand = Brand::create([
            'name' => 'Lenovo',
            'slug' => 'lenovo',
            'is_active' => true,
        ]);

        $this->product = Product::create([
            'category_id' => $category->id,
            'brand_id' => $brand->id,
            'name' => 'Laptop Lenovo IdeaPad 3',
            'slug' => 'laptop-lenovo-ideapad-3',
            'sku' => 'LAP-LEN-001',
            'price' => 2499.00,
            'stock' => 10,
            'min_stock' => 2,
            'is_active' => true,
        ]);

        $this->admin = User::create([
            'name' => 'Admin Test',
            'email' => 'admin@nexora.com',
            'password' => bcrypt('secret123'),
            'role' => 'superadmin',
        ]);
    }

    public function test_order_creation_with_payment_proof_does_not_deduct_stock_and_marks_pending_review(): void
    {
        Storage::fake('public');

        $orderService = app(OrderService::class);
        $initialStock = $this->product->stock; // 10

        $data = [
            'customer_name' => 'Carlos Quispe',
            'customer_email' => 'carlos@gmail.com',
            'customer_phone' => '+51 987 654 321',
            'customer_document_type' => 'DNI',
            'customer_document_number' => '44556677',
            'department' => 'San Martín',
            'address' => 'Jr. Comercio 123',
            'shipping_type' => 'delivery',
            'payment_method' => 'yape',
        ];

        $cartItems = [
            [
                'id' => $this->product->id,
                'name' => $this->product->name,
                'sku' => $this->product->sku,
                'price' => 2499.00,
                'quantity' => 2,
            ],
        ];

        $proofFile = UploadedFile::fake()->create('yape_capture.jpg', 100, 'image/jpeg');

        $order = $orderService->createOrder($data, $cartItems, $proofFile);

        // 1. Order status must be pending_review (NOT paid!)
        $this->assertEquals('pending_review', $order->payment_status);
        $this->assertNotNull($order->payment_proof);

        // 2. CRITICAL RULE: Stock must NOT have changed!
        $this->product->refresh();
        $this->assertEquals($initialStock, $this->product->stock);

        // 3. No stock movements should have been logged yet
        $this->assertEquals(0, StockMovement::count());
    }

    public function test_admin_confirming_payment_deducts_stock_and_logs_kardex(): void
    {
        $inventoryService = app(InventoryService::class);

        // Create an order in pending_review
        $order = Order::create([
            'order_number' => 'PED-TEST-001',
            'customer_name' => 'María Pérez',
            'customer_email' => 'maria@gmail.com',
            'customer_phone' => '+51 981 111 222',
            'department' => 'San Martín',
            'address' => 'Av. Grau 400',
            'shipping_type' => 'delivery',
            'subtotal' => 4998.00,
            'total' => 4998.00,
            'payment_method' => 'plin',
            'payment_status' => 'pending_review',
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $this->product->id,
            'product_name' => $this->product->name,
            'product_sku' => $this->product->sku,
            'unit_price' => 2499.00,
            'quantity' => 3, // Ordered 3
            'subtotal' => 7497.00,
        ]);

        $this->assertEquals(10, $this->product->stock);

        // Act: Admin confirms payment
        $inventoryService->confirmOrderPayment($order, $this->admin);

        // Assert:
        $order->refresh();
        $this->assertEquals('paid', $order->payment_status);
        $this->assertEquals('processing', $order->order_status);
        $this->assertNotNull($order->paid_at);
        $this->assertEquals($this->admin->id, $order->verified_by_user_id);

        // Stock must have decreased from 10 to 7
        $this->product->refresh();
        $this->assertEquals(7, $this->product->stock);

        // Kardex movement must be recorded
        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $this->product->id,
            'order_id' => $order->id,
            'type' => 'sale',
            'quantity' => -3,
            'stock_before' => 10,
            'stock_after' => 7,
            'user_id' => $this->admin->id,
        ]);
    }

    public function test_payment_confirmation_is_idempotent_and_does_not_double_deduct(): void
    {
        $inventoryService = app(InventoryService::class);

        $order = Order::create([
            'order_number' => 'PED-IDEMPOTENT-001',
            'customer_name' => 'Jorge Ramos',
            'customer_email' => 'jorge@gmail.com',
            'customer_phone' => '+51 982 333 444',
            'department' => 'Lima',
            'address' => 'Miraflores 123',
            'subtotal' => 2499.00,
            'total' => 2499.00,
            'payment_method' => 'bcp',
            'payment_status' => 'pending_review',
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $this->product->id,
            'product_name' => $this->product->name,
            'product_sku' => $this->product->sku,
            'unit_price' => 2499.00,
            'quantity' => 2,
            'subtotal' => 4998.00,
        ]);

        // First confirmation
        $inventoryService->confirmOrderPayment($order, $this->admin);
        $this->product->refresh();
        $this->assertEquals(8, $this->product->stock);
        $this->assertEquals(1, StockMovement::count());

        // Second confirmation (simulating repeated webhook or admin click)
        $inventoryService->confirmOrderPayment($order, $this->admin);
        $this->product->refresh();

        // Stock must STILL be 8, NOT 6!
        $this->assertEquals(8, $this->product->stock);
        // Movements must STILL be 1, NOT 2!
        $this->assertEquals(1, StockMovement::count());
    }

    public function test_rejecting_payment_leaves_stock_untouched(): void
    {
        $inventoryService = app(InventoryService::class);

        $order = Order::create([
            'order_number' => 'PED-REJECT-001',
            'customer_name' => 'Pedro Castillo',
            'customer_email' => 'pedro@gmail.com',
            'customer_phone' => '+51 983 555 666',
            'department' => 'Cusco',
            'address' => 'Plaza Regocijo',
            'subtotal' => 2499.00,
            'total' => 2499.00,
            'payment_method' => 'yape',
            'payment_status' => 'pending_review',
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $this->product->id,
            'product_name' => $this->product->name,
            'product_sku' => $this->product->sku,
            'unit_price' => 2499.00,
            'quantity' => 1,
            'subtotal' => 2499.00,
        ]);

        // Act: Admin rejects payment
        $inventoryService->rejectOrderPayment($order, 'Captura ilegible y sin abono en cuenta', $this->admin);

        // Assert:
        $order->refresh();
        $this->assertEquals('rejected', $order->payment_status);
        $this->assertEquals('Captura ilegible y sin abono en cuenta', $order->rejection_reason);

        // Stock must NOT have changed
        $this->product->refresh();
        $this->assertEquals(10, $this->product->stock);
        $this->assertEquals(0, StockMovement::count());
    }
}
