<?php

namespace App\Http\Requests\Qualifications;

use Illuminate\Foundation\Http\FormRequest;

class EditQualificationRequest extends FormRequest
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
            'user' => 'required|numeric|exists:users,id',
            'name' => 'required|numeric',
            'date' => 'required|date',
            'description' => 'nullable|string'
        ];
    }
}
