<?php

namespace App\src\Account\Requests;

use App\Http\Requests\ApiRequest;

class TransfereMoneyReq extends ApiRequest
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
            'reciever_id' => 'required|string',
            'bocketPassword' => 'required|string',
            'amount' => 'required|integer|min:1',

        ];
    }

}
