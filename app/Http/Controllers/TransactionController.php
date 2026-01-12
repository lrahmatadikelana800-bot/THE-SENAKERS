<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TransactionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    // =========================
    // ADMIN & USER: LIST TRANSACTION
    // =========================
    public function index()
    {
        $user = auth()->user();

        if ($user->role === 'admin') {
            $transactions = Transaction::with(['user', 'product'])->get();
        } else {
            $transactions = Transaction::where('user_id', $user->id)
                ->with(['user', 'product'])
                ->get();
        }

        return response()->json($transactions);
    }

    // =========================
    // USER: CREATE TRANSACTION
    // =========================
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $product = Product::find($request->product_id);

        if ($product->stock < $request->quantity) {
            return response()->json([
                'message' => 'Insufficient stock'
            ], 400);
        }

        // kurangi stok
        $product->stock -= $request->quantity;
        $product->save();

        $transaction = Transaction::create([
            'user_id'     => auth()->id(),
            'product_id'  => $product->id,
            'quantity'    => $request->quantity,
            'total_price' => $product->price * $request->quantity,
            'status'      => 'pending',
        ]);

        return response()->json($transaction, 201);
    }

    // =========================
    // ADMIN & USER: DETAIL TRANSACTION
    // =========================
    public function show($id)
    {
        $transaction = Transaction::with(['user', 'product'])->find($id);

        if (!$transaction) {
            return response()->json([
                'message' => 'Transaction not found'
            ], 404);
        }

        $user = auth()->user();

        if ($user->role !== 'admin' && $transaction->user_id !== $user->id) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 403);
        }

        return response()->json($transaction);
    }

    // =========================
    // CANCEL TRANSACTION (USER & ADMIN)
    // =========================
    public function cancel($id)
    {
        $transaction = Transaction::find($id);

        if (!$transaction) {
            return response()->json([
                'message' => 'Transaction not found'
            ], 404);
        }

        $user = auth()->user();

        // User biasa hanya bisa cancel transaksi miliknya
        if ($user->role !== 'admin' && $transaction->user_id !== $user->id) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 403);
        }

        // Hanya transaksi pending yang bisa dibatalkan
        if ($transaction->status !== 'pending') {
            return response()->json([
                'message' => 'Only pending transactions can be cancelled'
            ], 400);
        }

        // kembalikan stok
        $product = Product::find($transaction->product_id);
        if ($product) {
            $product->stock += $transaction->quantity;
            $product->save();
        }

        $transaction->status = 'cancelled';
        $transaction->save();

        return response()->json([
            'message' => 'Transaction cancelled successfully',
            'data'    => $transaction
        ]);
    }

    // =========================
    // ADMIN: UPDATE STATUS
    // =========================
    public function update(Request $request, $id)
    {
        $transaction = Transaction::find($id);

        if (!$transaction) {
            return response()->json([
                'message' => 'Transaction not found'
            ], 404);
        }

        if (auth()->user()->role !== 'admin') {
            return response()->json([
                'message' => 'Unauthorized'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,paid,cancelled',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        // Jika admin cancel, kembalikan stok
        if ($request->status === 'cancelled' && $transaction->status !== 'cancelled') {
            $product = $transaction->product;
            if ($product) {
                $product->stock += $transaction->quantity;
                $product->save();
            }
        }

        $transaction->update([
            'status' => $request->status
        ]);

        return response()->json($transaction);
    }

    // =========================
    // ADMIN: DELETE TRANSACTION (PERMANENT)
    // =========================
    public function destroy($id)
    {
        $transaction = Transaction::find($id);

        if (!$transaction) {
            return response()->json([
                'message' => 'Transaction not found'
            ], 404);
        }

        if (auth()->user()->role !== 'admin') {
            return response()->json([
                'message' => 'Unauthorized'
            ], 403);
        }

        $transaction->forceDelete(); // <- hapus permanen

        return response()->json([
            'message' => 'Transaction deleted permanently'
        ]);
    }
}
