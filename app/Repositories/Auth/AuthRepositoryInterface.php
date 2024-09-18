<?php

namespace App\Repositories\Auth;

use App\Http\Requests\AuthRequest;
use App\Repositories\BaseRepositoryInterface;

interface AuthRepositoryInterface extends BaseRepositoryInterface
{
    public function login(AuthRequest $request);
    public function profile(AuthRequest $request);
    public function logout(AuthRequest $request);
    public function changeProfile(AuthRequest $request);
}
