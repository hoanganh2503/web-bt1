<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use App\Repositories\Category\CategoryRepositoryInterface;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    private $categoryRepositoryInterface;

    public function __construct(CategoryRepositoryInterface $categoryRepositoryInterface){
        $this->categoryRepositoryInterface = $categoryRepositoryInterface;
    }
    /**
     * @OA\Get(
     *     path="/api/admin/categories/index",
     *     summary="Get list of categories",
     *     tags={"Categories Management"},
     *     @OA\Response(response="200", description="Success",
     *         @OA\MediaType(
     *            mediaType="application/json"
     *         )
     *     ),
     *      @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search categories by name",
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
    public function index(CategoryRequest $request) : JsonResponse
    {
        return $this->categoryRepositoryInterface->getListCategories($request);
    }


    /**
     * @OA\Get(
     *     path="/api/admin/categories/detail",
     *     summary="Get detail of category",
     *     tags={"Categories Management"},
     *     @OA\Response(response="200", description="Success",
     *         @OA\MediaType(
     *            mediaType="application/json"
     *         )
     *     ),
     *      @OA\Parameter(
     *         name="id",
     *         in="query",
     *         description="Id of category",
     *         example=1,
     *         @OA\Schema(type="integer"),
     *         required=true,
     *      ),
     *    security={{"bearerAuth":{}}}
     * )
     */
    public function detail(CategoryRequest $request) : JsonResponse
    {
        return $this->categoryRepositoryInterface->getDetailCategory($request->id);
    }


    /**
     * @OA\Post(
     *     path="/api/admin/categories/create",
     *     summary="Create a new category",
     *     tags={"Categories Management"},
     *     @OA\Response(response="200", description="Success",
     *         @OA\MediaType(
     *            mediaType="application/json"
     *         )
     *     ),
     *     @OA\RequestBody(
     *        required=true,
     *        @OA\MediaType(
     *            mediaType="multipart/form-data",
     *            @OA\Schema(
     *                @OA\Property(property="name", type="string", example="Hoàng Anh"),
     *                @OA\Property(property="image", type="file"),
     *            )
     *        )
     *     ),
     *    security={{"bearerAuth":{}}}
     * )
     */
    public function create(CategoryRequest $request) : JsonResponse
    {
        return $this->categoryRepositoryInterface->addCategory($request);
    }


    /**
     * @OA\Post(
     *     path="/api/admin/categories/edit",
     *     summary="Update new category",
     *     tags={"Categories Management"},
     *     @OA\Response(response="200", description="Success",
     *         @OA\MediaType(
     *            mediaType="application/json"
     *         )
     *     ),
     *     @OA\RequestBody(
     *        required=true,
     *        @OA\MediaType(
     *            mediaType="multipart/form-data",
     *            @OA\Schema(
     *                @OA\Property(property="id", type="integer", example=1),
     *                @OA\Property(property="name", type="string", example="Hoàng Anh"),
     *                @OA\Property(property="image", type="file"),
     *            )
     *        )
     *     ),
     *    security={{"bearerAuth":{}}}
     * )
     */
    public function edit(CategoryRequest $request) : JsonResponse
    {
        return $this->categoryRepositoryInterface->updateCategory($request);
    }

    /**
     * @OA\Delete(
     *     path="/api/admin/categories/delete",
     *     summary="Delete a category",
     *     tags={"Categories Management"},
     *     @OA\Response(response="200", description="Success",
     *         @OA\MediaType(
     *            mediaType="application/json"
     *         )
     *     ),
     *      @OA\Parameter(
     *         name="id",
     *         in="query",
     *         description="Category ID is deleted",
     *         example=1,
     *         @OA\Schema(type="string"),
     *         required=true,
     *      ),
     *    security={{"bearerAuth":{}}}
     * )
     */
    public function delete(CategoryRequest $request) : JsonResponse
    {
        return $this->categoryRepositoryInterface->deleteCategory($request->id);
    }
}
