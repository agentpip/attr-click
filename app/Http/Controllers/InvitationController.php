<?php

namespace App\Http\Controllers;

use App\Models\Invitation;
use App\Models\User;
use App\Services\PasswordlessDeliveryGuard;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Illuminate\View\View;

class InvitationController extends Controller
{
    public function create(): View
    {
        return view('auth.invite');
    }

    public function store(Request $request, PasswordlessDeliveryGuard $delivery): RedirectResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email:rfc', 'max:255'],
            'code' => ['required', 'string', 'max:80'],
        ]);

        $invitation = Invitation::query()
            ->where('code_hash', Invitation::hashCode($data['code']))
            ->first();

        if (! $invitation?->canBeUsed()) {
            return back()->withErrors(['email' => 'We could not validate that invitation.'])->onlyInput('email');
        }

        $user = User::query()->firstOrCreate(
            ['email' => strtolower($data['email'])],
            ['name' => Str::before($data['email'], '@'), 'password' => Str::random(40)]
        );

        if ($user->hasVerifiedEmail()) {
            Auth::login($user, true);

            return redirect()->route('dashboard');
        }

        $delivery->ensureReady();

        $nonce = Str::random(64);
        $user->forceFill(['verification_nonce' => hash('sha256', $nonce)])->save();
        $verificationUrl = URL::temporarySignedRoute('invite.verify', now()->addMinutes(30), [
            'user' => $user->id,
            'invitation' => $invitation->id,
            'nonce' => $nonce,
        ]);

        Mail::raw("Verify your attr.click email:\n\n{$verificationUrl}", function ($message) use ($user): void {
            $message->to($user->email)->subject('Verify your attr.click email');
        });

        return back()->with('status', 'Check your email for a verification link.');
    }

    public function verify(Request $request, User $user, Invitation $invitation): RedirectResponse
    {
        abort_unless($request->hasValidSignature() && hash_equals((string) $user->verification_nonce, hash('sha256', (string) $request->string('nonce'))), 403);

        $consumed = Invitation::query()
            ->whereKey($invitation)
            ->whereNull('revoked_at')
            ->where(fn ($query) => $query->whereNull('expires_at')->orWhere('expires_at', '>', now()))
            ->where(fn ($query) => $query->whereNull('max_uses')->orWhereColumn('uses', '<', 'max_uses'))
            ->increment('uses');

        abort_unless($consumed === 1, 403);

        $user->forceFill(['email_verified_at' => now(), 'verification_nonce' => null])->save();
        Auth::login($user, true);
        $request->session()->regenerate();

        return redirect()->route('dashboard');
    }
}
