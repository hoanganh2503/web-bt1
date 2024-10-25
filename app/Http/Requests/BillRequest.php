<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class BillRequest extends FormRequest
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
                    'category_id' => 'nullable|integer|exists:categories,id',
                    'page' => 'integer',
                    'perpage' => 'integer',
                ];
                break;
            case 'detail':
                $rules = [
                    'id' => 'integer|required|exists:bills,id',
                ];
                break;
            case 'change-status':
                $rules = [
                    'bill_id' => 'integer|required|exists:bills,id',
                    'status' => 'integer|required|min:0|max:4'
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
            'category_id.integer' => 'Danh mục phải là một số nguyên.',
            'category_id.exists' => 'Danh mục không tồn tại.',
            'id.required' => 'ID là bắt buộc.',
            'id.integer' => 'ID phải là một số nguyên.',
            'id.exists' => 'Hóa đơn không tồn tại.',
            'bill_id.required' => 'ID hóa đơn là bắt buộc.',
            'bill_id.integer' => 'ID hóa đơn phải là một số nguyên.',
            'status.required' => 'Trạng thái là bắt buộc.',
            'status.integer' => 'Trạng thái phải là một số nguyên.',
            'status.min' => 'Trạng thái phải lớn hơn hoặc bằng 0.',
            'status.max' => 'Trạng thái phải nhỏ hơn hoặc bằng 4.',
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