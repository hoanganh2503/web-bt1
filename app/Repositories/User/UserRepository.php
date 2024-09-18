<?php

namespace App\Repositories\User;

use App\Http\Requests\UserRequest;
use App\Repositories\User\UserRepositoryInterface;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;
use App\Models\User;

/**
 * Class UserRepository.
 */
class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    public function getModel()
    {
        return User::class;
    }

    public function getListUsers(UserRequest $request, $perpage = 10){
        $search = $request->search ?? null;
        $status = $request->status ?? null;
        $perpage = $request->perpage ?? $perpage;
        try{
            $data = $this->model
                    ->where('role_id', User::$user)
                    ->when($search, function($query, $search){
                        $query->where('name', 'like', '%'.$search.'%')
                        ->orWhere('phone', 'like', '%'.$search.'%')
                        ->orWhere('email', 'like', '%'.$search.'%');
                    })
                    ->when(isset($request->status), function($query) use ($status){
                        $query->where('status', $status);
                    })
                    ->orderBy('created_at', 'desc')
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

    public function getDetailUser($id){
        try{
            $data = $this->model->with('addresses')->find($id);
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


    public function changeStatus(UserRequest $request){
        $user = $this->model->find($request->id);
        try{
            DB::beginTransaction();
            $user->update(['status' => abs(1-$user->status)]);
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

    public function deleteUser($id){
        try{
            DB::beginTransaction();
            $user = $this->model->find($id);
            if(!empty($User->img)){
                $this->deleteFile($user->img);
            }
            $user->delete();
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
