<?php

namespace App\Http\Repositories;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Exception;

class UserRepository
{
    public function getGuests(?string $search = null)
    {
        try {
            $query = User::where('role', 'guest');

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            }

            return [
                "message" => "Huéspedes obtenidos",
                "data" => $query->orderBy('name')->get(['id', 'name', 'email', 'phone', 'created_at'])
            ];
        } catch (Exception $e) {
            return ["message" => $e->getMessage()];
        }
    }

    public function createGuest(array $data)
    {
        try {
            $guest = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'password' => Hash::make($data['password']),
                'role' => 'guest',
            ]);

            return [
                "message" => "Huésped registrado",
                "data" => $guest
            ];
        } catch (Exception $e) {
            return ["message" => $e->getMessage()];
        }
    }

    public function createStaff(array $data)
    {
        try {
            $staff = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'password' => Hash::make($data['password']),
                'role' => 'recep',
            ]);

            return [
                "message" => "Recepcionista registrado",
                "data" => $staff
            ];
        } catch (Exception $e) {
            return ["message" => $e->getMessage()];
        }
    }
}
