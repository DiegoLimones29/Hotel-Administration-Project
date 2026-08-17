<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException; 

use Override;

class UpdateRoomType extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'type' => 'sometimes',
            'price_per_night' => 'sometimes',
            'capacity' => 'sometimes',
            'description' => 'sometimes'
        ];
    }

    public function messages()
    {
        return [
            'type.required' => "El campo tipo es requerido",
            'price_per_night.required' => 'El campo precio por noche es requerido',
            'description.required' => 'El campo descripcion es requerido' 
        ];
    }

    
    protected function failedValidation(Validator $validator){
        throw new HttpResponseException(response() -> json([
            "message" => "validacion fallida",
            "errors" => $validator->errors()
        ]));
    }
}
