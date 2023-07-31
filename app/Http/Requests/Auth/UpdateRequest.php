<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\BaseRequest;
use Illuminate\Support\Facades\Auth;

class UpdateRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            "name" => "required_without:email|string|max:255|min:2|",
            "email" => "string|email|unique:users,email,".Auth::id(),
            "password" => "string|min:5|required_unless:email,".Auth::user()->email.",null|current_password"
        ];
    }

    public function messages()
    {
        return [
            "required_unless" => "password is required on changing email",
            "required_without" => "update cannot perform with empty request"
        ];
    }
}
