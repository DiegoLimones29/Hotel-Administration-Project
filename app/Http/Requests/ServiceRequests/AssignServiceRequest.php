<?php

namespace App\Http\Requests\ServiceRequests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class AssignServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'reservation_id' => 'required|exists:reservations,id',
            'service_id'     => 'required|exists:services,id',
            'quantity'       => 'sometimes|integer|min:1',
            'requested_date' => 'sometimes|date',
        ];
    }

    public function messages()
    {
        return [
            'reservation_id.required' => 'La reservación es requerida',
            'reservation_id.exists'   => 'La reservación no existe',
            'service_id.required'     => 'El servicio es requerido',
            'service_id.exists'       => 'El servicio no existe',
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