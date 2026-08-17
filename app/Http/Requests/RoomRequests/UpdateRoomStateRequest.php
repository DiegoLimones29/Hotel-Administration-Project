<?php

namespace App\Http\Requests\RoomRequests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateRoomStateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'state' => 'required|in:available,on maintenance,out of service',
        ];
    }

    public function messages()
    {
        return [
            'state.required' => 'El estado es requerido',
            'state.in' => 'Estado no válido. Usa: available, on maintenance u out of service',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            "message" => "Failed Validation",
            "errors" => $validator->errors()
        ], 422));
    }
}
