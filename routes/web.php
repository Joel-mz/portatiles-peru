<?php

use App\Http\Controllers\Admin\BackupController;
use App\Http\Controllers\Admin\CatalogLookupController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\InventoryController as AdminInventoryController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\QuoteController as AdminQuoteController;
use App\Http\Controllers\Admin\SettingController as AdminSettingController;
use App\Http\Controllers\AdminLoginController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\CustomerAuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\QuoteController;
use App\Http\Middleware\EnsureAdminUser;
use App\Http\Middleware\EnsureSuperAdmin;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Storefront Routes
|--------------------------------------------------------------------------
*/

Route::get('/', HomeController::class)->name('home');

Route::get('/catalogo', [CatalogController::class, 'index'])->name('catalog');
Route::get('/catalogo/pdf', [CatalogController::class, 'pdf'])->name('catalog.pdf');
Route::get('/producto/{slug}', [CatalogController::class, 'show'])->name('product.detail');

Route::get('/cotizacion', [QuoteController::class, 'create'])->name('quotes.create');
Route::post('/cotizacion', [QuoteController::class, 'store'])->name('quotes.store');
Route::get('/cotizacion/{quoteNumber}', [QuoteController::class, 'show'])->name('quotes.show');

Route::get('/contacto', [ContactController::class, 'index'])->name('contact');
Route::post('/contacto', [ContactController::class, 'submit'])->name('contact.submit');
Route::middleware('guest')->group(function () {
    Route::get('/ingresar', [CustomerAuthController::class, 'login'])->name('customer.login');
    Route::post('/ingresar', [CustomerAuthController::class, 'authenticate'])->name('customer.login.store');
    Route::get('/registro', [CustomerAuthController::class, 'register'])->name('customer.register');
    Route::post('/registro', [CustomerAuthController::class, 'store'])->name('customer.register.store');
});
Route::middleware('auth')->group(function () {
    Route::get('/mi-cuenta/pedidos', [CustomerAuthController::class, 'orders'])->name('account.orders');
    Route::post('/salir', [CustomerAuthController::class, 'logout'])->name('customer.logout');
});

/*
|--------------------------------------------------------------------------
| Admin Panel Routes (SaaS)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/admin/login', [AdminLoginController::class, 'create'])->name('admin.login');
    Route::post('/admin/login', [AdminLoginController::class, 'store'])->middleware('throttle:5,1')->name('admin.login.store');
});

Route::post('/admin/logout', [AdminLoginController::class, 'destroy'])->middleware('auth')->name('admin.logout');

Route::prefix('admin')->name('admin.')->middleware(['auth', EnsureAdminUser::class])->group(function () {
    Route::get('/', AdminDashboardController::class)->name('dashboard');

    // Products Management
    Route::get('/productos', [AdminProductController::class, 'index'])->name('products.index');
    Route::get('/productos/crear', [AdminProductController::class, 'create'])->name('products.create');
    Route::post('/productos', [AdminProductController::class, 'store'])->name('products.store');
    Route::get('/productos/{product}/editar', [AdminProductController::class, 'edit'])->name('products.edit');
    Route::put('/productos/{product}', [AdminProductController::class, 'update'])->name('products.update');
    Route::delete('/productos/{product}', [AdminProductController::class, 'destroy'])->name('products.destroy');
    Route::post('/productos/importar-csv', [AdminProductController::class, 'importCsv'])->name('products.importCsv');
    Route::get('/productos/plantilla-csv', [AdminProductController::class, 'downloadCsvTemplate'])->name('products.downloadCsvTemplate');
    Route::get('/catalogo/{section}', CatalogLookupController::class)
        ->whereIn('section', ['categories', 'subcategories', 'brands', 'combos', 'banners', 'coupons', 'clients', 'users', 'roles'])
        ->name('catalog.lookup');
    Route::post('/catalogo/{section}', [CatalogLookupController::class, 'store'])->name('catalog.store');
    Route::delete('/catalogo/{section}/{id}', [CatalogLookupController::class, 'destroy'])->name('catalog.destroy');

    // Orders management
    Route::get('/pedidos', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('/pedidos/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::post('/pedidos/{order}/confirmar-pago', [AdminOrderController::class, 'confirmPayment'])->name('orders.confirmPayment');
    Route::post('/pedidos/{order}/rechazar-pago', [AdminOrderController::class, 'rejectPayment'])->name('orders.rejectPayment');
    Route::post('/pedidos/{order}/estado', [AdminOrderController::class, 'updateOrderStatus'])->name('orders.updateOrderStatus');

    // Quotes Management
    Route::get('/cotizaciones', [AdminQuoteController::class, 'index'])->name('quotes.index');
    Route::get('/cotizaciones/{quote}', [AdminQuoteController::class, 'show'])->name('quotes.show');
    Route::post('/cotizaciones/{quote}/estado', [AdminQuoteController::class, 'updateStatus'])->name('quotes.updateStatus');

    // Inventory & Kardex
    Route::get('/inventario', [AdminInventoryController::class, 'index'])->name('inventory.index');
    Route::post('/inventario/ajuste', [AdminInventoryController::class, 'adjust'])->name('inventory.adjust');

    // Settings
    Route::get('/configuracion', [AdminSettingController::class, 'index'])->name('settings.index');
    Route::put('/configuracion', [AdminSettingController::class, 'update'])->name('settings.update');
    Route::middleware(EnsureSuperAdmin::class)->group(function () {
        Route::get('/respaldos', [BackupController::class, 'index'])->name('backups.index');
        Route::get('/respaldos/descargar', [BackupController::class, 'download'])->name('backups.download');
        Route::post('/respaldos/restaurar', [BackupController::class, 'restore'])->name('backups.restore');
    });
});
