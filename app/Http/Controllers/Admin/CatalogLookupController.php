<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Combo;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Subcategory;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CatalogLookupController extends Controller
{
    public function __invoke(string $section): View
    {
        $configuration = match ($section) {
            'categories' => ['Categorías', 'Organiza los productos de la tienda.', Category::withCount('products')->orderBy('sort_order')->get(), 'name'],
            'subcategories' => ['Subcategorías', 'Consulta la estructura detallada del catálogo.', Subcategory::with('category')->withCount('products')->orderBy('name')->get(), 'name'],
            'brands' => ['Marcas', 'Marcas disponibles en el catálogo.', Brand::withCount('products')->orderBy('name')->get(), 'name'],
            'combos' => ['Combos', 'Paquetes y ofertas combinadas publicados.', Combo::withCount('items')->latest()->get(), 'name'],
            'banners' => ['Banners / Slider', 'Contenido visual mostrado en la página principal.', Banner::orderBy('sort_order')->get(), 'title'],
            'coupons' => ['Ofertas / Promociones', 'Cupones registrados para las compras.', Coupon::latest()->get(), 'code'],
            'clients' => ['Clientes', 'Clientes que realizaron pedidos en la tienda.', Order::select('customer_name', 'customer_email', 'customer_phone')->selectRaw('count(*) as total_orders')->groupBy('customer_name', 'customer_email', 'customer_phone')->orderByDesc('total_orders')->get(), 'customer_name'],
            'users' => ['Usuarios', 'Personal con acceso al panel administrativo.', User::orderBy('name')->get(), 'name'],
            'roles' => ['Roles y permisos', 'Roles actualmente asignados al personal.', User::select('role')->selectRaw('count(*) as total')->groupBy('role')->get(), 'role'],
        };

        [$title, $description, $records, $label] = $configuration;

        return view('admin.catalog-lookup', compact('title', 'description', 'records', 'label', 'section'));
    }

    public function store(Request $request, string $section): RedirectResponse
    {
        $rules = match ($section) {
            'categories' => ['name' => ['required', 'string', 'max:100'], 'description' => ['nullable', 'string']],
            'subcategories' => ['name' => ['required', 'string', 'max:100'], 'category_id' => ['required', 'exists:categories,id']],
            'brands' => ['name' => ['required', 'string', 'max:100']],
            'combos' => ['name' => ['required', 'string', 'max:150'], 'price' => ['required', 'numeric', 'min:0']],
            'banners' => ['title' => ['required', 'string', 'max:150'], 'image' => ['required', 'url'], 'link_url' => ['nullable', 'url']],
            'coupons' => ['code' => ['required', 'string', 'max:50', 'unique:coupons,code'], 'type' => ['required', 'in:percentage,fixed'], 'value' => ['required', 'numeric', 'min:0']],
            'users' => ['name' => ['required', 'string', 'max:100'], 'email' => ['required', 'email', 'unique:users,email'], 'password' => ['required', 'string', 'min:8'], 'role' => ['required', 'in:superadmin,admin,editor,seller']],
            default => abort(404),
        };
        $data = $request->validate($rules);
        match ($section) {
            'categories' => Category::create([...$data, 'slug' => Str::slug($data['name']), 'is_active' => true]),
            'subcategories' => Subcategory::create([...$data, 'slug' => Str::slug($data['name']), 'is_active' => true]),
            'brands' => Brand::create([...$data, 'slug' => Str::slug($data['name']), 'is_active' => true]),
            'combos' => Combo::create([...$data, 'slug' => Str::slug($data['name']), 'is_active' => true]),
            'banners' => Banner::create([...$data, 'type' => 'hero', 'is_active' => true, 'sort_order' => Banner::max('sort_order') + 1]),
            'coupons' => Coupon::create([...$data, 'is_active' => true]),
            'users' => User::create([...$data, 'password' => Hash::make($data['password'])]),
        };

        return back()->with('success', 'Registro creado correctamente.');
    }

    public function destroy(string $section, int $id): RedirectResponse
    {
        $model = match ($section) {
            'categories' => Category::class, 'subcategories' => Subcategory::class, 'brands' => Brand::class,
            'combos' => Combo::class, 'banners' => Banner::class, 'coupons' => Coupon::class, 'users' => User::class,
            default => abort(404),
        };
        $model::findOrFail($id)->delete();

        return back()->with('success', 'Registro eliminado correctamente.');
    }
}
