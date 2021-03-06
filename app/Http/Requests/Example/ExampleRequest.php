<?php

namespace App\Http\Requests\Example;

use Illuminate\Foundation\Http\FormRequest;

class ExampleRequest extends FormRequest
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
     * @return array
     */
    public function rules()
    {
        return [
            'email' => [
                'email'
            ],
            'password' => [
                'required'
            ]
        ];
    }

    /**
     * @return array
     */
    public function attributes()
    {
        return parent::attributes(); // TODO: Change the autogenerated stub
    }
}
