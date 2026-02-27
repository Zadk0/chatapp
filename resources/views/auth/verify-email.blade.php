@extends('layouts.app')
@section('title', 'Verifica tu correo - ChatApp')

@section('styles')
<style>
    body { background: linear-gradient(135deg, #075E54, #25D366); display: flex; align-items: center; justify-content: center; }
    .verify-container { background: white; border-radius: 16px; padding: 3rem 2rem; text-align: center; max-width: 460px; width: 90%; box-shadow: 0 20px 60px rgba(0,0,0,.25); }
    .verify-icon { font-size: 5rem; display: block; margin-bottom: 1rem; }
    h1 { color: #075E54; font-size: 1.6rem; margin-bottom: 1rem; }
    p { color: #667; line-height: 1.6; margin-bottom: 1.5rem; }
    .btn {
        padding: .85rem 2rem;
        border-radius: 10px;
        font-size: .95rem;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
        display: inline-block;
        transition: transform .15s;
        border: none;
    }
    .btn:hover { transform: translateY(-1px); }
    .btn-green { background: linear-gradient(135deg, #25D366, #128C7E); color: white; }
    .btn-outline { background: transparent; border: 2px solid #E9EDEF; color: #667; margin-left: .5rem; }
    .success-msg { background: #F0FFF4; color: #276749; border: 1px solid #25D366; border-radius: 8px; padding: .75rem; margin-bottom: 1.5rem; font-size: .9rem; }
</style>
@endsection

@section('content')
<div class="verify-container">
    <span class="verify-icon">📧</span>
    <h1>Verifica tu correo</h1>

    @if(session('success'))
        <div class="success-msg">✅ {{ session('success') }}</div>
    @endif

    <p>
        Hemos enviado un enlace de verificación a <strong>{{ auth()->user()->email }}</strong>.
        Por favor revisa tu bandeja de entrada y haz clic en el enlace para activar tu cuenta.
    </p>
    <p style="font-size: .85rem; color: #999;">
        ¿No recibiste el correo? Revisa tu carpeta de spam o reenvía el enlace.
    </p>

    <form method="POST" action="{{ route('verification.send') }}" style="display: inline">
        @csrf
        <button type="submit" class="btn btn-green">📨 Reenviar correo</button>
    </form>

    <form method="POST" action="{{ route('logout') }}" style="display: inline">
        @csrf
        <button type="submit" class="btn btn-outline">← Salir</button>
    </form>
</div>
@endsection
