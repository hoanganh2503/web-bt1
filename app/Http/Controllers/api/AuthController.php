<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AuthRequest;
use App\Models\User;
use App\Repositories\Auth\AuthRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    private $authRepositoryInterface;
    public function __construct(AuthRepositoryInterface $authRepositoryInterface){
        $this->authRepositoryInterface = $authRepositoryInterface;
    }
    
    /**
     * @OA\Post(
     *     path="/api/admin/login",
     *     summary="Authenticate user and generate JWT token",
     *     tags={"Admin Authentication"},
     *     @OA\RequestBody(
     *        required=true,
     *        @OA\MediaType(
     *            mediaType="application/json",
     *            @OA\Schema(
     *                @OA\Property(property="email", type="string", example="admin@gmail.com"),
     *                @OA\Property(property="password", type="string", example="123456")
     *            )
     *        )
     *     ),
     *     @OA\Response(response="200", description="Login successful"),
     *     @OA\Response(response="401", description="Invalid credentials")
     * )
     */
    public function login(AuthRequest $request) : JsonResponse
    {
        return $this->authRepositoryInterface->login($request);
    }


    /**
     * @OA\Get(
     *     path="/api/admin/profile",
     *     summary="Get profile user details",
     *     tags={"Admin Authentication"},
     *     @OA\Response(response="200", description="Success",
     *         @OA\MediaType(
     *            mediaType="application/json"
     *         )
     *     ),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function profile(AuthRequest $request)
    {
        return $this->authRepositoryInterface->profile($request);
    }

    /**
     * @OA\Post(
     *     path="/api/admin/logout",
     *     summary="Logout",
     *     tags={"Admin Authentication"},
     *     @OA\Response(response="200", description="Success",
     *         @OA\MediaType(
     *            mediaType="application/json"
     *         )
     *     ),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function logout(AuthRequest $request)
    {
        return $this->authRepositoryInterface->logout($request);
    }


    /**
     * @OA\Post(
     *     path="/api/admin/change-profile",
     *     summary="Change Profile information",
     *     tags={"Admin Authentication"},
     *     @OA\RequestBody(
     *        required=true,
     *        @OA\MediaType(
     *            mediaType="multipart/form-data",
     *            @OA\Schema(
     *                @OA\Property(property="old_password", type="string", example="123456"),
     *                @OA\Property(property="new_password", type="string", example="123123"),
     *                @OA\Property(property="name", type="string", example="Hoàng Anh"),
     *                @OA\Property(property="phone", type="string", example="0342835419"),
     *                @OA\Property(property="image", type="file"),
     *            )
     *        )
     *     ),
     *     @OA\Response(response="200", description="Success",
     *         @OA\MediaType(
     *            mediaType="application/json"
     *         )
     *     ),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function changeProfile(AuthRequest $request)
    {
        return $this->authRepositoryInterface->changeProfile($request);
    }
}
