<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Repayment\StoreRepaymentRequest;
use App\Http\Resources\RepaymentResource;
use App\Services\RepaymentService;
use Illuminate\Http\JsonResponse;

class RepaymentController extends Controller
{
    public function __construct(
        private readonly RepaymentService $repaymentService
    ) {}

    public function store(StoreRepaymentRequest $request): JsonResponse
    {
        $repayment = $this->repaymentService->create($request->validated(), $request->user());

        return RepaymentResource::make($repayment)
            ->response()
            ->setStatusCode(201);
    }
}
