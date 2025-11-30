<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreTransactionRequest;
use App\Http\Requests\Api\UpdateTransactionRequest;
use App\Http\Resources\TransactionResource;
use App\Models\Transaction;
use App\Services\TransactionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function __construct(
        private TransactionService $transactionService
    ) {}

    /**
     * Get transactions with optional filters.
     * GET /api/v1/transactions
     */
    public function index(Request $request): JsonResponse
    {
        $transactions = $this->transactionService->getFiltered(
            user: $request->user(),
            filters: $request->only(['date', 'start_date', 'end_date', 'type', 'category_id']),
            perPage: (int) $request->get('per_page', 20),
            sortBy: $request->get('sort_by', 'date'),
            sortOrder: $request->get('sort_order', 'desc')
        );

        $summary = $this->transactionService->calculateSummary($transactions);

        $message = $transactions->total() > 0
            ? "Found {$transactions->total()} transactions"
            : "No transactions found for this period";

        $response = [
            'success' => true,
            'status' => 'retrieved',
            'message' => $message,
            'data' => TransactionResource::collection($transactions),
            'summary' => $summary,
            'meta' => [
                'timestamp' => now()->toISOString(),
                'pagination' => [
                    'current_page' => $transactions->currentPage(),
                    'last_page' => $transactions->lastPage(),
                    'per_page' => $transactions->perPage(),
                    'total' => $transactions->total(),
                    'has_more' => $transactions->hasMorePages(),
                    'next_page_url' => $transactions->nextPageUrl(),
                    'prev_page_url' => $transactions->previousPageUrl(),
                ],
                'filters_applied' => array_filter($request->only(['date', 'type', 'category_id'])),
            ],
        ];

        // Add suggestion if no transactions found
        if ($transactions->total() === 0) {
            $response['meta']['suggestion'] = [
                'action' => 'add_transaction',
                'message' => 'Start tracking by adding your first expense or income!',
            ];
        }

        return response()->json($response);
    }

    /**
     * Create a new transaction.
     * POST /api/v1/transactions
     */
    public function store(StoreTransactionRequest $request): JsonResponse
    {
        $transaction = $this->transactionService->create(
            user: $request->user(),
            data: $request->validated()
        );

        // Load the category relationship
        $transaction->load('category');

        $dailyImpact = $this->transactionService->getDailyImpact(
            $request->user(),
            $transaction->date
        );

        $typeLabel = $transaction->type_label;
        $formattedAmount = $transaction->formatted_amount;
        $categoryName = $transaction->category?->name ?? 'Unknown';

        return response()->json([
            'success' => true,
            'status' => 'created',
            'message' => "{$typeLabel} of {$formattedAmount} added successfully!",
            'data' => [
                'transaction' => new TransactionResource($transaction),
                'daily_impact' => $dailyImpact,
            ],
            'meta' => [
                'timestamp' => now()->toISOString(),
                'action_performed' => $transaction->type . '_created',
                'suggested_action' => 'view_dashboard',
                'redirect_to' => '/dashboard',
                'notification' => [
                    'title' => "{$typeLabel} Added",
                    'body' => "{$formattedAmount} for {$categoryName}",
                    'type' => 'success',
                ],
            ],
        ], 201);
    }

    /**
     * Get a specific transaction.
     * GET /api/v1/transactions/{id}
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $transaction = $request->user()->transactions()
            ->with('category')
            ->find($id);

        if (!$transaction) {
            return response()->json([
                'success' => false,
                'status' => 'error',
                'message' => 'Transaction not found or you don\'t have permission to view it',
                'error_code' => 'NOT_FOUND',
                'meta' => [
                    'timestamp' => now()->toISOString(),
                ],
            ], 404);
        }

        $context = $this->transactionService->getTransactionContext($transaction);

        return response()->json([
            'success' => true,
            'status' => 'retrieved',
            'message' => 'Transaction details retrieved',
            'data' => [
                'transaction' => new TransactionResource($transaction),
                'context' => $context,
            ],
            'meta' => [
                'timestamp' => now()->toISOString(),
            ],
        ]);
    }

    /**
     * Update a transaction.
     * PUT /api/v1/transactions/{id}
     */
    public function update(UpdateTransactionRequest $request, int $id): JsonResponse
    {
        $transaction = $request->user()->transactions()->find($id);

        if (!$transaction) {
            return response()->json([
                'success' => false,
                'status' => 'error',
                'message' => 'Transaction not found or you don\'t have permission to edit it',
                'error_code' => 'NOT_FOUND',
                'meta' => [
                    'timestamp' => now()->toISOString(),
                ],
            ], 404);
        }

        // Store old data for comparison
        $oldData = $transaction->toArray();

        // Update the transaction
        $transaction = $this->transactionService->update(
            transaction: $transaction,
            data: $request->validated()
        );

        // Calculate changes and impact
        $changes = $this->transactionService->getChanges($oldData, $transaction);
        $dailyImpact = $this->transactionService->getDailyImpact(
            $request->user(),
            $transaction->date
        );

        $changesSummary = $this->transactionService->formatChangesSummary($changes);

        return response()->json([
            'success' => true,
            'status' => 'updated',
            'message' => 'Transaction updated successfully!',
            'data' => [
                'transaction' => new TransactionResource($transaction),
                'changes' => $changes,
                'daily_impact' => $dailyImpact,
            ],
            'meta' => [
                'timestamp' => now()->toISOString(),
                'action_performed' => 'transaction_updated',
                'suggested_action' => 'view_transactions',
                'redirect_to' => '/transactions',
                'notification' => [
                    'title' => 'Transaction Updated',
                    'body' => $changesSummary,
                    'type' => 'success',
                ],
            ],
        ]);
    }

    /**
     * Delete a transaction (soft delete).
     * DELETE /api/v1/transactions/{id}
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $transaction = $request->user()->transactions()
            ->with('category')
            ->find($id);

        if (!$transaction) {
            return response()->json([
                'success' => false,
                'status' => 'error',
                'message' => 'Transaction not found or you don\'t have permission to delete it',
                'error_code' => 'NOT_FOUND',
                'meta' => [
                    'timestamp' => now()->toISOString(),
                ],
            ], 404);
        }

        // Store data before deletion
        $deletedData = [
            'id' => $transaction->id,
            'amount' => (float) $transaction->amount,
            'type' => $transaction->type,
            'category_name' => $transaction->category?->name ?? 'Unknown',
            'date' => $transaction->date->format('Y-m-d'),
        ];

        $user = $request->user();
        $date = $transaction->date;
        $typeLabel = $transaction->type_label;
        $formattedAmount = $transaction->formatted_amount;

        // Soft delete for undo capability
        $transaction->delete();

        // Calculate new daily impact
        $dailyImpact = $this->transactionService->getDailyImpact($user, $date);

        return response()->json([
            'success' => true,
            'status' => 'deleted',
            'message' => "{$typeLabel} of {$formattedAmount} has been deleted",
            'data' => [
                'deleted_transaction' => $deletedData,
                'daily_impact' => $dailyImpact,
            ],
            'meta' => [
                'timestamp' => now()->toISOString(),
                'action_performed' => 'transaction_deleted',
                'suggested_action' => 'view_dashboard',
                'redirect_to' => '/dashboard',
                'notification' => [
                    'title' => 'Transaction Deleted',
                    'body' => "{$deletedData['category_name']} {$typeLabel} removed",
                    'type' => 'info',
                ],
                'undo_available' => true,
                'undo_expires_at' => now()->addMinutes(5)->toISOString(),
            ],
        ]);
    }

    /**
     * Restore a soft-deleted transaction (undo).
     * POST /api/v1/transactions/{id}/restore
     */
    public function restore(Request $request, int $id): JsonResponse
    {
        $transaction = $request->user()->transactions()
            ->withTrashed()
            ->with('category')
            ->find($id);

        if (!$transaction) {
            return response()->json([
                'success' => false,
                'status' => 'error',
                'message' => 'Transaction not found or you don\'t have permission to restore it',
                'error_code' => 'NOT_FOUND',
                'meta' => [
                    'timestamp' => now()->toISOString(),
                ],
            ], 404);
        }

        // Check if already restored
        if (!$transaction->trashed()) {
            return response()->json([
                'success' => false,
                'status' => 'error',
                'message' => 'This transaction is not deleted',
                'error_code' => 'NOT_DELETED',
                'meta' => [
                    'timestamp' => now()->toISOString(),
                ],
            ], 400);
        }

        // Check if undo period hasn't expired (5 minutes)
        if (!$transaction->canBeRestored()) {
            return response()->json([
                'success' => false,
                'status' => 'error',
                'message' => 'Undo period has expired. Transaction cannot be restored.',
                'error_code' => 'UNDO_EXPIRED',
                'meta' => [
                    'timestamp' => now()->toISOString(),
                ],
            ], 400);
        }

        // Restore the transaction
        $transaction->restore();

        $dailyImpact = $this->transactionService->getDailyImpact(
            $request->user(),
            $transaction->date
        );

        return response()->json([
            'success' => true,
            'status' => 'restored',
            'message' => 'Transaction has been restored!',
            'data' => [
                'transaction' => new TransactionResource($transaction),
                'daily_impact' => $dailyImpact,
            ],
            'meta' => [
                'timestamp' => now()->toISOString(),
                'action_performed' => 'transaction_restored',
                'suggested_action' => 'view_transactions',
                'redirect_to' => '/transactions',
                'notification' => [
                    'title' => 'Transaction Restored',
                    'body' => 'Your transaction has been restored successfully',
                    'type' => 'success',
                ],
            ],
        ]);
    }
}
