<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                // 役割に応じたホーム画面にリダイレクト
                $user = Auth::guard($guard)->user();

                return match ($user->role) {
                    'admin' => redirect()->route('admin.home'),
                    'teacher' => redirect()->route('teacher.home'),
                    'sub_teacher' => redirect()->route('teacher.home'),
                    'student' => redirect()->route('student.home'),
                    default => redirect(RouteServiceProvider::HOME),
                };
            }
        }

        return $next($request);
    }
}
