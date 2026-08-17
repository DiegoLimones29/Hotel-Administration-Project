<?php

namespace App\Http\Requests\ReservationRequests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreReservationRequest extends FormRequest
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
            'check_in_date'   => 'required|date|after_or_equal:today',
            'check_out_date'  => 'required|date|after:check_in_date',
            'num_guests'      => 'sometimes|integer|min:1',
        ];
    }

    public function messages()
    {
        return [
            'room_id.required'        => 'La habitación es requerida',
            'room_id.exists'          => 'La habitación no existe',
            'user_id.required'        => 'El huésped es requerido',
            'user_id.exists'          => 'El huésped no existe',
            'check_in_date.required'  => 'La fecha de entrada es requerida',
            'check_in_date.after_or_equal' => 'La fecha de entrada no puede ser en el pasado',
            'check_out_date.required' => 'La fecha de salida es requerida',
            'check_out_date.after'    => 'La fecha de salida debe ser posterior a la fecha de entrada',
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