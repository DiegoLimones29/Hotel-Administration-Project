<?php

namespace App\Http\Requests\CheckInOutRequests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class CheckOutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'payment_method' => 'required|in:cash,card,transfer',
        ];
    }

    public function messages()
    {
        return [
            'payment_method.required' => 'El método de pago es requerido',
            'payment_method.in'       => 'El método de pago debe ser cash, card o transfer',
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
