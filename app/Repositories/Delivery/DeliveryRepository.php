<?php

namespace App\Repositories\Delivery;

use App\Http\Requests\DeliveryRequest;
use App\Repositories\Delivery\DeliveryRepositoryInterface;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;

/**
 * Class DeliveryRepository.
 */
class DeliveryRepository extends BaseRepository implements DeliveryRepositoryInterface
{
    public function getModel()
    {
        return \App\Models\Delivery::class;
    }

    public function getListDeliveries(DeliveryRequest $request, $perpage = 10){
        $search = $request->search ?? null;
        $perpage = $request->perpage ?? $perpage;
        try{
            $data = $this->model->when($search, function($query, $search){
                    $query->where('name', 'like', '%'.$search.'%')
                    ->orWhere('phone', 'like', '%'.$search.'%')
                    ->orWhere('email', 'like', '%'.$search.'%');
                })
                ->paginate($perpage);
            foreach($data as $item){
                if(!empty($item['img'])){
                    $item['img'] = asset('storage/'.$item['img']);
                }
            }
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

    public function getDetailDelivery($id){
        try{
            $data = $this->model->find($id);
            if(!empty($data['img'])){
                $data['img'] = asset('storage/'.$data['img']);
            }
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

    public function addDelivery(DeliveryRequest $request){
        $data = $request->only('name', 'address', 'phone', 'email', 'description');
        try{
            DB::beginTransaction();
            $data['created_at'] = time();
            $data['updated_at'] = time();
            if($request->hasFile('image')){
                $file = $request->file('image');
                $data['img'] = $this->saveFile($file, 'uploads/deliveries');
            }
            $res = $this->model->create($data);

        }catch(\Exception $e){
            DB::rollBack();
            return response()->json([
                'status' => 500,
                'message' => $e->getMessage(),
                'data' => []
            ]);
        }
        DB::commit();
        return $this->getDetailDelivery($res->id);
    }

    public function updateDelivery(DeliveryRequest $request){
        $id = $request->id;
        $data = $request->only('name', 'address', 'phone', 'email', 'description');
        $delivery = $this->model->find($id);
        try{
            DB::beginTransaction();
            $data['updated_at'] = time();
            if($request->hasFile('image')){
                $file = $request->file('image');
                $data['img'] = $this->saveFile($file, 'uploads/deliveries', $delivery->img);
            }
            $delivery->update($data);

        }catch(\Exception $e){
            DB::rollBack();
            return response()->json([
                'status' => 500,
                'message' => $e->getMessage(),
                'data' => []
            ]);
        }
        DB::commit();
        return $this->getDetailDelivery($id);
    }

    public function deleteDelivery($id){
        try{
            DB::beginTransaction();
            $delivery = $this->model->find($id);
            if(!empty($delivery->img)){
                $this->deleteFile($delivery->img);
            }
            $delivery->delete();
        }catch(\Exception $e){
            DB::rollBack();
            return response()->json([
                'status' => 500,
                'message' => $e->getMessage(),
                'data' => []
            ]);
        }
        DB::commit();
        return response()->json([
           'status' => 200,
           'message' => "Success",
           'data' => []
        ]);
    }


}
