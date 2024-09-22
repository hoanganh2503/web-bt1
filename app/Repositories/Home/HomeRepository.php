<?php

namespace App\Repositories\Home;

use App\Http\Requests\HomeRequest;
use App\Models\Category;
use App\Models\FeatureHome;
use App\Models\FeatureProduct;
use App\Models\Home;
use App\Models\Product;
use App\Models\ProductCart;
use App\Repositories\Home\HomeRepositoryInterface;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;

/**
 * Class HomeRepository.
 */
class HomeRepository extends BaseRepository implements HomeRepositoryInterface
{ 
    private $categories;
    private $product;
    public function __construct(
        Category $categories,
        Product $product
    ){
        $this->categories = $categories;
        $this->product = $product;  
    }
    public function getModel()
    {
        return Product::class;
    }

    public function home(HomeRequest $request){
        $search = $request->search?? null;
        try{
            $categories = $this->categories->orderBy('created_at', 'desc')->take(6)->get();
            foreach($categories as $item){
                if(!empty($item['img'])){
                    $item['img'] = asset('storage/'.$item['img']);
                }
            }
            $product = $this->product
                ->when($search, function($query, $search){
                    $query->where('name', 'like', '%'.$search.'%');
                })->orderBy('created_at', 'desc')->get(); 
            foreach($product as $item){
                if(!empty($item['img'])){
                    $item['img'] = asset('storage/'.$item['img']);
                }
            }
            $data['categories'] = $categories;
            $data['newest_product'] = $product;           
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

    public function product($id){
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

    public function addToCart(HomeRequest $request){
        try{
            DB::begintransaction();
            $feature_product_id = $request->feature_product_id;
            $data = $request->only('feature_product_id', 'quantity');
            $cart = ProductCart::where('feature_product_id', $feature_product_id)->where('user_id', $request->user()->id)->first();
            $feature_product = FeatureProduct::find($feature_product_id);

            if ($feature_product->quantity < $request->quantity) {
                DB::rollBack();
                return response()->json([
                    'status' => 400,
                    'message' => 'Insufficient stock to fulfill the request.',
                    'data' => []
                ]);
            }
            if($cart){
                $cart->update([
                    'quantity' => $cart->quantity + $request->quantity,
                    'updated_at' => time(),
                ]);
            }else{
                $data['created_at'] = time();
                $data['updated_at'] = time();
                $data['user_id'] = $request->user()->id;
                ProductCart::create($data);                
            }

            
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

    public function cart(HomeRequest $request){
        try{
            $user_id = $request->user()->id;
            $data = ProductCart::where('user_id', $user_id)->orderBy('created_at' ,'desc')->get();
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
