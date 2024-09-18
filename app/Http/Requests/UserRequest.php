<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;


class UserRequest extends FormRequest
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
                    'status' => 'integer|between:0,1',
                    'perpage' => 'integer',
                ];
                break;
            case 'detail':
                $rules = [
                    'id' => 'integer|required|exists:users,id,role_id,2',
                ];
                break;
            case 'delete':
            case 'change-status':
                $rules = [
                    'id' =>'integer|required|exists:users,id,role_id,2',
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
