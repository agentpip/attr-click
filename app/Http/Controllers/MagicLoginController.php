<?php

namespace App\Http\Controllers;

use App\Models\LoginLink;
use App\Models\User;
use App\Services\PasswordlessDeliveryGuard;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Illuminate\View\View;

class MagicLoginController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(Request $request, PasswordlessDeliveryGuard $delivery): RedirectResponse
    {
        $data = $request->validate(['email' => ['required', 'email:rfc', 'max:255']]);
        $user = User::query()->where('email', strtolower($data['email']))->whereNotNull('email_verified_at')->first();

        if (! $user) {
            return back()->with('status', 'If that address is eligible, we’ll send a sign-in link.');
        }

        $delivery->ensureReady();

        $token = Str::random(64);
        $loginLink = $user->loginLinks()->create([
            'token_hash' => hash('sha256', $token),
            'expires_at' => now()->addMinutes(30),
        ]);
        $loginUrl = URL::temporarySignedRoute('login.verify', $loginLink->expires_at, [
            'loginLink' => $loginLink->id,
            'token' => $token,
        ]);

        Mail::raw("Sign in to attr.click:\n\n{$loginUrl}", function ($message) use ($user): void {
            $message->to($user->email)->subject('Sign in to attr.click');
        });

        return back()->with('status', 'If that address is eligible, we’ll send a sign-in link.');
    }

    public function verify(Request $request, LoginLink $loginLink): RedirectResponse
    {
        abort_unless(
            $request->hasValidSignature()
            && $loginLink->used_at === null
            && $loginLink->expires_at->isFuture()
            && hash_equals($loginLink->token_hash, hash('sha256', (string) $request->string('token'))),
            403,
        );

        $consumed = LoginLink::query()->whereKey($loginLink)->whereNull('used_at')->update(['used_at' => now()]);
        abort_unless($consumed === 1, 403);

        Auth::login($loginLink->user, true);
        $request->session()->regenerate();

        return redirect()->route('dashboard');
    }
}
