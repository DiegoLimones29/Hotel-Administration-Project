<?php

namespace App\Http\Repositories;

use App\Models\Reservation;
use App\Models\Invoice;
use App\Models\Room;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;
use Exception;

class ReportRepository
{
    // Estados que cuentan como "la habitación está comprometida" para
    // efectos de ocupación (excluye pending porque aún no se confirma,
    // y cancelled porque libera la habitación).
    private const OCCUPYING_STATUSES = ['confirmed', 'in_progress', 'completed'];

    public function occupancyReport(string $startDate, string $endDate, ?int $roomTypeId = null)
    {
        try {
            $start = Carbon::parse($startDate)->startOfDay();
            $end = Carbon::parse($endDate)->startOfDay();

            // Evita loops enormes si alguien manda un rango absurdo por error.
            if ($start->diffInDays($end) > 366) {
                return ["message" => "El rango de fechas no puede exceder 366 días"];
            }

            $totalRooms = Room::when($roomTypeId, fn ($q) => $q->where('room_type_id', $roomTypeId))->count();

            $days = [];
            foreach (CarbonPeriod::create($start, $end) as $day) {
                $occupied = Reservation::whereIn('status', self::OCCUPYING_STATUSES)
                    ->where('check_in_date', '<=', $day->format('Y-m-d'))
                    ->where('check_out_date', '>', $day->format('Y-m-d'))
                    ->when($roomTypeId, function ($q) use ($roomTypeId) {
                        $q->whereHas('room', fn ($rq) => $rq->where('room_type_id', $roomTypeId));
                    })
                    ->distinct('room_id')
                    ->count('room_id');

                $days[] = [
                    'date' => $day->format('Y-m-d'),
                    'occupied' => $occupied,
                    'total_rooms' => $totalRooms,
                    'occupancy_percent' => $totalRooms > 0 ? round(($occupied / $totalRooms) * 100, 2) : 0,
                ];
            }

            $avg = count($days) > 0 ? round(array_sum(array_column($days, 'occupancy_percent')) / count($days), 2) : 0;

            return [
                "message" => "Reporte de ocupación generado",
                "data" => [
                    "days" => $days,
                    "average_occupancy_percent" => $avg,
                ]
            ];
        } catch (Exception $e) {
            return ["message" => $e->getMessage()];
        }
    }

    public function revenueReport(string $startDate, string $endDate, ?int $roomTypeId = null)
    {
        try {
            $start = Carbon::parse($startDate)->startOfDay();
            $end = Carbon::parse($endDate)->endOfDay();

            $query = Invoice::with(['reservation.room.roomType', 'reservation.guest'])
                ->whereBetween('issued_at', [$start, $end]);

            if ($roomTypeId) {
                $query->whereHas('reservation.room', fn ($q) => $q->where('room_type_id', $roomTypeId));
            }

            $invoices = $query->orderBy('issued_at')->get();

            $totalRoomCost = $invoices->sum('room_cost');
            $totalServicesCost = $invoices->sum('services_cost');
            $totalRevenue = $invoices->sum('total_cost');

            return [
                "message" => "Reporte de ingresos generado",
                "data" => [
                    "invoices" => $invoices,
                    "summary" => [
                        "total_room_revenue" => round($totalRoomCost, 2),
                        "total_services_revenue" => round($totalServicesCost, 2),
                        "total_revenue" => round($totalRevenue, 2),
                        "invoice_count" => $invoices->count(),
                    ]
                ]
            ];
        } catch (Exception $e) {
            return ["message" => $e->getMessage()];
        }
    }

    public function mostRequestedRoomTypes(string $startDate, string $endDate)
    {
        try {
            $rows = Reservation::join('rooms', 'reservations.room_id', '=', 'rooms.id')
                ->join('room_type', 'rooms.room_type_id', '=', 'room_type.id')
                ->whereBetween('reservations.check_in_date', [$startDate, $endDate])
                ->where('reservations.status', '!=', 'cancelled')
                ->select('room_type.id', 'room_type.type', DB::raw('count(*) as total_reservations'))
                ->groupBy('room_type.id', 'room_type.type')
                ->orderByDesc('total_reservations')
                ->get();

            return [
                "message" => "Reporte de habitaciones más solicitadas generado",
                "data" => $rows
            ];
        } catch (Exception $e) {
            return ["message" => $e->getMessage()];
        }
    }

    public function frequentGuests(string $startDate, string $endDate, int $limit = 10)
    {
        try {
            $rows = Reservation::whereBetween('check_in_date', [$startDate, $endDate])
                ->where('status', '!=', 'cancelled')
                ->select('user_id', DB::raw('count(*) as total_stays'))
                ->groupBy('user_id')
                ->orderByDesc('total_stays')
                ->limit($limit)
                ->get();

            $users = User::whereIn('id', $rows->pluck('user_id'))->get()->keyBy('id');

            $data = $rows->map(function ($row) use ($users) {
                $user = $users->get($row->user_id);
                return [
                    'user_id' => $row->user_id,
                    'name' => $user->name ?? '—',
                    'email' => $user->email ?? '—',
                    'total_stays' => $row->total_stays,
                ];
            });

            return [
                "message" => "Reporte de huéspedes frecuentes generado",
                "data" => $data
            ];
        } catch (Exception $e) {
            return ["message" => $e->getMessage()];
        }
    }

    public function dailySummary(?string $date = null)
    {
        try {
            $day = $date ? Carbon::parse($date)->format('Y-m-d') : Carbon::today()->format('Y-m-d');

            $checkins = Reservation::whereDate('check_in_date', $day)
                ->where('status', '!=', 'cancelled')
                ->count();

            $checkouts = Reservation::whereDate('check_out_date', $day)
                ->where('status', '!=', 'cancelled')
                ->count();

            $roomsAvailable = Room::where('state', 'available')->count();
            $roomsTotal = Room::count();

            return [
                "message" => "Resumen del día generado",
                "data" => [
                    "date" => $day,
                    "checkins" => $checkins,
                    "checkouts" => $checkouts,
                    "rooms_available" => $roomsAvailable,
                    "rooms_total" => $roomsTotal,
                ]
            ];
        } catch (Exception $e) {
            return ["message" => $e->getMessage()];
        }
    }
}
