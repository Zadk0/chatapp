@extends('layouts.app')
@section('title', 'Registrarse - ChatApp')

@section('styles')
<style>
    body { background: linear-gradient(135deg, #075E54 0%, #128C7E 50%, #25D366 100%); display: flex; align-items: center; justify-content: center; }
    .auth-container { width: 100%; max-width: 420px; padding: 1rem; }
    .auth-logo { text-align: center; margin-bottom: 2rem; color: white; }
    .auth-logo .icon { font-size: 4rem; display: block; margin-bottom: .5rem; }
    .auth-logo h1 { font-size: 2rem; font-weight: 700; }
    .auth-logo p { font-size: .9rem; opacity: .85; margin-top: .25rem; }
    .auth-card { background: white; border-radius: 16px; padding: 2rem; box-shadow: 0 20px 60px rgba(0,0,0,.25); }
    .auth-card h2 { font-size: 1.4rem; margin-bottom: 1.5rem; color: #075E54; font-weight: 700; }
    .form-group { margin-bottom: 1.1rem; }
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
    .form-group input.error-input { border-color: #e53e3e; }
    .error-msg { color: #e53e3e; font-size: .8rem; margin-top: .3rem; }
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
        margin-top: .5rem;
    }
    .btn-primary:hover { transform: translateY(-1px); box-shadow: 0 8px 20px rgba(37,211,102,.4); }
    .auth-footer { text-align: center; margin-top: 1.5rem; font-size: .9rem; color: #667; }
    .auth-footer a { color: #128C7E; font-weight: 600; text-decoration: none; }
    .security-note {
        background: #F0FFF4;
        border: 1px solid #25D366;
        border-radius: 8px;
        padding: .75rem;
        font-size: .8rem;
        color: #276749;
        margin-bottom: 1.25rem;
    }
    .security-note strong { display: block; margin-bottom: .2rem; }
    .divider { border: none; border-top: 1px solid #E9EDEF; margin: 1.25rem 0; }
</style>
@endsection

@section('content')
<div class="auth-container">
    <div class="auth-logo">
        <span class="icon">💬</span>
        <h1>ChatApp</h1>
        <p>Crea tu cuenta gratis</p>
    </div>
    <div class="auth-card">
        <h2>Crear Cuenta</h2>
        <div class="security-note">
            <strong>🔒 Cifrado de extremo a extremo</strong>
            Tus mensajes y contraseña están completamente cifrados.
        </div>
        <form method="POST" action="{{ route('register.post') }}">
            @csrf
            <div class="form-group">
                <label>Nombre completo</label>
                <input type="text" name="name" value="{{ old('name') }}" placeholder="Tu nombre" class="{{ $errors->has('name') ? 'error-input' : '' }}" required autofocus>
                @error('name') <p class="error-msg">{{ $message }}</p> @enderror
            </div>
            <div class="form-group">
                <label>Correo electrónico</label>
                <input type="email" name="email" value="{{ old('email') }}" placeholder="tu@correo.com" class="{{ $errors->has('email') ? 'error-input' : '' }}" required>
                @error('email') <p class="error-msg">{{ $message }}</p> @enderror
            </div>
            <div class="form-group">
                <label>Contraseña</label>
                <input type="password" name="password" placeholder="Mínimo 8 caracteres" class="{{ $errors->has('password') ? 'error-input' : '' }}" required>
                @error('password') <p class="error-msg">{{ $message }}</p> @enderror
            </div>
            <div class="form-group">
                <label>Confirmar contraseña</label>
                <input type="password" name="password_confirmation" placeholder="Repite tu contraseña" required>
            </div>
            <button type="submit" class="btn-primary">Crear cuenta y verificar email →</button>
        </form>
        <hr class="divider">
        <div class="auth-footer">
            ¿Ya tienes cuenta? <a href="{{ route('login') }}">Inicia sesión</a>
        </div>
    </div>
</div>
@endsection
