<?php

namespace App\src\Event\Requests;

use App\Http\Requests\ApiRequest;

class EventCreationRequest extends ApiRequest
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
            'title' => 'required|string',
            'desc' => 'required|string',
            'content' => 'nullable|string',
            'image' => 'nullable|image',
        ];
    }
}
