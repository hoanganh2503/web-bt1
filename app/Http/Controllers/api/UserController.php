<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Repositories\User\UserRepositoryInterface;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    private $userRepositoryInterface;

    public function __construct(UserRepositoryInterface $userRepositoryInterface){
        $this->userRepositoryInterface = $userRepositoryInterface;
    }
    /**
     * @OA\Get(
     *     path="/api/admin/users/index",
     *     summary="Get list of users",
     *     tags={"User Management"},
     *     @OA\Response(response="200", description="Success",
     *         @OA\MediaType(
     *            mediaType="application/json"
     *         )
     *     ),
     *      @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search user by name, phone and email",
     *         example="food",
     *         @OA\Schema(type="string"),
     *         required=false,
     *      ),
     *      @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number for paginated results",
     *         example=1,
     *         @OA\Schema(type="integer"),
     *         required=false,
     *      ),
     *      @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="0:deactivated, 1:activated",
     *         example=1,
     *         @OA\Schema(type="integer"),
     *         required=false,
     *      ),
     *      @OA\Parameter(
     *         name="perpage",
     *         in="query",
     *         description="Number of items per page",
     *         example=20,
     *         @OA\Schema(type="integer"),
     *      required=false,
     *      ),
     *    security={{"bearerAuth":{}}}
     * )
     */
    public function index(UserRequest $request) : JsonResponse
    {
        return $this->userRepositoryInterface->getListUsers($request);
    }


    /**
     * @OA\Get(
     *     path="/api/admin/users/detail",
     *     summary="Get detail of User",
     *     tags={"User Management"},
     *     @OA\Response(response="200", description="Success",
     *         @OA\MediaType(
     *            mediaType="application/json"
     *         )
     *     ),
     *      @OA\Parameter(
     *         name="id",
     *         in="query",
     *         description="Id of User",
     *         example=1,
     *         @OA\Schema(type="integer"),
     *         required=true,
     *      ),
     *    security={{"bearerAuth":{}}}
     * )
     */
    public function detail(UserRequest $request) : JsonResponse
    {
        return $this->userRepositoryInterface->getDetailUser($request->id);
    }


    /**
     * @OA\Post(
     *     path="/api/admin/users/change-status",
     *     summary="Change the status of a user",
     *     tags={"User Management"},
     *      @OA\Parameter(
     *         name="id",
     *         in="query",
     *         description="User ID to change status",
     *         example=1,
     *         @OA\Schema(type="string"),
     *         required=true,
     *      ),
     *      @OA\Response(response="200", description="Success",
     *         @OA\MediaType(
     *            mediaType="application/json"
     *         )
     *     ),
     *    security={{"bearerAuth":{}}}
     * )
     */
    public function changeStatus(UserRequest $request) : JsonResponse
    {
        return $this->userRepositoryInterface->changeStatus($request);
    }

    /**
     * @OA\Delete(
     *     path="/api/admin/users/delete",
     *     summary="Delete a User",
     *     tags={"User Management"},
     *     @OA\Response(response="200", description="Success",
     *         @OA\MediaType(
     *            mediaType="application/json"
     *         )
     *     ),
     *      @OA\Parameter(
     *         name="id",
     *         in="query",
     *         description="User ID is deleted",
     *         example=1,
     *         @OA\Schema(type="string"),
     *         required=true,
     *      ),
     *    security={{"bearerAuth":{}}}
     * )
     */
    public function delete(UserRequest $request) : JsonResponse
    {
        return $this->userRepositoryInterface->deleteUser($request->id);
    }
}
