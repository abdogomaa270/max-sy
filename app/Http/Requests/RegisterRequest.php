<?php

namespace App\Http\Requests;

use App\Http\Requests\ApiRequest;
use Illuminate\Validation\Rule;

class RegisterRequest extends ApiRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|min:3|max:100',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ];
    }
//    public function messages()
//    {
//        return [
//            'name.required' => 'Please enter your name.',
//            'email.required' => 'Please enter your email address.',
//            'email.email' => 'Please enter a valid email address.',
//        ];
//    }
}
