<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;


class DeliveryRequest extends FormRequest
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
                    'search' =>'nullable|string|min:1|max:255',
                    'page' => 'integer',
                    'perpage' => 'integer',
                ];
                break;
            case 'detail':
                $rules = [
                    'id' =>'integer|required|exists:deliveries,id',
                ];
                break;
            case 'create':
                $rules = [
                    'name' =>'required|string|min:3|max:255|unique:deliveries',
                    'address' =>'required|string|max:255',
                    'phone' =>'required|regex:/(0)[0-9]{7}/|max:12|unique:deliveries',
                    'email' =>'required|email|unique:deliveries',
                    'image' =>'mimes:jpeg,jpg,png,gif,webp|required|max:10000',
                ];
                break;
            case 'edit':
                $rules = [
                    'id' =>'integer|required|exists:deliveries,id',
                    'name' =>'nullable|string|min:3|max:255|unique:deliveries',
                    'address' =>'nullable|string|max:255',
                    'phone' =>'nullable|regex:/(0)[0-9]{7}/|max:12|unique:deliveries',
                    'email' =>'nullable|email|unique:deliveries',
                    'image' =>'mimes:jpeg,jpg,png,gif,webp|nullable|max:10000',
                ];
                break;
            case 'delete':
                $rules = [
                    'id' =>'integer|required|exists:deliveries,id',
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
