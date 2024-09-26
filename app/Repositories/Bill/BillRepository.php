<?php

namespace App\Repositories\Bill;

use App\Http\Requests\BillRequest;
use App\Models\Address;
use App\Models\Bill;
use App\Models\User as ModelsUser;
use App\Repositories\Bill\BillRepositoryInterface;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;


/**
 * Class BillRepository.
 */
class BillRepository extends BaseRepository implements BillRepositoryInterface
{
    public function getModel()
    {
        return Bill::class;
    }

    public function getListBills(BillRequest $request, $perpage = 10){
        $perpage = $request->perpage ?? $perpage;
        try{
            $data = $this->model
                ->orderBy('created_at', 'desc')
                ->with('address')
                ->paginate($perpage);
        }catch(\Exception $e){
            return response()->json([
                'status' => 500,
                'message' => $e->getMessage(),
                'data' => []
            ]);
        }

        return response()->json([
            'status' => 200,
            'message' => "Success",
            'data' => $data
        ]);
    }

    public function getDetailBill($id){
        try{
            $data = Bill::where('id', $id)->with(['product', 'address'])->get();
        }catch(\Exception $e){
            return response()->json([
                'status' => 500,
                'message' => $e->getMessage(),
                'data' => []
            ]);
        }
        return response()->json([
            'status' => 200,
            'message' => "Success",
            'data' => $data
        ]);
    }

    public function changeStatus(BillRequest $request){
        try{
            $this->model->where('id', $request->bill_id)->update(['status' => $request->status]);
        }catch(\Exception $e){
            return response()->json([
                'status' => 500,
                'message' => $e->getMessage(),
                'data' => []
            ]);
        }
        return response()->json([
            'status' => 200,
            'message' => "Success",
            'data' => []
        ]);
    }

}
