<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Auth\Events\Registered;

class AuthController extends Controller
{
    // ─── MOSTRAR LOGIN ────────────────────────────────────────────────────────
    public function showLogin()
    {
        if (Auth::check()) return redirect()->route('chat.index');
        return view('auth.login');
    }

    // ─── PROCESAR LOGIN ───────────────────────────────────────────────────────
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            // Verificar email
            if (!Auth::user()->hasVerifiedEmail()) {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Por favor verifica tu correo electrónico antes de iniciar sesión.',
                ])->withInput($request->only('email'));
            }

            // Marcar online
            Auth::user()->update(['is_online' => true, 'last_seen' => now()]);

            return redirect()->intended(route('chat.index'));
        }

        return back()->withErrors([
            'email' => 'Las credenciales no coinciden con nuestros registros.',
        ])->withInput($request->only('email'));
    }

    // ─── MOSTRAR REGISTRO ─────────────────────────────────────────────────────
    public function showRegister()
    {
        if (Auth::check()) return redirect()->route('chat.index');
        return view('auth.register');
    }

    // ─── PROCESAR REGISTRO ────────────────────────────────────────────────────
    public function register(Request $request)
    {
        $request->validate([
            'name'     => ['required', 'string', 'max:100'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
        ], [
            'name.required'      => 'El nombre es obligatorio.',
            'email.unique'       => 'Este correo ya está registrado.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'password.min'       => 'La contraseña debe tener al menos 8 caracteres.',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password), // Bcrypt hash
        ]);

        event(new Registered($user)); // Envía email de verificación

        return redirect()->route('verification.notice')
            ->with('success', '¡Cuenta creada! Revisa tu correo para verificar tu cuenta.');
    }

    // ─── LOGOUT ───────────────────────────────────────────────────────────────
    public function logout(Request $request)
    {
        if (Auth::check()) {
            Auth::user()->update(['is_online' => false, 'last_seen' => now()]);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
