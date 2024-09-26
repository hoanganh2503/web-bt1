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
}
