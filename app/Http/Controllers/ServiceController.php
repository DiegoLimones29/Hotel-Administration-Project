<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Repositories\ServiceRepository;
use App\Http\Requests\ServiceRequests\StoreServiceRequest;
use App\Http\Requests\ServiceRequests\UpdateServiceRequest;

class ServiceController extends Controller
{
    protected $serviceRepository;

    public function __construct(ServiceRepository $serviceRepository)
    {
        $this->serviceRepository = $serviceRepository;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['active']);
        return response()->json($this->serviceRepository->getServices($filters), 200);
    }

    public function store(StoreServiceRequest $request)
    {
        try {
            $data = $request->validated();
            $result = $this->serviceRepository->createService($data);
            $status = isset($result['data']) ? 201 : 422;
            return response()->json($result, $status);
        } catch (\Exception $e) {
            return response()->json(["message" => $e->getMessage()], 500);
        }
    }

    public function update(UpdateServiceRequest $request, string $id)
    {
        try {
            $data = $request->validated();
            $result = $this->serviceRepository->updateService($data, (int) $id);
            $status = isset($result['data']) ? 200 : 422;
            return response()->json($result, $status);
        } catch (\Exception $e) {
            return response()->json(["message" => $e->getMessage()], 500);
        }
    }

    public function deactivate(string $id)
    {
        try {
            $result = $this->serviceRepository->deactivateService((int) $id);
            $status = isset($result['data']) ? 200 : 422;
            return response()->json($result, $status);
        } catch (\Exception $e) {
            return response()->json(["message" => $e->getMessage()], 500);
        }
    }

    public function activate(string $id)
    {
        try {
            $result = $this->serviceRepository->activateService((int) $id);
            $status = isset($result['data']) ? 200 : 422;
            return response()->json($result, $status);
        } catch (\Exception $e) {
            return response()->json(["message" => $e->getMessage()], 500);
        }
    }
}
