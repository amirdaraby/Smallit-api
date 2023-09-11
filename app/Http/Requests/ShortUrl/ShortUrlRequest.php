<?php

namespace App\Http\Requests\ShortUrl;

use App\Http\Requests\BaseRequest;
use Illuminate\Contracts\Validation\Validator;


class ShortUrlRequest extends BaseRequest
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
            "url" => ["required", "url", "regex: /^(http:\/\/www\.|https:\/\/www\.|http:\/\/|https:\/\/)?[A-Z-a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}(:[0-9]{1,5})?(\/.*)?$/"],
            "amount" => ["required", "int", "between:1,100000", "integer"],
            "batch_name" => ["string", "between:1,255"]
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        return $this->failed($validator, 422);
    }
}
