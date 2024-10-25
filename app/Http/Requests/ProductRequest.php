<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class ProductRequest extends FormRequest
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
        $action = explode('/', $this->route()->uri())[3];
        $rules = array();
        switch ($action) {
            case 'index':
                $rules = [
                    'category_id' => 'nullable|integer|exists:categories,id',
                    'search' => 'nullable|string|min:1|max:255',
                    'page' => 'integer',
                    'perpage' => 'integer',
                ];
                break;
            case 'detail':
            case 'delete':
                $rules = [
                    'id' => 'required|exists:products,id',
                ];
                break;
            case 'create':
                $rules = [
                    'category_id' => 'required|integer|exists:categories,id',
                    'cost_price' => 'required|integer|min:0',
                    'selling_price' => 'required|integer|min:' . (request()->input('cost_price') + 1),
                    'name' => 'required|string|min:1|max:255|unique:products',
                    'description' => 'required',
                    'image' => 'mimes:jpeg,jpg,png,gif,webp|required|max:10000',
                ];
                break;
            case 'edit':
                $rules = [
                    'id' => 'integer|required|exists:products,id',
                    'category_id' => 'nullable|integer|exists:categories,id',
                    'cost_price' => 'nullable|integer|min:0',
                    'selling_price' => 'nullable|integer|min:' . (request()->input('cost_price') + 1),
                    'name' => 'nullable|string|min:3|max:255|unique:products,name,' . $this->id,
                    'description' => 'nullable',
                    'image' => 'mimes:jpeg,jpg,png,gif,webp|nullable|max:10000',
                ];
                break;
            case 'detail-child':
            case 'delete-child':
                $rules = [
                    'id' => 'required|exists:feature_products,id',
                ];
                break;
            case 'create-child':
                $rules = [
                    'product_id' => 'required|exists:products,id',
                    'feature_name' => 'required|string|min:1|max:255',
                    'cost_price' => 'required|integer|min:0',
                    'selling_price' => 'required|integer|min:' . (request()->input('cost_price') + 1),
                    'quantity' => 'required|integer|min:0',
                    'image' => 'mimes:jpeg,jpg,png,gif,webp|required|max:10000',
                ];
                break;
            case 'edit-child':
                $rules = [
                    'id' => 'required|exists:feature_products,id',
                    'product_id' => 'nullable|exists:products,id',
                    'feature_name' => 'nullable|string|min:3|max:255',
                    'cost_price' => 'nullable|integer|min:0',
                    'selling_price' => 'nullable|integer|min:' . (request()->input('cost_price') + 1),
                    'quantity' => 'nullable|integer|min:0',
                    'image' => 'mimes:jpeg,jpg,png,gif,webp|nullable|max:10000',
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
            'id.required' => 'ID là bắt buộc.',
            'id.exists' => 'Sản phẩm không tồn tại.',
            'category_id.required' => 'ID danh mục là bắt buộc.',
            'category_id.integer' => 'ID danh mục phải là một số nguyên.',
            'category_id.exists' => 'Danh mục không tồn tại.',
            'cost_price.required' => 'Giá gốc là bắt buộc.',
            'cost_price.integer' => 'Giá gốc phải là một số nguyên.',
            'cost_price.min' => 'Giá gốc không được nhỏ hơn 0.',
            'selling_price.required' => 'Giá bán là bắt buộc.',
            'selling_price.integer' => 'Giá bán phải là một số nguyên.',
            'selling_price.min' => 'Giá bán phải lớn hơn giá gốc.',
            'name.required' => 'Tên sản phẩm là bắt buộc.',
            'name.string' => 'Tên sản phẩm phải là một chuỗi.',
            'name.min' => 'Tên sản phẩm phải có ít nhất 1 ký tự.',
            'name.max' => 'Tên sản phẩm không được vượt quá 255 ký tự.',
            'name.unique' => 'Tên sản phẩm đã tồn tại.',
            'description.required' => 'Mô tả là bắt buộc.',
            'image.mimes' => 'Hình ảnh phải có định dạng jpeg, jpg, png, gif hoặc webp.',
            'image.required' => 'Hình ảnh là bắt buộc.',
            'image.max' => 'Hình ảnh không được vượt quá 10MB.',
            'feature_name.required' => 'Tên đặc trưng là bắt buộc.',
            'feature_name.string' => 'Tên đặc trưng phải là một chuỗi.',
            'feature_name.min' => 'Tên đặc trưng phải có ít nhất 1 ký tự.',
            'feature_name.max' => 'Tên đặc trưng không được vượt quá 255 ký tự.',
            'quantity.required' => 'Số lượng là bắt buộc.',
            'quantity.integer' => 'Số lượng phải là một số nguyên.',
            'quantity.min' => 'Số lượng không được nhỏ hơn 0.',
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