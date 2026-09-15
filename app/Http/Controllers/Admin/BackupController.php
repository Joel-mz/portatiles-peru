<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BackupController extends Controller
{
    /** @var list<string> */
    private const TABLES = ['users', 'brands', 'categories', 'subcategories', 'products', 'product_images', 'combos', 'combo_items', 'coupons', 'company_settings', 'banners', 'orders', 'order_items', 'quotes', 'quote_items', 'stock_movements'];

    public function index(): View
    {
        return view('admin.backups.index');
    }

    public function download(): StreamedResponse
    {
        $backup = ['version' => 1, 'created_at' => now()->toIso8601String(), 'tables' => []];
        foreach (self::TABLES as $table) {
            $backup['tables'][$table] = DB::table($table)->get()->map(fn ($row) => (array) $row)->all();
        }

        return response()->streamDownload(function () use ($backup): void {
            echo json_encode($backup, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
        }, 'respaldo-nexora-'.now()->format('Y-m-d-His').'.json', ['Content-Type' => 'application/json']);
    }

    public function restore(Request $request): RedirectResponse
    {
        $request->validate(['backup' => ['required', 'file', 'mimes:json,txt', 'max:30720'], 'confirmation' => ['accepted']]);
        $backup = json_decode($request->file('backup')->get(), true);

        if (! is_array($backup) || ($backup['version'] ?? null) !== 1 || ! isset($backup['tables']) || ! is_array($backup['tables'])) {
            return back()->with('error', 'El archivo no es un respaldo válido de NEXORA.');
        }

        DB::transaction(function () use ($backup): void {
            foreach (array_reverse(self::TABLES) as $table) {
                DB::table($table)->delete();
            }
            foreach (self::TABLES as $table) {
                $rows = $backup['tables'][$table] ?? [];
                if (is_array($rows) && $rows !== []) {
                    DB::table($table)->insert($rows);
                }
            }
        });

        return redirect()->route('admin.backups.index')->with('success', 'Respaldo restaurado correctamente.');
    }
}
