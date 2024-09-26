<?php

namespace App\Repositories\Bill;

use App\Http\Requests\BillRequest;
use App\Models\Bill;
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
            
            $data = $this->model->with('listChild')->find($id);
            if(!empty($data['img'])){
                $data['img'] = asset('storage/'.$data['img']);
            }
            $data->listChild->map(function ($item) {
                if (!empty($item['img'])) {
                    $item['img'] = asset('storage/' . $item['img']);
                }
            });
            
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

}
