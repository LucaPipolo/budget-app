<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Transactions;

use App\Exceptions\TokenAbilitiesException;
use App\Http\Controllers\Api\V1\ApiController;
use App\Http\Requests\Api\V1\Transactions\StoreTransactionRequest;
use App\Http\Requests\Api\V1\Transactions\UpdateTransactionRequest;
use App\Http\Resources\Api\V1\Transactions\TransactionResource;
use App\Models\Transaction;
use App\Policies\TransactionPolicy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\Enums\FilterOperator;
use Spatie\QueryBuilder\QueryBuilder;

class TransactionsApiController extends ApiController
{
    /**
     * The policy class that handles authorization for the resource.
     */
    protected string $policyClass = TransactionPolicy::class;

    /**
     * List transactions.
     *
     * @return AnonymousResourceCollection The list of transactions.
     *
     * @response AnonymousResourceCollection<Illuminate\Pagination\LengthAwarePaginator<TransactionResource>>
     *
     * @throws TokenAbilitiesException
     */
    public function index(): AnonymousResourceCollection
    {
        $this->isAble('viewAny', Transaction::class, 'read');

        $user = auth()->user();
        $teamIds = $user->allTeams()->pluck('id');

        /** @var Collection<int, Transaction> $transactions */
        $transactions = QueryBuilder::for(Transaction::class)
            ->allowedFilters([
                AllowedFilter::exact('teamId', 'team_id')->ignore(null),
                AllowedFilter::exact('accountId', 'account_id')->ignore(null),
                AllowedFilter::exact('merchantId', 'merchant_id')->ignore(null),
                AllowedFilter::exact('categoryId', 'category_id')->ignore(null),
                AllowedFilter::operator('amount', FilterOperator::DYNAMIC, 'or'),
            ])
            ->allowedSorts([
                'amount',
                'date',
                AllowedSort::field('createdAt', 'created_at'),
                AllowedSort::field('updatedAt', 'updated_at'),
            ])
            ->where(function (Builder $query) use ($teamIds): void {
                $query->whereIn('team_id', $teamIds);
            })
            ->paginate();

        return TransactionResource::collection($transactions);
    }

    /**
     * Create a transaction.
     *
     * @param  StoreTransactionRequest  $request  The request containing the transaction data.
     *
     * @return TransactionResource The created transaction or a JSON error response.
     *
     * @throws TokenAbilitiesException
     */
    public function store(StoreTransactionRequest $request): TransactionResource
    {
        $this->isAble('create', new Transaction([
            'team_id' => $request->input('data.attributes.teamId'),
        ]), 'create');

        $transaction = Transaction::create($request->mappedAttributes());

        return new TransactionResource($transaction);
    }

    /**
     * Show transaction.
     *
     * @param  string  $transactionId  The ID of the transaction to display.
     *
     * @throws TokenAbilitiesException
     */
    public function show(string $transactionId): TransactionResource
    {
        /** @var Transaction $transaction */
        $transaction = QueryBuilder::for(Transaction::class)
            ->findOrFail($transactionId);

        $this->isAble('view', $transaction, 'read');

        return new TransactionResource($transaction);
    }

    /**
     * Replace transaction.
     *
     * @param  StoreTransactionRequest  $request  The request containing the updated transaction data.
     * @param  string  $transactionId  The ID of the transaction to update.
     *
     * @return TransactionResource The updated transaction or a JSON error response.
     *
     * @throws TokenAbilitiesException
     */
    public function replace(StoreTransactionRequest $request, string $transactionId): TransactionResource
    {
        $transaction = Transaction::findOrFail($transactionId);
        $transaction->team_id = $request->input('data.attributes.teamId');
        $this->isAble('update', $transaction, 'update');

        $transaction->update($request->mappedAttributes());

        return new TransactionResource($transaction);
    }

    /**
     * Update transaction.
     *
     * @param  UpdateTransactionRequest  $request  The request containing the updated transaction data.
     * @param  string  $transactionId  The ID of the transaction to update.
     *
     * @return TransactionResource|JsonResponse The updated transaction or a JSON error response.
     *
     * @throws TokenAbilitiesException
     */
    public function update(UpdateTransactionRequest $request, string $transactionId): TransactionResource|JsonResponse
    {
        $transaction = Transaction::findOrFail($transactionId);
        $request->has('data.attributes.teamId') && $transaction->team_id = $request->input('data.attributes.teamId');
        $this->isAble('update', $transaction, 'update');

        $transaction->update($request->mappedAttributes());

        return new TransactionResource($transaction);
    }

    /**
     * Delete transaction.
     *
     * @param  string  $transaction_id  The ID of the transaction to delete.
     *
     * @return JsonResponse The response indicating the result of the operation.
     *
     * @throws TokenAbilitiesException
     */
    public function destroy(string $transaction_id): JsonResponse
    {
        $transaction = Transaction::findOrFail($transaction_id);
        $this->isAble('delete', $transaction, 'delete');

        $transaction->delete();

        return response()->json([], 204);
    }
}
