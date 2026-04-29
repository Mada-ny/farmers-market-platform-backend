<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Farmer\StoreFarmerRequest;
use App\Http\Requests\Farmer\UpdateFarmerRequest;
use App\Http\Resources\DebtResource;
use App\Http\Resources\FarmerResource;
use App\Models\Farmer;
use App\Services\FarmerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class FarmerController extends Controller
{
    public function __construct(
        private readonly FarmerService $farmerService
    ) {}

    public function index(): AnonymousResourceCollection
    {
        return FarmerResource::collection(
            $this->farmerService->list()
        );
    }

    public function store(StoreFarmerRequest $request): JsonResponse
    {
        $farmer = $this->farmerService->create($request->validated());

        return FarmerResource::make($farmer)
            ->response()
            ->setStatusCode(201);
    }

    public function show(Farmer $farmer): FarmerResource
    {
        return FarmerResource::make($farmer);
    }

    public function update(UpdateFarmerRequest $request, Farmer $farmer): FarmerResource
    {
        $farmer = $this->farmerService->update($farmer, $request->validated());

        return FarmerResource::make($farmer);
    }

    public function destroy(Farmer $farmer): JsonResponse
    {
        $this->farmerService->delete($farmer);

        return response()->json(null, 204);
    }

    public function debts(Farmer $farmer): AnonymousResourceCollection
    {
        return DebtResource::collection(
            $this->farmerService->outstandingDebts($farmer)
        );
    }
}
