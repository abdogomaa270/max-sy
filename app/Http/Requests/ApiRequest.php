<?php

namespace App\Http\Requests;

use http\Client\Response;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;

abstract class ApiRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    abstract public function authorize();


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    abstract public function rules();

    //if the rules of validation was violated , this method will return a response

    protected function failedValidation(Validator $validator)
    {
        $errors= (new ValidationException($validator))->errors();
        if (!empty($errors))
        {
//            $ErrorList=[];
//            foreach ($errors as  $message)
//            {
//                $ErrorList=[
//                    "error"=>$message[0]
//
//                ];
//            }
            throw new HttpResponseException(
                response()->json(
                    [
                        'errors'=>$errors
                    ],
                    400
                )
            );
        }
    }

}
