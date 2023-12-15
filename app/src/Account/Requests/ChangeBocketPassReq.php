<?php

namespace App\src\Account\Requests;

use App\Http\Requests\ApiRequest;

class ChangeBocketPassReq extends ApiRequest
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
            'current_bocket_password' => 'required|string',
            'new_bocket_password' => 'required|string|min:8',
            'bocket_password_confirmation' => 'required|string|same:new_bocket_password',

        ];
    }

}
