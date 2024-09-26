<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\BillRequest;
use App\Repositories\Bill\BillRepositoryInterface;
use Illuminate\Http\JsonResponse;

class BillController extends Controller
{
    private $BillRepositoryInterface;

    public function __construct(BillRepositoryInterface $BillRepositoryInterface){
        $this->BillRepositoryInterface = $BillRepositoryInterface;
    }
    /**
     * @OA\Get(
     *     path="/api/admin/bills/index",
     *     summary="Get list of Bills",
     *     tags={"Bill Management"},
     *     @OA\Response(response="200", description="Success",
     *         @OA\MediaType(
     *            mediaType="application/json"
     *         )
     *     ),
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
    public function index(BillRequest $request) : JsonResponse
    {
        return $this->BillRepositoryInterface->getListBills($request);
    }

    /**
     * @OA\Get(
     *     path="/api/admin/bills/detail",
     *     summary="Get detail of Bills",
     *     tags={"Bill Management"},
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
    public function detail(BillRequest $request) : JsonResponse
    {
        return $this->BillRepositoryInterface->getDetailBill($request->id);
    }

    /**
     * @OA\Post(
     *     path="/api/admin/bills/change-status",
     *     summary="Change the status of the order",
     *     tags={"Bill Management"},
     *     @OA\Response(response="200", description="Success",
     *         @OA\MediaType(
     *            mediaType="application/json"
     *         )
     *     ),
     *      @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="1: Đã đặt hàng; 2: Đã xác nhận; 3:Đang giao hàng; 4:Giao hàng thành công; 5:Đã hủy",
     *         example=1,
     *         @OA\Schema(type="integer"),
     *         required=false,
     *      ),
     *      @OA\Parameter(
     *         name="bill_id",
     *         in="query",
     *         description="Bill id",
     *         example=1,
     *         @OA\Schema(type="integer"),
     *         required=false,
     *      ),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function changeStatus(BillRequest $request) : JsonResponse
    {
        return $this->BillRepositoryInterface->changeStatus($request);
    }
}
