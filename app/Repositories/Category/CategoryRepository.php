<?php

namespace App\Repositories\Category;

use App\Http\Requests\CategoryRequest;
use App\Repositories\Category\CategoryRepositoryInterface;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * Class CategoryRepository.
 */
class CategoryRepository extends BaseRepository implements CategoryRepositoryInterface
{
    public function getModel()
    {
        return \App\Models\Category::class;
    }

    public function getListCategories(CategoryRequest $request, $perpage = 10){
        $search = $request->search ?? null;
        $perpage = $request->perpage ?? $perpage;

        try{
            $data = $this->model->when($search, function($query, $search){
                    $query->where('name', 'like', '%'.$search.'%');
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

    public function getDetailCategory($id){
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

    public function addCategory(CategoryRequest $request){
        $name = $request->name ?? null;
        try{
            DB::beginTransaction();
            $data['name'] = $name;
            $data['created_at'] = time();
            $data['updated_at'] = time();
            if($request->hasFile('image')){
                $file = $request->file('image');
                $data['img'] = $this->saveFile($file, 'uploads/categories');
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
        return $this->getDetailCategory($res->id);
    }

    public function updateCategory(CategoryRequest $request){
        $id = $request->id;
        $data = $request->only('name');
        $category = $this->model->find($id);
        try{
            DB::beginTransaction();
            $data['updated_at'] = time();
            if($request->hasFile('image')){
                $file = $request->file('image');
                $data['img'] = $this->saveFile($file, 'uploads/categories', $category->img);
            }
            $category->update($data);

        }catch(\Exception $e){
            DB::rollBack();
            return response()->json([
                'status' => 500,
                'message' => $e->getMessage(),
                'data' => []
            ]);
        }
        DB::commit();
        return $this->getDetailCategory($id);
    }

    public function deleteCategory($id){
        try{
            DB::beginTransaction();
            $category = $this->model->find($id);
            if(!empty($category->img)){
                $this->deleteFile($category->img);
            }
            $category->delete();
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
