<?php

namespace App\Http\Requests\Qualifications;

use Illuminate\Foundation\Http\FormRequest;

class AllQualificationRequest extends FormRequest
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
            'filterUser' => 'nullable|numeric|exists:users,id',
            'filterFrom' => 'nullable|date',
            'filterTo' => 'nullable|date',
            'filterName' => 'nullable|string',
            'sort' => 'nullable|array'
        ];
    }
}
