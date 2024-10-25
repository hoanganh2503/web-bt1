<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class HomeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {   
        $uri = explode('/', $this->route()->uri());
        $action = $uri[1];
        $rules = array();
        switch ($action) {
            case 'product':
                $rules = [
                    'product_id' => 'required|integer|exists:products,id',
                ];
                break;
            case 'add-to-cart':
                $rules = [
                    'feature_product_id' => 'required|exists:feature_products,id',
                    'quantity' => 'required|integer|min:1',
                ];
                break;
            case 'change-profile':
                $rules = [
                    'new_password' => 'nullable|string|min:6|max:255',
                    'phone' => 'nullable|regex:/(0)[0-9]{9}/',
                    'name' => 'nullable|string|min:3|max:255',
                    'image' => 'mimes:jpeg,jpg,png,gif,webp|nullable|max:10000',
                ];
                break;
            case 'detail-address':
            case 'delete-address':
                $rules = [
                    'id' => 'required|integer|exists:address,id',
                ];
                break;
            case 'create-address':
                $rules = [
                    'name' => 'required|string|min:3|max:255',
                    'detail_address' => 'required|string|max:255',
                    'phone' => 'required|regex:/(0)[0-9]{7}/|max:12',
                    'ward_id' => 'required|regex:/[0-9]{5}/|max:5',
                ];
                break;
            case 'edit-address':
                $rules = [
                    'id' => 'integer|required|exists:address,id',
                    'name' => 'nullable|string|min:3|max:255',
                    'detail_address' => 'nullable|string|max:255',
                    'phone' => 'nullable|regex:/(0)[0-9]{7}/|max:12',
                    'ward_id' => 'nullable|regex:/[0-9]{5}/|max:5',
                ];
                break;
            case 'order':
                $rules = [
                    'delivery_id' => 'required|integer|exists:deliveries,id',
                    'address_id' => 'required|exists:address,id',
                    'total_price' => 'required|integer',
                ];
                break;
            case 'order-detail':
            case 'change-status':
                $rules = [
                    'id' => 'required|integer|exists:bills,id',
                ];
                break;            
        } 

        return $rules;
    }

    /**
     * Custom messages for validation errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'product_id.required' => 'ID sản phẩm là bắt buộc.',
            'product_id.integer' => 'ID sản phẩm phải là một số nguyên.',
            'product_id.exists' => 'Sản phẩm không tồn tại.',
            'feature_product_id.required' => 'ID sản phẩm đặc biệt là bắt buộc.',
            'feature_product_id.exists' => 'Sản phẩm đặc biệt không tồn tại.',
            'quantity.required' => 'Số lượng là bắt buộc.',
            'quantity.integer' => 'Số lượng phải là một số nguyên.',
            'quantity.min' => 'Số lượng phải lớn hơn hoặc bằng 1.',
            'new_password.min' => 'Mật khẩu mới phải có ít nhất 6 ký tự.',
            'phone.regex' => 'Số điện thoại không hợp lệ.',
            'name.min' => 'Tên phải có ít nhất 3 ký tự.',
            'id.required' => 'ID là bắt buộc.',
            'id.integer' => 'ID phải là một số nguyên.',
            'id.exists' => 'Địa chỉ không tồn tại.',
            'detail_address.required' => 'Địa chỉ chi tiết là bắt buộc.',
            'ward_id.required' => 'ID phường là bắt buộc.',
            'ward_id.regex' => 'ID phường không hợp lệ.',
            'total_price.required' => 'Giá tổng là bắt buộc.',
            'total_price.integer' => 'Giá tổng phải là một số nguyên.',
            'image.mimes' => 'Hình ảnh phải có định dạng jpeg, jpg, png, gif hoặc webp.',
            'image.required' => 'Hình ảnh là bắt buộc.',
            'image.max' => 'Hình ảnh không được vượt quá 10MB.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors();
    
        $response = response()->json([
            'status' => 422,
            'message' => $errors->messages(),
            'data' => []
        ]);
    
        throw new HttpResponseException($response);
    }
}