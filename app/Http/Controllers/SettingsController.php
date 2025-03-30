<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SettingsController extends Controller
{
    // app/Http/Controllers/SettingsController.php
public function index()
{
    return view('Settings.index');
}

public function toggleTwoFactor(Request $request)
{
    $user = $request->user();
    $user->two_factor_enabled = !$user->two_factor_enabled;
    $user->save();

    return back()->with('status', 'Two-factor authentication ' . 
        ($user->two_factor_enabled ? 'enabled' : 'disabled') . ' successfully.');
}
}
