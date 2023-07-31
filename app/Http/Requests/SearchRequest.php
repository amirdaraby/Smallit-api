<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;

class SearchRequest extends BaseRequest
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
        return [
            "search" => "required|string"
        ];
    }
    protected function failedValidation(Validator $validator)
    {
        return $this->failed($validator, 422);
    }
}
