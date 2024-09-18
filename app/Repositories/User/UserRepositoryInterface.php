<?php

namespace App\Repositories\User;

use App\Http\Requests\UserRequest;
use App\Repositories\BaseRepositoryInterface;

interface UserRepositoryInterface extends BaseRepositoryInterface
{
    public function getListUsers(UserRequest $request, $perpage = 10);
    public function getDetailUser($id);
    public function changeStatus(UserRequest $request);
    public function deleteUser($id);
}
