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
use App\Models\User;
use App\Repositories\Home\HomeRepositoryInterface;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

use function PHPUnit\Framework\isEmpty;

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
                // dd($order_data);
                FeatureProduct::find($cart->feature_product_id)->update(['quantity' => $product->quantity - $cart->quantity]);
                ProductBill::create($order_data);
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
            $addresses = Address::where('user_id', $request->user()->id)->pluck('id');
            $data = array();
            foreach($addresses as $address){
                $bill = Bill::where('address_id', $address)->orderBy('created_at', 'DESC')->get();
                if (!$bill->isEmpty()) {
                    $data[] = $bill;   
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
            'data' => $data[0]
        ]);
    }

}
