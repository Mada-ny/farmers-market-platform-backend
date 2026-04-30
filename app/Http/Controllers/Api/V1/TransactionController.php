<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Transaction\IndexTransactionRequest;
use App\Http\Requests\Transaction\StoreTransactionRequest;
use App\Http\Resources\TransactionResource;
use App\Models\Transaction;
use App\Services\TransactionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TransactionController extends Controller
{
    public function __construct(
        private readonly TransactionService $transactionService
    ) {}

    public function index(IndexTransactionRequest $request): AnonymousResourceCollection
    {
        return TransactionResource::collection(
            $this->transactionService->list($request->validated())
        );
    }

    public function store(StoreTransactionRequest $request): JsonResponse
    {
        $transaction = $this->transactionService->create($request->validated(), $request->user());

        return TransactionResource::make($transaction)
            ->response()
            ->setStatusCode(201);
    }

    public function show(Transaction $transaction): TransactionResource
    {
        $transaction->load(['farmer', 'operator', 'items.product']);
        $transaction->farmer->loadOutstandingDebt();

        return TransactionResource::make($transaction);
    }
}
