<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class WalletController extends Controller
{
   
    public function balance(Request $request)
    {
        $user = $request->user();
        return response()->json([
            'user_id' => $user->id,
            'balance' => $user->balance,
        ]);
    }


    public function transfer(Request $request)
    {
        $validated = $request->validate([
            'receiver_id' => 'required|integer|exists:users,id',
            'amount' => 'required|numeric|min:0.01',
        ]);

        $sender = $request->user();
        $receiver = User::find($validated['receiver_id']);

        if ($sender->id === $receiver->id) {
            throw ValidationException::withMessages([
                'receiver_id' => 'You cannot transfer money to yourself.',
            ]);
        }

        if ($sender->balance < $validated['amount']) {
            return response()->json(['message' => 'Solde insuffisant'], 400);
        }

        try {
            DB::beginTransaction();

            $sender->decrement('balance', $validated['amount']);
            $receiver->increment('balance', $validated['amount']);
            $transaction = Transaction::create([
                'sender_id' => $sender->id,
                'receiver_id' => $receiver->id,
                'amount' => $validated['amount'],
                'type' => 'transfer',
                'status' => 'success',
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error during transfer', 'error' => $e->getMessage()], 500);
        }

        return response()->json([
            'transaction_id' => $transaction->id,
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
            'amount' => $validated['amount'],
            'status' => 'success',
            'created_at' => $transaction->created_at,
        ], 201);
    }

    public function topup(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01|max:10000',
        ]);

        $user = $request->user();

        try {
            DB::beginTransaction();

            $user->increment('balance', $validated['amount']);

            $transaction = Transaction::create([
                'sender_id' => null,
                'receiver_id' => $user->id,
                'amount' => $validated['amount'],
                'type' => 'topup',
                'status' => 'success',
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error while reloading'], 500);
        }

        return response()->json([
            'user_id' => $user->id,
            'balance' => $user->balance,
            'topup_amount' => $validated['amount'],
            'created_at' => $transaction->created_at,
        ], 201);
    }

    
    public function transactions(Request $request)
    {
        $user = $request->user();

        $transactions = Transaction::where('sender_id', $user->id)
                                   ->orWhere('receiver_id', $user->id)
                                   ->orderByDesc('created_at')
                                   ->get();

        return response()->json($transactions);
    }
}
