<?php

namespace App\Http\Requests\ServiceRequests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateServiceStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => 'required|in:solicitado,en_proceso,entregado',
        ];
    }

    public function messages()
    {
        return [
            'status.required' => 'El estado es requerido',
            'status.in'       => 'El estado debe ser: solicitado, en_proceso o entregado',
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