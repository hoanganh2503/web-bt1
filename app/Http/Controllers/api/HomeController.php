<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\HomeRequest;
use App\Repositories\Home\HomeRepositoryInterface;
use Illuminate\Http\JsonResponse;

class HomeController extends Controller
{
    private $homeRepositoryInterface;
    public function __construct(HomeRepositoryInterface $homeRepositoryInterface){
        $this->homeRepositoryInterface = $homeRepositoryInterface;
    }
    /**
     * @OA\Get(
     *     path="/api/home",
     *     summary="Home",
     *     tags={"Pages for users"},
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
     * )
     */
    public function home(HomeRequest $request) : JsonResponse
    {
        return $this->homeRepositoryInterface->home($request);
    }

    /**
     * @OA\Get(
     *     path="/api/product",
     *     summary="Product information",
     *     tags={"Pages for users"},
     *     @OA\Response(response="200", description="Success",
     *         @OA\MediaType(
     *            mediaType="application/json"
     *         )
     *     ),
     *      @OA\Parameter(
     *         name="product_id",
     *         in="query",
     *         description="ID of product",
     *         example=1,
     *         @OA\Schema(type="integer"),
     *         required=false,
     *      ),
     * )
     */
    public function product(HomeRequest $request) : JsonResponse
    {
        return $this->homeRepositoryInterface->product($request->product_id);
    }

    /**
     * @OA\Post(
     *     path="/api/add-to-cart",
     *     summary="Add product to cart",
     *     tags={"Pages for users"},
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
     *                @OA\Property(property="feature_product_id", type="integer", example=2),
     *                @OA\Property(property="quantity", type="integer", example=200),
     *            )
     *        )
     *     ),
     *    security={{"bearerAuth":{}}}
     * )
     */
    public function addToCart(HomeRequest $request) : JsonResponse
    {
        return $this->homeRepositoryInterface->addToCart($request);
    }

    /**
     * @OA\Get(
     *     path="/api/cart",
     *     summary="View cart",
     *     tags={"Pages for users"},
     *     @OA\Response(response="200", description="Success",
     *         @OA\MediaType(
     *            mediaType="application/json"
     *         )
     *     ),
     *    security={{"bearerAuth":{}}}
     * )
     */
    public function cart(HomeRequest $request) : JsonResponse
    {
        return $this->homeRepositoryInterface->cart($request);
    }
}
