<?php

namespace App\Http\Controllers;

use App\Models\Invitation;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminInvitationController extends Controller
{
    public function index(): View
    {
        return view('admin.invitations', ['invitations' => Invitation::query()->latest()->paginate(25)]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'code' => [
                'required',
                'string',
                'max:80',
                function (string $attribute, mixed $value, Closure $fail): void {
                    if (is_string($value) && Invitation::query()->where('code_hash', Invitation::hashCode($value))->exists()) {
                        $fail('That invitation code already exists.');
                    }
                },
            ],
            'max_uses' => ['nullable', 'integer', 'min:1'],
            'expires_at' => ['nullable', 'date', 'after:now'],
        ]);

        Invitation::query()->create($data);

        return redirect()->route('admin.invitations.index')->with('status', 'Invitation code issued.');
    }

    public function revoke(Invitation $invitation): RedirectResponse
    {
        $invitation->update(['revoked_at' => now()]);

        return redirect()->route('admin.invitations.index')->with('status', 'Invitation revoked.');
    }
}
