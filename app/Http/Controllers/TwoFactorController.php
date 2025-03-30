<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TwoFactorController extends Controller
{
    // app/Http/Controllers/TwoFactorController.php
public function index()
{
    return view('2fa.index');
}

public function verify(Request $request)
{
    $request->validate([
        'code' => 'required|numeric',
    ]);

    $userId = session('two_factor_user_id');
    $user = User::findOrFail($userId);

    if ($user->two_factor_code === $request->code && 
        now()->lt($user->two_factor_expires_at)) {
        $user->update([
            'two_factor_code' => null,
            'two_factor_expires_at' => null,
        ]);

        auth()->login($user);
        return redirect()->intended('/home');
    }

    return back()->withErrors(['code' => 'Invalid or expired code']);
}
}
