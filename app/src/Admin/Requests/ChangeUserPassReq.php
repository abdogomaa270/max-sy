<?php

namespace App\src\Admin\Requests;

use App\Http\Requests\ApiRequest;

class ChangeUserPassReq extends ApiRequest
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
            'new_password' => 'required|string|min:8',
        ];
    }
}

