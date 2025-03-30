<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLayoutMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
 
    $layout = 'Layouts.StudentNavbar'; 
    
    if (auth()->check()) {
        switch(auth()->user()->role) {
            case 'Admin':
                $layout = 'Layouts.AdminNavBar';
                break;
            case 'Technician':
                $layout = 'Layouts.TechnicianNavbar';
                break;
            default: // student
                $layout = 'Layouts.StudentNavbar';
        }
    }

    view()->share('layout', $layout);
    
    return $next($request);

    }
}
