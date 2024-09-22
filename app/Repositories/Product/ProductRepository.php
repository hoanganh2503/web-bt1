<?php

namespace App\Repositories\Product;

use App\Http\Requests\ProductRequest;
use App\Models\FeatureProduct;
use App\Models\Product;
use App\Repositories\Product\ProductRepositoryInterface;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;


/**
 * Class ProductRepository.
 */
class ProductRepository extends BaseRepository implements ProductRepositoryInterface
{
    private $featureProduct;
    private $product;

    public function __construct(FeatureProduct $featureProduct, Product $product){
        $this->featureProduct = $featureProduct;
        $this->product = $product;
    }
    
    public function getModel()
    {
        return Product::class;
    }

    public function getListProducts(ProductRequest $request, $perpage = 10){
        $search = $request->search ?? null;
        $perpage = $request->perpage ?? $perpage;
        $category_id = $request->category_id ?? null;
        try{
            $data = $this->product->when($search, function($query, $search){
                    $query->where('name', 'like', '%'.$search.'%');
                })
                ->when(isset($request->category_id), function($query) use ($category_id){
                    $query->where('category_id', $category_id);
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

    public function getDetailProduct($id){
        try{
            
            $data = $this->product->with('listChild')->find($id);
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

    public function addProduct(ProductRequest $request){
        $data = $request->only('name', 'description', 'category_id', 'cost_price', 'selling_price');
        try{
            DB::beginTransaction();
            $data['created_at'] = time();
            $data['updated_at'] = time();
            if($request->hasFile('image')){
                $file = $request->file('image');
                $data['img'] = $this->saveFile($file, 'uploads/products');
            }
            $res = $this->product->create($data);

        }catch(\Exception $e){
            DB::rollBack();
            return response()->json([
                'status' => 500,
                'message' => $e->getMessage(),
                'data' => []
            ]);
        }
        DB::commit();
        return $this->getDetailProduct($res->id);
    }

    public function updateProduct(ProductRequest $request){
        $id = $request->id;
        $data = $request->only('name', 'category_id', 'description', 'cost_price', 'selling_price');
        $product = $this->product->find($id);
        try{
            DB::beginTransaction();
            $data['updated_at'] = time();
            if($request->hasFile('image')){
                $file = $request->file('image');
                $data['img'] = $this->saveFile($file, 'uploads/products', $product->img);
            }
            $product->update($data);

        }catch(\Exception $e){
            DB::rollBack();
            return response()->json([
                'status' => 500,
                'message' => $e->getMessage(),
                'data' => []
            ]);
        }
        DB::commit();
        return $this->getDetailProduct($id);
    }

    public function deleteProduct($id){
        try{
            DB::beginTransaction();
            $product = $this->product->find($id);
            if(!empty($product->img)){
                $this->deleteFile($product->img);
            }
            $listChild = $product->listChild;
            foreach($listChild as $child){
                $this->deleteFeatureProduct($child->id);
            }
            $product->delete();
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

    public function getDetailChild($id){
        try{
            $data = $this->featureProduct->find($id);
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

    public function addFeatureProduct(ProductRequest $request){
        $data = $request->only('product_id', 'feature_name', 'cost_price', 'selling_price', 'quantity');
        try{
            DB::beginTransaction();
            $data['created_at'] = time();
            $data['updated_at'] = time();
            if($request->hasFile('image')){
                $file = $request->file('image');
                $data['img'] = $this->saveFile($file, 'uploads/products');
            }
            $res = $this->featureProduct->create($data);

        }catch(\Exception $e){
            DB::rollBack();
            return response()->json([
                'status' => 500,
                'message' => $e->getMessage(),
                'data' => []
            ]);
        }
        DB::commit();
        return $this->getDetailChild($res->id);
    }

    public function editFeatureProduct(ProductRequest $request){
        $id = $request->id;
        $data = $request->only('product_id', 'feature_name', 'cost_price', 'selling_price', 'quantity');
        try{
            DB::beginTransaction();
            $data['updated_at'] = time();
            if($request->hasFile('image')){
                $file = $request->file('image');
                $data['img'] = $this->saveFile($file, 'uploads/products');
            }
            $this->featureProduct->find($id)->update($data);

        }catch(\Exception $e){
            DB::rollBack();
            return response()->json([
                'status' => 500,
                'message' => $e->getMessage(),
                'data' => []
            ]);
        }
        DB::commit();
        return $this->getDetailChild($id);
    }

    public function deleteFeatureProduct($id){
        try{
            DB::beginTransaction();
            $featureProduct = $this->featureProduct->find($id);
            if(!empty($featureProduct->img)){
                $this->deleteFile($featureProduct->img);
            }
            $featureProduct->delete();
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
