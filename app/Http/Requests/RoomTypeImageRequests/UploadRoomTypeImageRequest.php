<?php

namespace App\Http\Requests\RoomTypeImageRequests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UploadRoomTypeImageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            
            'image' => 'required|image|mimes:jpg,jpeg,png|max:5120',
        ];
    }

    public function messages()
    {
        return [
            'image.required' => 'La imagen es requerida',
            'image.mimes'    => 'Solo se permiten imágenes JPG o PNG',
            'image.max'      => 'La imagen no puede pesar más de 5MB',
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
