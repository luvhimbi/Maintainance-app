<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HandleStorageAccess
{
    public function handle(Request $request, Closure $next)
    {
        $path = $request->path();
        
        if (str_starts_with($path, 'storage/')) {
            $filePath = str_replace('storage/', '', $path);
            
            if (Storage::disk('public')->exists($filePath)) {
                return response()->file(Storage::disk('public')->path($filePath));
            }
        }
        
        return $next($request);
    }
} 