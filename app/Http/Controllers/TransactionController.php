<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TransactionController extends Controller
{
    /**
     * List all transactions (optional helper endpoint).
     */
    public function index(): JsonResponse
    {
        $transactions = Transaction::with('transactionDetails.product')
            ->latest()
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $transactions,
        ]);
    }

    /**
     * Process checkout transaction.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'items.*.qty' => ['required', 'integer', 'min:1'],
        ]);

        DB::beginTransaction();

        try {
            $totalAmount = 0;
            $preparedDetails = [];

            $itemsGrouped = [];
            foreach ($request->items as $item) {
                $productId = $item['product_id'];
                $qty = $item['qty'];
                $itemsGrouped[$productId] = ($itemsGrouped[$productId] ?? 0) + $qty;
            }

            foreach ($itemsGrouped as $productId => $qty) {
                $product = Product::lockForUpdate()->find($productId);

                if (! $product) {
                    DB::rollBack();
                    return response()->json([
                        'status' => 'error',
                        'message' => "Product with ID {$productId} not found.",
                    ], 404);
                }

                if ($product->stock < $qty) {
                    DB::rollBack();
                    return response()->json([
                        'status' => 'error',
                        'message' => "Insufficient stock for product '{$product->name}'. Available: {$product->stock}, Requested: {$qty}.",
                    ], 400);
                }

                $subtotal = $product->price * $qty;
                $totalAmount += $subtotal;

                $preparedDetails[] = [
                    'product' => $product,
                    'product_id' => $product->id,
                    'qty' => $qty,
                    'price' => $product->price,
                    'subtotal' => $subtotal,
                ];
            }

            do {
                $invoiceNumber = 'INV-' . strtoupper(Str::random(8));
            } while (Transaction::where('invoice_number', $invoiceNumber)->exists());

            $transaction = Transaction::create([
                'invoice_number' => $invoiceNumber,
                'total_amount' => $totalAmount,
            ]);

            foreach ($preparedDetails as $detail) {
                TransactionDetail::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $detail['product_id'],
                    'qty' => $detail['qty'],
                    'price' => $detail['price'],
                    'subtotal' => $detail['subtotal'],
                ]);

                $detail['product']->decrement('stock', $detail['qty']);
            }

            DB::commit();

            $transaction->load('transactionDetails.product');

            return response()->json([
                'status' => 'success',
                'message' => 'Transaction created successfully.',
                'data' => $transaction,
            ], 201);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to process transaction: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Show single transaction details.
     */
    public function show(Transaction $transaction): JsonResponse
    {
        $transaction->load('transactionDetails.product');

        return response()->json([
            'status' => 'success',
            'data' => $transaction,
        ]);
    }

    /**
     * Daily sales report.
     */
    public function dailyReport(Request $request): JsonResponse
    {
        $request->validate([
            'date' => ['nullable', 'date_format:Y-m-d'],
        ]);

        $date = $request->query('date', Carbon::today()->toDateString());

        $transactions = Transaction::with('transactionDetails.product')
            ->whereDate('created_at', $date)
            ->latest()
            ->get();

        $totalOmzet = (float) $transactions->sum('total_amount');
        $totalTransactions = $transactions->count();

        return response()->json([
            'status' => 'success',
            'data' => [
                'date' => $date,
                'total_omzet' => $totalOmzet,
                'total_transactions' => $totalTransactions,
                'transactions' => $transactions,
            ],
        ]);
    }
}
