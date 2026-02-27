<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\ChatController;
use Illuminate\Support\Facades\Route;

// ─── RUTAS PÚBLICAS ──────────────────────────────────────────────────────────
Route::get('/', fn() => redirect()->route('login'));

Route::get('/login',    [AuthController::class, 'showLogin'])->name('login');
Route::post('/login',   [AuthController::class, 'login'])->name('login.post');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register',[AuthController::class, 'register'])->name('register.post');
Route::post('/logout',  [AuthController::class, 'logout'])->name('logout');

// ─── VERIFICACIÓN DE EMAIL ───────────────────────────────────────────────────
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (
    \Illuminate\Foundation\Auth\EmailVerificationRequest $request
) {
    $request->fulfill();
    return redirect()->route('chat.index')->with('success', '¡Email verificado! Bienvenido a ChatApp.');
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/verification-notification', function (
    \Illuminate\Http\Request $request
) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('success', 'Se ha enviado un nuevo enlace de verificación.');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

// ─── RUTAS PROTEGIDAS (requieren login + email verificado) ───────────────────
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/chat',                         [ChatController::class, 'index'])->name('chat.index');
    Route::get('/chat/{conversation}',          [ChatController::class, 'show'])->name('chat.show');
    Route::post('/chat/{conversation}/message', [ChatController::class, 'sendMessage'])->name('chat.message.send');
    Route::post('/conversations',               [ChatController::class, 'createConversation'])->name('conversation.create');
});
