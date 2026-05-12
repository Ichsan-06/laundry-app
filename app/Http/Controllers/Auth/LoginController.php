<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if ($user && ! $user->aktif) {
            return back()
                ->withErrors(['email' => 'Akun Anda sedang nonaktif. Hubungi administrator.'])
                ->onlyInput('email');
        }

        if (! Auth::attempt($credentials)) {
            return back()
                ->withErrors(['email' => 'Email atau password tidak cocok.'])
                ->onlyInput('email');
        }

        $request->session()->regenerate();

        return redirect()->intended(route($this->redirectPathFor(Auth::user())));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    public function redirect(): RedirectResponse
    {
        return redirect()->route($this->redirectPathFor(Auth::user()));
    }

    private function redirectPathFor(?User $user): string
    {
        if (! $user) {
            return 'login';
        }

        if ($user->hasAnyRole([User::ROLE_SUPER_ADMIN, User::ROLE_ADMIN])) {
            return 'dashboard';
        }

        if ($user->can('access cashier')) {
            return 'kasir.index';
        }

        return 'dashboard';
    }
}
