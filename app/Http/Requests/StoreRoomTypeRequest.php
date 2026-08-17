<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator; 
use Illuminate\Http\Exceptions\HttpResponseException; 

class storeRoomTypeRequest extends FormRequest
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
            'type' => 'required',
            'price_per_night' => 'required',
            'capacity' => 'required',
            'description' => 'required'
        ];
    }

    public function messages(){
        return[
        'type.required' => "El campo tipo es requerido",
        'price_per_night.required' => 'El campo precio por noche es requerido',
        'description.required' => 'El campo descripcion es requerido' 
        ];
    }

    protected function failedValidation(Validator $validator){
        throw new HttpResponseException(response()->json([
            "message" => "Failed Validation",
            "errors" => $validator->errors()
        ], 422));
    }
}
