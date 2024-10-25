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
        $action = explode('/', $this->route()->uri())[3];
        $rules = array();
        switch ($action) {
            case 'index':
                $rules = [
                    'search' => 'nullable|string|min:1|max:255',
                    'page' => 'integer',
                    'perpage' => 'integer',
                ];
                break;
            case 'detail':
                $rules = [
                    'id' => 'integer|required|exists:deliveries,id',
                ];
                break;
            case 'create':
                $rules = [
                    'name' => 'required|string|min:3|max:255|unique:deliveries',
                    'address' => 'required|string|max:255',
                    'phone' => 'required|regex:/(0)[0-9]{7}/|max:12|unique:deliveries',
                    'email' => 'required|email|unique:deliveries',
                    'image' => 'mimes:jpeg,jpg,png,gif,webp|required|max:10000',
                ];
                break;
            case 'edit':
                $rules = [
                    'id' => 'integer|required|exists:deliveries,id',
                    'name' => 'nullable|string|min:3|max:255|unique:deliveries',
                    'address' => 'nullable|string|max:255',
                    'phone' => 'nullable|regex:/(0)[0-9]{7}/|max:12|unique:deliveries',
                    'email' => 'nullable|email|unique:deliveries',
                    'image' => 'mimes:jpeg,jpg,png,gif,webp|nullable|max:10000',
                ];
                break;
            case 'delete':
                $rules = [
                    'id' => 'integer|required|exists:deliveries,id',
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
            'id.required' => 'ID là bắt buộc.',
            'id.integer' => 'ID phải là một số nguyên.',
            'id.exists' => 'Đơn hàng không tồn tại.',
            'name.required' => 'Tên là bắt buộc.',
            'name.string' => 'Tên phải là một chuỗi.',
            'name.min' => 'Tên phải có ít nhất 3 ký tự.',
            'name.max' => 'Tên không được vượt quá 255 ký tự.',
            'name.unique' => 'Tên đã tồn tại.',
            'address.required' => 'Địa chỉ là bắt buộc.',
            'address.string' => 'Địa chỉ phải là một chuỗi.',
            'address.max' => 'Địa chỉ không được vượt quá 255 ký tự.',
            'phone.required' => 'Số điện thoại là bắt buộc.',
            'phone.regex' => 'Số điện thoại không hợp lệ.',
            'phone.max' => 'Số điện thoại không được vượt quá 12 ký tự.',
            'phone.unique' => 'Số điện thoại đã tồn tại.',
            'email.required' => 'Email là bắt buộc.',
            'email.email' => 'Email không hợp lệ.',
            'email.unique' => 'Email đã tồn tại.',
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