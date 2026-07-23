<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminUserController extends Controller
{
    public function index(): View
    {
        return view('admin.users', ['users' => User::query()->withCount('links')->latest()->paginate(25)]);
    }

    public function updateRole(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate(['is_admin' => ['required', 'boolean']]);

        abort_if($user->is($request->user()), 403);

        $user->update(['is_admin' => $data['is_admin']]);

        return redirect()->route('admin.users.index')->with('status', 'User role updated.');
    }
}
