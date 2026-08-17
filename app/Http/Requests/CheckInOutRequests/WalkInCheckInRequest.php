<?php

namespace App\Http\Requests\CheckInOutRequests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class WalkInCheckInRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'room_id'         => 'required|exists:rooms,id',
            'user_id'         => 'required|exists:users,id',
            'check_out_date'  => 'required|date|after:today',
            'num_guests'      => 'sometimes|integer|min:1',
        ];
    }

    public function messages()
    {
        return [
            'room_id.required'        => 'La habitación es requerida',
            'user_id.required'        => 'El huésped es requerido',
            'check_out_date.required' => 'La fecha de salida es requerida',
            'check_out_date.after'    => 'La fecha de salida debe ser posterior a hoy',
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
