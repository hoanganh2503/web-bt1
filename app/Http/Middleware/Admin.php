<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use JWTAuth;
use App\Models\Role;

class Admin
{
    public function handle($request, Closure $next, ... $roles)
    {
        $user = Auth::user();
        if ($user->role_id == Role::$admin) {
            return $next($request);
        } else {
            return response()->json(['status'=>403, 'message' => 'Unauthorized'], 403);
        }
    }
}