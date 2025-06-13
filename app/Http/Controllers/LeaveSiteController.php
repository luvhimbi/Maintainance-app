<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LeaveSiteController extends Controller
{
    public function show(Request $request)
    {
        $url = $request->query('url');

        // Basic safety check
        if (!$url || !filter_var($url, FILTER_VALIDATE_URL)) {
            return redirect()->back()->with('error', 'Invalid URL.');
        }

        return view('leave-site', ['url' => $url]);
    }
}
