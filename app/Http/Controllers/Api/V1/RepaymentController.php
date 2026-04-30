<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Repayment\IndexRepaymentRequest;
use App\Http\Requests\Repayment\StoreRepaymentRequest;
use App\Http\Resources\RepaymentResource;
use App\Models\Repayment;
use App\Services\RepaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class RepaymentController extends Controller
{
    public function __construct(
        private readonly RepaymentService $repaymentService
    ) {}

    public function index(IndexRepaymentRequest $request): AnonymousResourceCollection
    {
        return RepaymentResource::collection(
            $this->repaymentService->list($request->validated())
        );
    }

    public function show(Repayment $repayment): RepaymentResource
    {
        $repayment->load(['farmer', 'operator', 'debts']);
        $repayment->farmer->loadOutstandingDebt();

        return RepaymentResource::make($repayment);
    }

    public function store(StoreRepaymentRequest $request): JsonResponse
    {
        $repayment = $this->repaymentService->create($request->validated(), $request->user());

        return RepaymentResource::make($repayment)
            ->response()
            ->setStatusCode(201);
    }
}
