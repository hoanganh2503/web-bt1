<?php

namespace App\Http\Middleware;

use App\Models\Role;
use Closure;
use Illuminate\Support\Facades\Auth;

class User
{
    public function handle($request, Closure $next, ... $roles)
    {
        $user = Auth::user();
        if ($user->role_id == Role::$user) {
            return $next($request);
        } else {
            return response()->json(['status'=>403, 'message' => 'Unauthorized'], 403);
        }
    }
}