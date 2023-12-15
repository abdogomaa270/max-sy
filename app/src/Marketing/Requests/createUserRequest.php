<?php

namespace App\src\Marketing\Requests;

use App\Http\Requests\ApiRequest;

class createUserRequest extends ApiRequest
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
            'name' => 'required|string',
            'nickname' => 'required|string',
            'father_name' => 'required|string',
            'mother_name' => 'required|string',
            'email' => 'nullable|email|unique:users,email',
            'gender' => 'required|string|in:male,female',
            'phone' => 'required|string|max:15',
            'inheritor' => 'required|string',
            'national_number' => 'required|string',
            'qid' => 'required|string',
            'amana' => 'required|string',
            'birth_country' => 'required|string',
            'birth_city' => 'required|string',
            'birth_street' => 'required|string',
            'b_day' => 'required|string',
            'b_month' => 'required|string',
            'b_year' => 'required|string',
            'm_day' => 'required|string',
            'm_month' => 'required|string',
            'm_year' => 'required|string',
            'address_country' => 'required|string',
            'address_city' => 'required|string',
            'address_street' => 'required|string',
            'shipping_country' => 'required|string',
            'shipping_city' => 'required|string',
            'shipping_street' => 'required|string',
            'identity_front' =>'required|image',
            'identity_back' =>'required|image',
            'healthDoc'=>'required|image',
            'product_id'=>'required|exists:products,id'
        ];
    }
    public function messages()
    {
        return [
            'product_id.required' => 'You should select a product.',
            'product_id.exists' => 'product not found',
        ];
    }

}
