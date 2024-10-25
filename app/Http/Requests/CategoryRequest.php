<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class CategoryRequest extends FormRequest
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
                    'id' => 'integer|required|exists:categories,id',
                ];
                break;
            case 'create':
                $rules = [
                    'name' => 'required|string|min:3|max:255|unique:categories',
                    'image' => 'mimes:jpeg,jpg,png,gif,webp|required|max:10000',
                ];
                break;
            case 'edit':
                $rules = [
                    'id' => 'integer|required|exists:categories,id',
                    'name' => 'nullable|string|min:3|max:255|unique:categories,name,' . $this->id,
                    'image' => 'mimes:jpeg,jpg,png,gif,webp|nullable|max:10000',
                ];
                break;
            case 'delete':
                $rules = [
                    'id' => 'integer|required|exists:categories,id',
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
            'category_id.integer' => 'ID danh mục phải là một số nguyên.',
            'category_id.exists' => 'Danh mục không tồn tại.',
            'id.required' => 'ID là bắt buộc.',
            'id.integer' => 'ID phải là một số nguyên.',
            'id.exists' => 'Danh mục không tồn tại.',
            'name.required' => 'Tên danh mục là bắt buộc.',
            'name.string' => 'Tên danh mục phải là một chuỗi.',
            'name.min' => 'Tên danh mục phải có ít nhất 3 ký tự.',
            'name.max' => 'Tên danh mục không được vượt quá 255 ký tự.',
            'name.unique' => 'Tên danh mục đã tồn tại.',
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