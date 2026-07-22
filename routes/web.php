<?php

use App\Http\Controllers\InvitationController;
use App\Http\Controllers\LinkController;
use App\Http\Controllers\RedirectController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');
Route::get('/invite', [InvitationController::class, 'create'])->name('invite.create');
Route::post('/invite', [InvitationController::class, 'store'])->middleware('throttle:6,1')->name('invite.register');
Route::get('/invite/verify/{user}/{invitation}', [InvitationController::class, 'verify'])->middleware('signed')->name('invite.verify');

Route::middleware('auth')->group(function (): void {
    Route::get('/dashboard', [LinkController::class, 'index'])->name('dashboard');
    Route::get('/links/create', [LinkController::class, 'create'])->name('links.create');
    Route::post('/links', [LinkController::class, 'store'])->name('links.store');
    Route::get('/links/{link:slug}', [LinkController::class, 'show'])->name('links.show');
});

Route::get('/{link:slug}', RedirectController::class)->where('link', '[A-Za-z0-9_-]+')->name('links.redirect');
