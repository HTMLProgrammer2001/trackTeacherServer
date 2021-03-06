<?php

namespace App\Http\Requests\Publication;

use Illuminate\Foundation\Http\FormRequest;

class AllPublicationRequest extends FormRequest
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
            'filterUser' => 'nullable|numeric',
            'filterName' => 'nullable|string',
            'filterFrom' => 'nullable|date',
            'filterTo' => 'nullable|date',
            'rules' => 'nullable|array'
        ];
    }
}
