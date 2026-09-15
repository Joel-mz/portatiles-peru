<?php

namespace App\Http\Controllers;

use App\Models\CompanySetting;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index(): View
    {
        $settings = CompanySetting::current();

        return view('pages.contact', compact('settings'));
    }

    public function submit(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:100'],
            'phone' => ['required', 'string', 'max:30'],
            'subject' => ['required', 'string', 'max:150'],
            'message' => ['required', 'string', 'max:1500'],
        ]);

        return back()->with('success', '¡Gracias por contactarnos! Un asesor tecnológico de Nexora se comunicará contigo a la brevedad.');
    }
}
