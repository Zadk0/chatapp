@extends('layouts.app')
@section('title', 'Iniciar Sesión - ChatApp')

@section('styles')
<style>
    body { background: linear-gradient(135deg, #075E54 0%, #128C7E 50%, #25D366 100%); display: flex; align-items: center; justify-content: center; }
    .auth-container {
        width: 100%;
        max-width: 420px;
        padding: 1rem;
    }
    .auth-logo {
        text-align: center;
        margin-bottom: 2rem;
        color: white;
    }
    .auth-logo .icon { font-size: 4rem; display: block; margin-bottom: .5rem; }
    .auth-logo h1 { font-size: 2rem; font-weight: 700; }
    .auth-logo p { font-size: .9rem; opacity: .85; margin-top: .25rem; }
    .auth-card {
        background: white;
        border-radius: 16px;
        padding: 2rem;
        box-shadow: 0 20px 60px rgba(0,0,0,.25);
    }
    .auth-card h2 { font-size: 1.4rem; margin-bottom: 1.5rem; color: #075E54; font-weight: 700; }
    .form-group { margin-bottom: 1.25rem; }
    .form-group label { display: block; font-size: .85rem; font-weight: 600; color: #555; margin-bottom: .4rem; }
    .form-group input {
        width: 100%;
        padding: .75rem 1rem;
        border: 2px solid #E9EDEF;
        border-radius: 10px;
        font-size: .95rem;
        transition: border-color .2s;
        outline: none;
    }
    .form-group input:focus { border-color: #25D366; }
    .form-group input.error { border-color: #e53e3e; }
    .error-msg { color: #e53e3e; font-size: .8rem; margin-top: .3rem; }
    .remember-row {
        display: flex;
        align-items: center;
        gap: .5rem;
        margin-bottom: 1.25rem;
        font-size: .9rem;
        color: #555;
    }
    .btn-primary {
        width: 100%;
        padding: .85rem;
        background: linear-gradient(135deg, #25D366, #128C7E);
        color: white;
        border: none;
        border-radius: 10px;
        font-size: 1rem;
        font-weight: 700;
        cursor: pointer;
        transition: transform .15s, box-shadow .15s;
    }
    .btn-primary:hover { transform: translateY(-1px); box-shadow: 0 8px 20px rgba(37,211,102,.4); }
    .auth-footer { text-align: center; margin-top: 1.5rem; font-size: .9rem; color: #667; }
    .auth-footer a { color: #128C7E; font-weight: 600; text-decoration: none; }
    .auth-footer a:hover { text-decoration: underline; }
    .divider { border: none; border-top: 1px solid #E9EDEF; margin: 1.25rem 0; }
</style>
@endsection

@section('content')
<div class="auth-container">
    <div class="auth-logo">
        <span class="icon">💬</span>
        <h1>ChatApp</h1>
        <p>Mensajería cifrada y segura</p>
    </div>
    <div class="auth-card">
        <h2>Iniciar Sesión</h2>
        <form method="POST" action="{{ route('login.post') }}">
            @csrf
            <div class="form-group">
                <label for="email">Correo Electrónico</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="{{ old('email') }}"
                    placeholder="tu@correo.com"
                    class="{{ $errors->has('email') ? 'error' : '' }}"
                    required autofocus>
                @error('email')
                    <p class="error-msg">{{ $message }}</p>
                @enderror
            </div>
            <div class="form-group">
                <label for="password">Contraseña</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="••••••••"
                    class="{{ $errors->has('password') ? 'error' : '' }}"
                    required>
                @error('password')
                    <p class="error-msg">{{ $message }}</p>
                @enderror
            </div>
            <div class="remember-row">
                <input type="checkbox" id="remember" name="remember" style="accent-color:#25D366">
                <label for="remember">Recordarme</label>
            </div>
            <button type="submit" class="btn-primary">Entrar →</button>
        </form>
        <hr class="divider">
        <div class="auth-footer">
            ¿No tienes cuenta? <a href="{{ route('register') }}">Regístrate gratis</a>
        </div>
    </div>
</div>
@endsection
