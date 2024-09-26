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

    
    /**
     * @OA\Get(
     *     path="/api/profile",
     *     summary="Get profile user details",
     *     tags={"Pages for users"},
     *     @OA\Response(response="200", description="Success",
     *         @OA\MediaType(
     *            mediaType="application/json"
     *         )
     *     ),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function profile(HomeRequest $request)
    {
        return $this->homeRepositoryInterface->profile($request);
    }

    /**
     * @OA\Post(
     *     path="/api/change-profile",
     *     summary="Change Profile information",
     *     tags={"Pages for users"},
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
    public function changeProfile(HomeRequest $request)
    {
        return $this->homeRepositoryInterface->changeProfile($request);
    }

    /**
     * @OA\Get(
     *     path="/api/address",
     *     summary="Get list of addresses",
     *     tags={"User's Address"},
     *     @OA\Response(response="200", description="Success",
     *         @OA\MediaType(
     *            mediaType="application/json"
     *         )
     *     ),
     *    security={{"bearerAuth":{}}}
     * )
     */
    public function address(HomeRequest $request) : JsonResponse
    {
        return $this->homeRepositoryInterface->getListAddresses($request);
    }


    /**
     * @OA\Get(
     *     path="/api/detail-address",
     *     summary="Get detail address information",
     *     tags={"User's Address"},
     *     @OA\Response(response="200", description="Success",
     *         @OA\MediaType(
     *            mediaType="application/json"
     *         )
     *     ),
     *      @OA\Parameter(
     *         name="id",
     *         in="query",
     *         description="Id address information",
     *         example=1,
     *         @OA\Schema(type="integer"),
     *         required=true,
     *      ),
     *    security={{"bearerAuth":{}}}
     * )
     */
    public function detailAddress(HomeRequest $request) : JsonResponse
    {
        return $this->homeRepositoryInterface->getDetailAddress($request->id);
    }


    /**
     * @OA\Post(
     *     path="/api/create-address",
     *     summary="Create address information",
     *     tags={"User's Address"},
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
     *                @OA\Property(property="detail_address", type="string", example="Số 112 Nguyễn Bỉnh Khiêm"),
     *                @OA\Property(property="name", type="string", example="Giao hàng tiết kiệm"),
     *                @OA\Property(property="ward_id", type="string", example="00091"),
     *                @OA\Property(property="phone", type="string", example="0342835419"),
     *            )
     *        )
     *     ),
     *    security={{"bearerAuth":{}}}
     * )
     */
    public function createAddress(HomeRequest $request) : JsonResponse
    {
        return $this->homeRepositoryInterface->addAddress($request);
    }


    /**
     * @OA\Post(
     *     path="/api/edit-address",
     *     summary="Edit address information",
     *     tags={"User's Address"},
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
     *                @OA\Property(property="detail_address", type="string", example="Số 112 Nguyễn Bỉnh Khiêm"),
     *                @OA\Property(property="name", type="string", example="Giao hàng tiết kiệm"),
     *                @OA\Property(property="ward_id", type="string", example="00091"),
     *                @OA\Property(property="phone", type="string", example="0342835419"),
     *            )
     *        )
     *     ),
     *    security={{"bearerAuth":{}}}
     * )
     */
    public function editAddress(HomeRequest $request) : JsonResponse
    {
        return $this->homeRepositoryInterface->updateAddress($request);
    }

    /**
     * @OA\Delete(
     *     path="/api/delete-address",
     *     summary="Delete a Address",
     *     tags={"User's Address"},
     *     @OA\Response(response="200", description="Success",
     *         @OA\MediaType(
     *            mediaType="application/json"
     *         )
     *     ),
     *      @OA\Parameter(
     *         name="id",
     *         in="query",
     *         description="Address ID is deleted",
     *         example=1,
     *         @OA\Schema(type="string"),
     *         required=true,
     *      ),
     *    security={{"bearerAuth":{}}}
     * )
     */
    public function deleteAddress(HomeRequest $request) : JsonResponse
    {
        return $this->homeRepositoryInterface->deleteAddress($request->id);
    }

    /**
     * @OA\Get(
     *     path="/api/checkout",
     *     summary="Checkout",
     *     tags={"Pages for users"},
     *     @OA\Response(response="200", description="Success",
     *         @OA\MediaType(
     *            mediaType="application/json"
     *         )
     *     ),
     *    security={{"bearerAuth":{}}}
     * )
     */
    public function checkout(HomeRequest $request) : JsonResponse
    {
        return $this->homeRepositoryInterface->checkout($request);
    }

    /**
     * @OA\Post(
     *     path="/api/order",
     *     summary="Order",
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
     *                @OA\Property(property="address_id", type="integer", example=2),
     *                @OA\Property(property="delivery_id", type="integer", example=1),
     *                @OA\Property(property="note", type="string", example="Banh mi bo sua 5 nghin 1 cai."),
     *                @OA\Property(property="total_price", type="integer", example=1234324),
     *            )
     *        )
     *     ),
     *    security={{"bearerAuth":{}}}
     * )
     */
    public function order(HomeRequest $request) : JsonResponse
    {
        return $this->homeRepositoryInterface->order($request);
    }

    /**
     * @OA\Get(
     *     path="/api/order-history",
     *     summary="Order history",
     *     tags={"Pages for users"},
     *     @OA\Response(response="200", description="Success",
     *         @OA\MediaType(
     *            mediaType="application/json"
     *         )
     *     ),
     *    security={{"bearerAuth":{}}}
     * )
     */
    public function orderHistory(HomeRequest $request) : JsonResponse
    {
        return $this->homeRepositoryInterface->orderHistory($request);
    }

    /**
     * @OA\Get(
     *     path="/api/order-detail",
     *     summary="Order detail",
     *     tags={"Pages for users"},
     *     @OA\Response(response="200", description="Success",
     *         @OA\MediaType(
     *            mediaType="application/json"
     *         )
     *     ),
     *      @OA\Parameter(
     *         name="id",
     *         in="query",
     *         description="ID of bill",
     *         example=1,
     *         @OA\Schema(type="integer"),
     *         required=false,
     *      ),
     *    security={{"bearerAuth":{}}}
     * )
     */
    public function orderDetail(HomeRequest $request) : JsonResponse
    {
        return $this->homeRepositoryInterface->orderDetail($request);
    }

    /**
     * @OA\Post(
     *     path="/api/change-status",
     *     summary="Change status",
     *     tags={"Pages for users"},
     *     @OA\Response(response="200", description="Success",
     *         @OA\MediaType(
     *            mediaType="application/json"
     *         )
     *     ),
     *      @OA\Parameter(
     *         name="id",
     *         in="query",
     *         description="ID of bill",
     *         example=1,
     *         @OA\Schema(type="integer"),
     *         required=false,
     *      ),
     *    security={{"bearerAuth":{}}}
     * )
     */
    public function changeStatus(HomeRequest $request) : JsonResponse
    {
        return $this->homeRepositoryInterface->changeStatus($request->id);
    }
}
