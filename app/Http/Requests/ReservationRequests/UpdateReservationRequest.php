<?php

namespace App\Http\Requests\ReservationRequests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateReservationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'room_id'        => 'sometimes|required|exists:rooms,id',
            'check_in_date'  => 'sometimes|required|date',
            'check_out_date' => 'sometimes|required|date|after:check_in_date',
            'num_guests'     => 'sometimes|integer|min:1',
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