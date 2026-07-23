<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminInvitationController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\LinkAnalyticsController;
use App\Http\Controllers\LinkController;
use App\Http\Controllers\MagicLoginController;
use App\Http\Controllers\QrExportController;
use App\Http\Controllers\QrRegenerationController;
use App\Http\Controllers\QrTemplateController;
use App\Http\Controllers\RedirectController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');
Route::get('/login', [MagicLoginController::class, 'create'])->name('login');
Route::post('/login', [MagicLoginController::class, 'store'])->middleware('throttle:6,1')->name('login.store');
Route::get('/login/verify/{loginLink}', [MagicLoginController::class, 'verify'])->middleware('signed')->name('login.verify');

Route::get('/invite', [InvitationController::class, 'create'])->name('invite.create');
Route::post('/invite', [InvitationController::class, 'store'])->middleware('throttle:6,1')->name('invite.register');
Route::get('/invite/verify/{user}/{invitation}', [InvitationController::class, 'verify'])->middleware('signed')->name('invite.verify');

Route::middleware('auth')->group(function (): void {
    Route::get('/dashboard', [LinkController::class, 'index'])->name('dashboard');
    Route::get('/links/create', [LinkController::class, 'create'])->name('links.create');
    Route::get('/templates', [QrTemplateController::class, 'index'])->name('templates.index');
    Route::post('/templates', [QrTemplateController::class, 'store'])->name('templates.store');
    Route::post('/links', [LinkController::class, 'store'])->name('links.store');
    Route::put('/links/{link:slug}', [LinkController::class, 'update'])->name('links.update');
    Route::post('/links/{link:slug}/qr/regenerate', [QrRegenerationController::class, 'store'])->name('links.qr-regenerate');
    Route::get('/links/{link:slug}/analytics', [LinkAnalyticsController::class, 'show'])->name('links.analytics');
    Route::get('/links/{link:slug}/qr.png', [QrExportController::class, 'png'])->name('links.qr-png');
    Route::get('/links/{link:slug}', [LinkController::class, 'show'])->name('links.show');
});

Route::middleware(['auth', 'can:access-admin'])->prefix('admin')->name('admin.')->group(function (): void {
    Route::get('/', [AdminController::class, 'index'])->name('dashboard');
    Route::get('/stats', [AdminController::class, 'stats'])->name('stats');
    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::patch('/users/{user}/role', [AdminUserController::class, 'updateRole'])->name('users.role');
    Route::get('/invitations', [AdminInvitationController::class, 'index'])->name('invitations.index');
    Route::post('/invitations', [AdminInvitationController::class, 'store'])->name('invitations.store');
    Route::patch('/invitations/{invitation}/revoke', [AdminInvitationController::class, 'revoke'])->name('invitations.revoke');
});

Route::get('/{link:slug}', RedirectController::class)->where('link', '[A-Za-z0-9_-]+')->name('links.redirect');
