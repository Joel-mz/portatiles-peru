<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class CustomerAuthController extends Controller
{
    public function login(): View
    {
        return view('auth.customer-login');
    }

    public function register(): View
    {
        return view('auth.customer-register');
    }

    public function authenticate(Request $request): RedirectResponse
    {
        $data = $request->validate(['email' => ['required', 'email'], 'password' => ['required', 'string']]);
        if (! Auth::attempt($data, $request->boolean('remember'))) {
            return back()->withErrors(['email' => 'Las credenciales no son válidas.'])->onlyInput('email');
        }
        $request->session()->regenerate();

        return redirect()->intended(route('account.orders'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:100'], 'email' => ['required', 'email', 'unique:users,email'], 'password' => ['required', 'string', 'min:8', 'confirmed']]);
        $user = User::create([...$data, 'password' => Hash::make($data['password']), 'role' => 'customer']);
        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('account.orders');
    }

    public function orders(Request $request): View
    {
        $orders = Order::where('customer_email', $request->user()->email)->latest()->paginate(12);

        return view('account.orders', compact('orders'));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
