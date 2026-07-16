<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\InviteRegistrationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class RegisterController extends Controller
{
    public function create(Request $request): View
    {
        $inviteLocale = $request->query('locale');

        if (in_array($inviteLocale, ['tr', 'en'], true)) {
            session(['locale' => $inviteLocale]);
            app()->setLocale($inviteLocale);
        }

        return view('auth.register', [
            'inviteCode' => (string) $request->query('invite'),
        ]);
    }

    public function store(Request $request, InviteRegistrationService $inviteRegistrationService): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'invite_code' => ['required', 'string', 'max:64'],
        ]);

        $user = $inviteRegistrationService->register($validated);

        Auth::login($user);

        return redirect()->route('user.dashboard');
    }
}
