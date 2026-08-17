<?php

namespace App\Http\Repositories;

use App\Models\Service;
use Exception;

class ServiceRepository
{
    public function getServices(array $filters = [])
    {
        try {
            $query = Service::query();

            if (array_key_exists('active', $filters)) {
                $query->where('active', filter_var($filters['active'], FILTER_VALIDATE_BOOLEAN));
            }

            return [
                "message" => "Servicios obtenidos",
                "data" => $query->orderBy('name')->get()
            ];
        } catch (Exception $e) {
            return ["message" => $e->getMessage()];
        }
    }

    public function createService(array $data)
    {
        try {
            $service = Service::create([
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'price' => $data['price'],
                'active' => true,
            ]);

            return [
                "message" => "Servicio registrado",
                "data" => $service
            ];
        } catch (Exception $e) {
            return ["message" => $e->getMessage()];
        }
    }

    public function updateService(array $data, int $id)
    {
        try {
            $service = Service::find($id);

            if (!$service) {
                return ["message" => "Servicio no encontrado"];
            }

            $service->update($data);

            return [
                "message" => "Servicio actualizado",
                "data" => $service
            ];
        } catch (Exception $e) {
            return ["message" => $e->getMessage()];
        }
    }

    public function deactivateService(int $id)
    {
        try {
            $service = Service::find($id);

            if (!$service) {
                return ["message" => "Servicio no encontrado"];
            }

            $service->update(['active' => false]);

            return [
                "message" => "Servicio desactivado",
                "data" => $service
            ];
        } catch (Exception $e) {
            return ["message" => $e->getMessage()];
        }
    }
}