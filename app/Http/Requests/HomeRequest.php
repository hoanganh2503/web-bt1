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
        $uri = explode('/', $this->route()->Uri());
        $action =$uri[1];
        $rules = array();
        switch($action) {
            case 'product':
                $rules = [
                    'product_id' =>'required|integer|exists:products,id',
                ];
                break;
            case 'add-to-cart':
                $rules = [
                    'feature_product_id' =>'required|exists:feature_products,id',
                    'quantity' =>'required|integer|min:1',
                ];
                break;
            case 'change-profile':
                $rules = [
                    'old_password' =>'nullable|string|min:6|max:255',
                    'new_password' =>'nullable|string|min:6|max:255',
                    'phone' => 'nullable|regex:/(0)[0-9]{9}/',
                    'name' =>'nullable|string|min:3|max:255',
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
