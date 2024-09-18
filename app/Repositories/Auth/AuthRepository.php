<?php

namespace App\Repositories\Auth;

use App\Http\Requests\AuthRequest;
use App\Repositories\Auth\AuthRepositoryInterface;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

/**
 * Class AuthRepository.
 */
class AuthRepository extends BaseRepository implements AuthRepositoryInterface
{
    public function getModel()
    {
        return \App\Models\User::class;
    }

    public function login(AuthRequest $request){
        $email = $request->email ?? null;
        $password = $request->password ?? null;
        try{
            DB::beginTransaction();
            $user = $this->model->where('email', $email)->first();
            if(!$user || !Hash::check($password, $user->password)){
                DB::rollBack();
                return response()->json([
                    'status' => 404,
                    'message' => 'Tài khoản hoặc mật khẩu không đúng',
                    'data' => []
                 ], 404);
            }
            $token = $user->createToken($user->name.'Auth-token')->plainTextToken;
            $data = [
                'access_token' => $token,
                'token_type' => 'Bearer'
            ];
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
            'data' => $data
        ], 200);
    }

    public function profile(AuthRequest $request){
        try{
            $user = $request->user();  
            if($user['img'] != null) $user['img'] = asset('storage/'.$user['img']);
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
            'data' => $user
        ], 200);  

    }

    public function logout(AuthRequest $request){
        try{
            $request->user()->currentAccessToken()->delete();  
        }catch(\Exception $e){
            return response()->json([
                'status' => 500,
                'message' => $e->getMessage(),
                'data' => []
            ]);
        }
        return response()->json([
            'status' => 200,
            'message' => "Logout successfully",
            'data' => []
        ], 200);  
    }


    public function changeProfile(AuthRequest $request)
    {
        try{
            $oldPassword = $request->old_password ?? null;
            $newPassword = $request->new_password ?? null;
            $name = $request->name ?? null;
            $phone = $request->phone ?? null;
            $user = $request->user();
            DB::beginTransaction();

            $data = [];
            if(isset($request->new_password)){
                if(!Hash::check($oldPassword, $user->password)){
                    return response()->json([
                        'status' => 422,
                        'message' => 'Old password is incorrect!',
                        'data' => []
                    ]);
                }
                $data['password'] = bcrypt($newPassword);
            }
            $data['name'] = $name;
            $data['phone'] = $phone;
            
            if($request->hasFile('image')){
                $file = $request->file('image');
                $data['img'] = $this->saveFile($file, 'uploads/profile', $user->img);
            }
            
            $this->model->find($user->id)->update($data);
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
            'message' => 'Update profile successfully',
            'data' => []
        ]);

    }

}
