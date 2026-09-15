<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\CompanySetting;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index(): View
    {
        $settings = CompanySetting::current();
        $banners = Banner::orderBy('sort_order')->get();

        return view('admin.settings.index', compact('settings', 'banners'));
    }

    public function update(Request $request): RedirectResponse
    {
        $settings = CompanySetting::current();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'tagline' => ['nullable', 'string', 'max:150'],
            'legal_name' => ['required', 'string', 'max:150'],
            'ruc' => ['required', 'string', 'max:20'],
            'email' => ['required', 'email', 'max:100'],
            'phone' => ['required', 'string', 'max:30'],
            'whatsapp_number' => ['required', 'string', 'max:30'],
            'whatsapp_default_message' => ['nullable', 'string', 'max:500'],
            'address' => ['required', 'string', 'max:200'],
            'schedule_weekdays' => ['required', 'string', 'max:100'],
            'schedule_weekends' => ['required', 'string', 'max:100'],
            'facebook_url' => ['nullable', 'url', 'max:200'],
            'instagram_url' => ['nullable', 'url', 'max:200'],
            'tiktok_url' => ['nullable', 'url', 'max:200'],
        ]);

        $settings->update($validated);

        return back()->with('success', 'Configuración del sistema guardada con éxito.');
    }
}
