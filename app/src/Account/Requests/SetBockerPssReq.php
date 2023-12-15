<?php

namespace App\src\Account\Requests;

use App\Http\Requests\ApiRequest;

class SetBockerPssReq extends ApiRequest
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
            'bocket_password' => 'required|string|min:8',
            'bocket_password_confirmation' => 'required|string|same:bocket_password',

        ];
    }

}
