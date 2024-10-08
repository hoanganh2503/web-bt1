<?php

namespace App\Repositories\Home;

use App\Http\Requests\HomeRequest;
use App\Models\Address;
use App\Models\Bill;
use App\Models\Category;
use App\Models\FeatureHome;
use App\Models\FeatureProduct;
use App\Models\Home;
use App\Models\Product;
use App\Models\ProductBill;
use App\Models\ProductCart;
use App\Models\Sell;
use App\Models\User;
use App\Repositories\Home\HomeRepositoryInterface;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

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
            $products = Product::select('products.*')
                ->leftJoin('sells', 'products.id', '=', 'sells.product_id')
                ->orderBy('sells.quantity', 'desc')
                ->get()->unique('id');
            $products = array_slice($products->values()->all(), 0, 5, true);

            foreach($products as $item){
                if(!empty($item['img'])){
                    $item['img'] = asset('storage/'.$item['img']);
                }
            }

            $flashSale = Product::select('products.*')
                ->leftJoin('sells', 'products.id', '=', 'sells.product_id')
                ->orderBy('sells.quantity', 'asc')
                ->get()->unique('id');
            $flashSale = array_slice($flashSale->values()->all(), 0, 5, true);

            foreach($flashSale as $item){
                $item['sale_price'] = $item['selling_price'] * 0.8;
                if(!empty($item['img'])){
                    $item['img'] = asset('storage/'.$item['img']);
                }
            }
            $data['categories'] = $categories;
            $data['newest_product'] = $product; 
            $data['best_sell'] = $products; 
            $data['flash_sale'] = $flashSale;         
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
            
            $currentProduct = $this->product->with('listChild')->find($id);
            if(!empty($currentProduct['img'])){
                $currentProduct['img'] = asset('storage/'.$currentProduct['img']);
            }
            $currentProduct->listChild->map(function ($item) {
                if (!empty($item['img'])) {
                    $item['img'] = asset('storage/' . $item['img']);
                }
            });

            $data['currentProduct'] = $currentProduct;
            $relatedProducts = $this->product
            ->where('category_id', $currentProduct->category_id)
            ->where('id', '<>', $currentProduct->id)
            ->get();
            foreach($relatedProducts as $item){
                if(!empty($item['img'])){
                    $item['img'] = asset('storage/'.$item['img']);
                }
            }
            $data['relatedProducts'] = $relatedProducts;
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
            $products = ProductCart::where('user_id', $user_id)->with('featureProduct')->orderBy('created_at' ,'desc')->get();
            $total_price = 0;
            foreach ($products as $item) {
                    $item->featureProduct->img = asset('storage/' . $item->featureProduct->img);
                    $item->featureProduct->total = $item->quantity * $item->featureProduct->selling_price;
                    
                    $total_price += $item->quantity * $item->featureProduct->selling_price;
            }
            $data['products'] = $products;
            $data['total_price'] = $total_price;
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

    public function profile(HomeRequest $request){
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

    public function changeProfile(HomeRequest $request)
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
            
            User::find($user->id)->update($data);
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

    public function getListAddresses(HomeRequest $request)
    {
        try{
            $data = Address::where('user_id', $request->user()->id)->orderBy('created_at', 'DESC')->get();
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

    public function getDetailAddress($id){
        try{
            $data = Address::find($id);
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

    public function addAddress(HomeRequest $request){
        $data = $request->only('name', 'detail_address', 'phone', 'ward_id');
        $data['user_id'] = $request->user()->id;

        try{
            DB::beginTransaction();
            $data['created_at'] = time();
            $data['updated_at'] = time();
            $res = Address::create($data);

        }catch(\Exception $e){
            DB::rollBack();
            return response()->json([
                'status' => 500,
                'message' => $e->getMessage(),
                'data' => []
            ]);
        }
        DB::commit();
        return $this->getDetailAddress($res->id);
    }

    public function updateAddress(HomeRequest $request){
        $id = $request->id;
        $data = $request->only('name', 'detail_address', 'phone', 'ward_id');
        $data['user_id'] = $request->user()->id;
        $address = Address::find($id);
        try{
            DB::beginTransaction();
            $data['updated_at'] = time();
            $address->update($data);

        }catch(\Exception $e){
            DB::rollBack();
            return response()->json([
                'status' => 500,
                'message' => $e->getMessage(),
                'data' => []
            ]);
        }
        DB::commit();
        return $this->getDetailAddress($id);
    }

    public function deleteAddress($id){
        try{
            DB::beginTransaction();
            Address::find($id)->delete();
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

    public function checkout(HomeRequest $request){
        try{
            $data = array();
            $user_id = $request->user()->id;
            $data['products'] =  ProductCart::where('user_id', $user_id)->get();
            $total = 0;
            foreach($data['products'] as $cart){
                $product = FeatureProduct::find($cart->feature_product_id);
                $cart['feature_name'] = $product->feature_name;
                $cart['price'] = $product->selling_price * $cart->quantity;
                $cart['img'] = asset('storage/'.$product['img']);
                $total += $product->selling_price * $cart->quantity;
            }
            $data['address'] = Address::where('user_id', $user_id)->get();    
            $data['total'] = $total;
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

    public function order(HomeRequest $request){
        try{
            DB::beginTransaction();
            $user_id = $request->user()->id;
            $products =  ProductCart::where('user_id', $user_id)->get();
            if ($products->isEmpty()) {
                return response()->json([
                   'status' => 400,
                   'message' => 'No product in cart',
                   'data' => []
                ]);
            }
            $data = $request->only('address_id', 'delivery_id', 'note', 'total_price');
            $data['payment_status'] = 0;
            $data['status'] = 0;
            $data['created_at'] = time();
            $data['updated_at'] = time();
            $id = Bill::create($data)->id;

            foreach($products as $cart){
                $product = FeatureProduct::find($cart->feature_product_id);
                if($cart->quantity > $product -> quantity){
                    return response()->json([
                        'status' => 400,
                        'message' => 'Not enough stock for product '. $product->feature_name,
                        'data' => []
                    ]);
                }
                $order_data = [
                    'bill_id' => $id,
                    'feature_product_id' => $cart->feature_product_id,
                    'quantity' => $cart->quantity,
                    'price' => $product->selling_price,
                    'created_at' => time(),
                    'updated_at' => time()
                ];

                FeatureProduct::find($cart->feature_product_id)->update(['quantity' => $product->quantity - $cart->quantity]);
                ProductBill::create($order_data);

                $parentProduct = Sell::where('product_id', $product->product_id)->first();
                if(empty($parentProduct)){
                    Sell::create([
                        'product_id' => $product->product_id,
                        'quantity' => $cart->quantity
                    ]);
                }else{
                    Sell::where('product_id', $product->product_id)->update(['quantity' => $cart->quantity + $parentProduct->quantity]);
                }
            }
            ProductCart::where('user_id', $user_id)->delete();
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

    public function orderHistory(HomeRequest $request){
        try{ 
            $data = Bill::orderBy('created_at', 'desc')->with('address')->get();      
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

    public function orderDetail(HomeRequest $request){
        try{
            $data = Bill::where('id', $request->id)->with(['product', 'address'])->first();
            foreach($data->product as $item){
                $feature_product_id = $item->feature_product_id;
                $feature_product = FeatureProduct::find($feature_product_id);
                $item->feature_name = $feature_product->feature_name;
                if($feature_product->img){
                    $item->img = asset('storage/'.$feature_product->img);
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

    public function changeStatus($id){
        try{
            Bill::find($id)->update(['status' => 5]);
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
