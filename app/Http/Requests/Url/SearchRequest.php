<?php

namespace App\Http\Requests\Url;

use App\Http\Requests\BaseRequest;
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
            "q" => "required|string|min:3"
        ];
    }
    protected function failedValidation(Validator $validator)
    {
        return $this->failed($validator, 422);
    }
}
