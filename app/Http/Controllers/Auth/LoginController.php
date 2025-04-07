<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
{
    $this->validate($request, [
        'email' => 'required|email',
        'password' => 'required|min:8',
    ]);

    $throttleKey = strtolower($request->input('email')) . '|' . $request->ip();

    if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
        return back()->withErrors(['email' => 'Terlalu banyak percobaan login. Coba lagi nanti.']);
    }

    if (auth()->attempt($request->only('email', 'password'))) {
        RateLimiter::clear($throttleKey);
        return redirect()->route('dashboard');
    }

    RateLimiter::hit($throttleKey, 60);

    return back()->withErrors(['email' => 'Email atau password salah.']);
}

    protected function redirectToRole()
    {
        $user = Auth::user();

        if ($user->hasRole('Admin')) {
            return redirect()->route('dashboard');
        } elseif ($user->hasRole('Headbar')) {
            return redirect()->route('dashboard');
        }elseif ($user->hasRole('HeadKitchen')) {
            return redirect()->route('dashboard');
        } elseif ($user->hasRole('Bar')) {
            return redirect()->route('dashboard');
        } elseif ($user->hasRole('Kitchen')) {
            return redirect()->route('dashboard');
        }
        return redirect('/'); // Fallback jika tidak ada role
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
