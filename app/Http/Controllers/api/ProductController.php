<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Repositories\Product\ProductRepositoryInterface;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    private $productRepositoryInterface;

    public function __construct(ProductRepositoryInterface $productRepositoryInterface){
        $this->productRepositoryInterface = $productRepositoryInterface;
    }
    /**
     * @OA\Get(
     *     path="/api/admin/products/index",
     *     summary="Get list of products",
     *     tags={"Product Management"},
     *     @OA\Response(response="200", description="Success",
     *         @OA\MediaType(
     *            mediaType="application/json"
     *         )
     *     ),
     *      @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search product by name",
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
    public function index(ProductRequest $request) : JsonResponse
    {
        return $this->productRepositoryInterface->getListProducts($request);
    }


    /**
     * @OA\Get(
     *     path="/api/admin/products/detail",
     *     summary="Get detail of Product",
     *     tags={"Product Management"},
     *     @OA\Response(response="200", description="Success",
     *         @OA\MediaType(
     *            mediaType="application/json"
     *         )
     *     ),
     *      @OA\Parameter(
     *         name="id",
     *         in="query",
     *         description="Id of Product",
     *         example=1,
     *         @OA\Schema(type="integer"),
     *         required=true,
     *      ),
     *    security={{"bearerAuth":{}}}
     * )
     */
    public function detail(ProductRequest $request) : JsonResponse
    {
        return $this->productRepositoryInterface->getDetailProduct($request->id);
    }


    /**
     * @OA\Post(
     *     path="/api/admin/products/create",
     *     summary="Create a new Product",
     *     tags={"Product Management"},
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
     *                @OA\Property(property="name", type="string", example="Iphone 16 promax"),
     *                @OA\Property(property="description", type="string", example="Giao hàng rẻ nhưng không nhanh"),
     *                @OA\Property(property="image", type="file"),
     *            )
     *        )
     *     ),
     *    security={{"bearerAuth":{}}}
     * )
     */
    public function create(ProductRequest $request) : JsonResponse
    {
        return $this->productRepositoryInterface->addProduct($request);
    }


    /**
     * @OA\Post(
     *     path="/api/admin/products/edit",
     *     summary="Update new Product",
     *     tags={"Product Management"},
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
     *                @OA\Property(property="name", type="string", example="Hoa Loa kèn"),
     *                @OA\Property(property="description", type="string", example="Hoa Loa kèn rất loa kèn"),
     *                @OA\Property(property="image", type="file"),
     *            )
     *        )
     *     ),
     *    security={{"bearerAuth":{}}}
     * )
     */
    public function edit(ProductRequest $request) : JsonResponse
    {
        return $this->productRepositoryInterface->updateProduct($request);
    }

    /**
     * @OA\Delete(
     *     path="/api/admin/products/delete",
     *     summary="Delete a Product",
     *     tags={"Product Management"},
     *     @OA\Response(response="200", description="Success",
     *         @OA\MediaType(
     *            mediaType="application/json"
     *         )
     *     ),
     *      @OA\Parameter(
     *         name="id",
     *         in="query",
     *         description="Product ID is deleted",
     *         example=1,
     *         @OA\Schema(type="string"),
     *         required=true,
     *      ),
     *    security={{"bearerAuth":{}}}
     * )
     */
    public function delete(ProductRequest $request) : JsonResponse
    {
        return $this->productRepositoryInterface->deleteProduct($request->id);
    }

    /**
     * @OA\Get(
     *     path="/api/admin/products/detail-child",
     *     summary="Get detail of Feature Product",
     *     tags={"Product Management"},
     *     @OA\Response(response="200", description="Success",
     *         @OA\MediaType(
     *            mediaType="application/json"
     *         )
     *     ),
     *      @OA\Parameter(
     *         name="id",
     *         in="query",
     *         description="Id of Product",
     *         example=1,
     *         @OA\Schema(type="integer"),
     *         required=true,
     *      ),
     *    security={{"bearerAuth":{}}}
     * )
     */
    public function detailChild(ProductRequest $request) : JsonResponse
    {
        return $this->productRepositoryInterface->getDetailChild($request->id);
    }

    /**
     * @OA\Post(
     *     path="/api/admin/products/create-child",
     *     summary="Create a new Feature Product",
     *     tags={"Product Management"},
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
     *                @OA\Property(property="product_id", type="integer", example=2),
     *                @OA\Property(property="feature_name", type="string", example="Iphone 16 promax"),
     *                @OA\Property(property="cost_price", type="integer", example=20000),
     *                @OA\Property(property="selling_price", type="integer", example=30000),
     *                @OA\Property(property="quantity", type="integer", example=200),
     *                @OA\Property(property="image", type="file"),
     *            )
     *        )
     *     ),
     *    security={{"bearerAuth":{}}}
     * )
     */
    public function createChild(ProductRequest $request) : JsonResponse
    {
        return $this->productRepositoryInterface->addFeatureProduct($request);
    }

    /**
     * @OA\Post(
     *     path="/api/admin/products/edit-child",
     *     summary="Edit a Feature Product",
     *     tags={"Product Management"},
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
     *                @OA\Property(property="id", type="integer", example=2),
     *                @OA\Property(property="product_id", type="integer", example=2),
     *                @OA\Property(property="feature_name", type="string", example="Iphone 16 promax"),
     *                @OA\Property(property="cost_price", type="integer", example=20000),
     *                @OA\Property(property="selling_price", type="integer", example=30000),
     *                @OA\Property(property="quantity", type="integer", example=200),
     *                @OA\Property(property="image", type="file"),
     *            )
     *        )
     *     ),
     *    security={{"bearerAuth":{}}}
     * )
     */
    public function editChild(ProductRequest $request) : JsonResponse
    {
        return $this->productRepositoryInterface->editFeatureProduct($request);
    }

    /**
     * @OA\Delete(
     *     path="/api/admin/products/delete-child",
     *     summary="Delete a Feature Product",
     *     tags={"Product Management"},
     *     @OA\Response(response="200", description="Success",
     *         @OA\MediaType(
     *            mediaType="application/json"
     *         )
     *     ),
     *      @OA\Parameter(
     *         name="id",
     *         in="query",
     *         description="Product ID is deleted",
     *         example=1,
     *         @OA\Schema(type="string"),
     *         required=true,
     *      ),
     *    security={{"bearerAuth":{}}}
     * )
     */
    public function deleteChild(ProductRequest $request) : JsonResponse
    {
        return $this->productRepositoryInterface->deleteFeatureProduct($request->id);
    }
}
