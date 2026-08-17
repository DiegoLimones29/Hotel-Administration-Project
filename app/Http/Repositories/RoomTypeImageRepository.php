<?php

namespace App\Http\Repositories;

use App\Models\Room_Type;
use App\Models\Room_Img;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Exception;

class RoomTypeImageRepository
{
    public function upload(int $roomTypeId, UploadedFile $file)
    {
        try {
            $roomType = Room_Type::find($roomTypeId);

            if (!$roomType) {
                return ["message" => "Tipo de habitación no encontrado"];
            }

            $url = config('services.supabase.url');
            $key = config('services.supabase.service_role_key');
            $bucket = config('services.supabase.bucket');
            $isPublic = filter_var(config('services.supabase.bucket_public'), FILTER_VALIDATE_BOOLEAN);

            if (!$url || !$key) {
                return ["message" => "Supabase no está configurado (falta SUPABASE_URL o SUPABASE_SERVICE_ROLE_KEY en .env)"];
            }

            $path = 'room-type-'.$roomTypeId.'/'.Str::uuid().'.'.$file->getClientOriginalExtension();

            // Sube el binario tal cual a Supabase Storage vía su API REST.
            // La service_role_key nunca sale del backend (nunca viaja al navegador).
            $uploadResponse = Http::withHeaders([
                'Authorization' => "Bearer {$key}",
                'apikey' => $key,
            ])->withBody(file_get_contents($file->getRealPath()), $file->getMimeType())
              ->post("{$url}/storage/v1/object/{$bucket}/{$path}");

            if (!$uploadResponse->successful()) {
                return ["message" => "Error subiendo la imagen a Supabase: ".$uploadResponse->body()];
            }

            if ($isPublic) {
                $imgUrl = "{$url}/storage/v1/object/public/{$bucket}/{$path}";
            } else {
                // Bucket privado: pedimos una URL firmada (1 año) para poder mostrarla en la app.
                $signResponse = Http::withHeaders([
                    'Authorization' => "Bearer {$key}",
                    'apikey' => $key,
                    'Content-Type' => 'application/json',
                ])->post("{$url}/storage/v1/object/sign/{$bucket}/{$path}", [
                    'expiresIn' => 31536000,
                ]);

                if (!$signResponse->successful()) {
                    return ["message" => "Imagen subida, pero no se pudo generar la URL firmada: ".$signResponse->body()];
                }

                $imgUrl = $url.$signResponse->json('signedURL');
            }

            $roomImg = Room_Img::create([
                'room_type_id' => $roomTypeId,
                'img_url' => $imgUrl,
            ]);

            return [
                "message" => "Imagen subida correctamente",
                "data" => $roomImg
            ];
        } catch (Exception $e) {
            return ["message" => $e->getMessage()];
        }
    }

    public function getByRoomType(int $roomTypeId)
    {
        try {
            $images = Room_Img::where('room_type_id', $roomTypeId)->get();

            return [
                "message" => "Imágenes obtenidas",
                "data" => $images
            ];
        } catch (Exception $e) {
            return ["message" => $e->getMessage()];
        }
    }
}
