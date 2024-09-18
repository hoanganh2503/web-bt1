<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\DeliveryRequest;
use App\Repositories\Delivery\DeliveryRepositoryInterface;
use Illuminate\Http\JsonResponse;

class DeliveryController extends Controller
{
    private $deliveryRepositoryInterface;

    public function __construct(DeliveryRepositoryInterface $deliveryRepositoryInterface){
        $this->deliveryRepositoryInterface = $deliveryRepositoryInterface;
    }
    /**
     * @OA\Get(
     *     path="/api/admin/deliveries/index",
     *     summary="Get list of deliveries",
     *     tags={"Delivery Management"},
     *     @OA\Response(response="200", description="Success",
     *         @OA\MediaType(
     *            mediaType="application/json"
     *         )
     *     ),
     *      @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search delivery by name, phone and email",
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
    public function index(DeliveryRequest $request) : JsonResponse
    {
        return $this->deliveryRepositoryInterface->getListDeliveries($request);
    }


    /**
     * @OA\Get(
     *     path="/api/admin/deliveries/detail",
     *     summary="Get detail of delivery",
     *     tags={"Delivery Management"},
     *     @OA\Response(response="200", description="Success",
     *         @OA\MediaType(
     *            mediaType="application/json"
     *         )
     *     ),
     *      @OA\Parameter(
     *         name="id",
     *         in="query",
     *         description="Id of delivery",
     *         example=1,
     *         @OA\Schema(type="integer"),
     *         required=true,
     *      ),
     *    security={{"bearerAuth":{}}}
     * )
     */
    public function detail(DeliveryRequest $request) : JsonResponse
    {
        return $this->deliveryRepositoryInterface->getDetailDelivery($request->id);
    }


    /**
     * @OA\Post(
     *     path="/api/admin/deliveries/create",
     *     summary="Create a new delivery",
     *     tags={"Delivery Management"},
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
     *                @OA\Property(property="address", type="string", example="Số 112 Nguyễn Bỉnh Khiêm"),
     *                @OA\Property(property="name", type="string", example="Giao hàng tiết kiệm"),
     *                @OA\Property(property="phone", type="string", example="0342835419"),
     *                @OA\Property(property="email", type="string", example="ghtk@gmail.com"),
     *                @OA\Property(property="description", type="string", example="Giao hàng rẻ nhưng không nhanh"),
     *                @OA\Property(property="image", type="file"),
     *            )
     *        )
     *     ),
     *    security={{"bearerAuth":{}}}
     * )
     */
    public function create(DeliveryRequest $request) : JsonResponse
    {
        return $this->deliveryRepositoryInterface->addDelivery($request);
    }


    /**
     * @OA\Post(
     *     path="/api/admin/deliveries/edit",
     *     summary="Update new delivery",
     *     tags={"Delivery Management"},
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
     *                @OA\Property(property="address", type="string", example="Số 112 Nguyễn Bỉnh Khiêm"),
     *                @OA\Property(property="name", type="string", example="Giao hàng tiết kiệm"),
     *                @OA\Property(property="phone", type="string", example="0342835419"),
     *                @OA\Property(property="email", type="string", example="ghtk@gmail.com"),
     *                @OA\Property(property="description", type="string", example="Giao hàng rẻ nhưng không nhanh"),
     *                @OA\Property(property="image", type="file"),
     *            )
     *        )
     *     ),
     *    security={{"bearerAuth":{}}}
     * )
     */
    public function edit(DeliveryRequest $request) : JsonResponse
    {
        return $this->deliveryRepositoryInterface->updateDelivery($request);
    }

    /**
     * @OA\Delete(
     *     path="/api/admin/deliveries/delete",
     *     summary="Delete a delivery",
     *     tags={"Delivery Management"},
     *     @OA\Response(response="200", description="Success",
     *         @OA\MediaType(
     *            mediaType="application/json"
     *         )
     *     ),
     *      @OA\Parameter(
     *         name="id",
     *         in="query",
     *         description="Delivery ID is deleted",
     *         example=1,
     *         @OA\Schema(type="string"),
     *         required=true,
     *      ),
     *    security={{"bearerAuth":{}}}
     * )
     */
    public function delete(DeliveryRequest $request) : JsonResponse
    {
        return $this->deliveryRepositoryInterface->deleteDelivery($request->id);
    }
}
