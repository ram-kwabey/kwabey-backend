<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCategoryRequest extends FormRequest
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
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|min:2',
            'figure_id' => 'required',
            'description' => 'sometimes',
            'slug' => 'sometimes',
            'code' => 'sometimes',
            'image' => 'sometimes',
            'sequence' => 'sometimes',
            'type' => 'sometimes',
            'status' => 'sometimes',            
        ];
    }
}