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
        $action =explode('/', $this->route()->Uri())[3];
        $rules = array();
        switch($action) {
            case 'index':
                $rules = [
                    'category_id' => 'nullable|integer|exists:categories,id',
                    'search' =>'nullable|string|min:1|max:255',
                    'page' => 'integer',
                    'perpage' => 'integer',
                ];
                break;
            case 'detail':
            case 'delete':
                $rules = [
                    'id' =>'required|exists:products,id',
                ];
                break;
            case 'create':
                $rules = [
                    'category_id' => 'required|integer|exists:categories,id',
                    'cost_price' =>'required|integer|min:0',
                    'selling_price' =>'required|integer|min:' . request()->input('cost_price') + 1,
                    'name' =>'required|string|min:3|max:255|unique:products',
                    'description' =>'required',
                    'image' =>'mimes:jpeg,jpg,png,gif|required|max:10000',
                ];
                break;
            case 'edit':
                $rules = [
                    'id' =>'integer|required|exists:products,id',
                    'category_id' => 'nullable|integer|exists:categories,id',
                    'cost_price' =>'nullable|integer|min:0',
                    'selling_price' =>'nullable|integer|min:' . request()->input('cost_price') + 1,
                    'name' =>'nullable|string|min:3|max:255|unique:products',
                    'description' =>'nullable',
                    'image' =>'mimes:jpeg,jpg,png,gif|nullable|max:10000',
                ];
                break;
            case 'detail-child':
            case 'delete-child':
                $rules = [
                    'id' =>'required|exists:feature_products,id',
                ];
                break;
            case 'create-child':
                $rules = [
                    'product_id' =>'required|exists:products,id',
                    'feature_name' =>'required|string|min:3|max:255|unique:feature_products',
                    'cost_price' =>'required|integer|min:0',
                    'selling_price' =>'required|integer|min:' . request()->input('cost_price') + 1,
                    'quantity' =>'required|integer|min:0',
                    'image' =>'mimes:jpeg,jpg,png,gif|required|max:10000',
                ];
                break;
            case 'edit-child':
                $rules = [
                    'id' =>'required|exists:feature_products,id',
                    'product_id' =>'nullable|exists:products,id',
                    'feature_name' =>'nullable|string|min:3|max:255|unique:feature_products',
                    'cost_price' =>'nullable|integer|min:0',
                    'selling_price' =>'nullable|integer|min:' . request()->input('cost_price') + 1,
                    'quantity' =>'nullable|integer|min:0',
                    'image' =>'mimes:jpeg,jpg,png,gif|nullable|max:10000',
                ];
                break;

        }
        return $rules;
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
