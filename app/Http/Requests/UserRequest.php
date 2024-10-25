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
        $action = explode('/', $this->route()->uri())[3];
        $rules = array();
        switch ($action) {
            case 'index':
                $rules = [
                    'search' => 'nullable|string|min:1|max:255',
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
                    'id' => 'integer|required|exists:users,id,role_id,2',
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
            'search.string' => 'Tìm kiếm phải là một chuỗi.',
            'search.min' => 'Tìm kiếm phải có ít nhất 1 ký tự.',
            'search.max' => 'Tìm kiếm không được vượt quá 255 ký tự.',
            'page.integer' => 'Trang phải là một số nguyên.',
            'status.integer' => 'Trạng thái phải là một số nguyên.',
            'status.between' => 'Trạng thái phải nằm trong khoảng 0 đến 1.',
            'perpage.integer' => 'Số lượng trên mỗi trang phải là một số nguyên.',
            'id.required' => 'ID là bắt buộc.',
            'id.integer' => 'ID phải là một số nguyên.',
            'id.exists' => 'Người dùng không tồn tại hoặc không có quyền truy cập.',
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