<?php

namespace App\Http\Requests\RoomRequests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreRoomRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'room_type_id' => 'required|exists:room_type,id',
            'room_number'  => 'required|integer|min:1',
            'room_floor'   => 'required|integer|min:0',
        ];
    }

    public function messages()
    {
        return [
            'room_type_id.required' => 'El tipo de habitación es requerido',
            'room_type_id.exists'   => 'El tipo de habitación no existe',
            'room_number.required'  => 'El número de habitación es requerido',
            'room_floor.required'   => 'El piso es requerido',
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