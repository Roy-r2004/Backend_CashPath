<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Models\Account;
use App\Models\Transaction;

class AccountController extends Controller
{
    // ✅ Get all accounts for the authenticated user
    public function index()
    {
        $user = Auth::user();

        $accounts = Account::where('user_id', $user->id)->get();

        return response()->json([
            'message' => 'Accounts retrieved successfully.',
            'accounts' => $accounts
        ]);
    }

    // ✅ Create a new account (Ensuring user_id is stored)
    public function store(Request $request)
    {
        $user = Auth::user(); // ✅ Extract authenticated user's ID

        $request->validate([
            'name' => 'required|string|max:255',
            'balance' => 'nullable|numeric',
            'account_type' => 'required|in:Savings,Checking,Credit,Investment,Loan',
            'currency' => 'required|string|max:3',
            'icon' => 'nullable|string',
            'is_default' => 'boolean',
        ]);

        // ✅ If setting as default, reset previous default account
        if ($request->is_default) {
            Account::where('user_id', $user->id)->update(['is_default' => false]);
        }

        $account = Account::create([
            'user_id' => $user->id, // ✅ Store user ID
            'name' => $request->name,
            'balance' => $request->balance ?? 0.00,
            'account_type' => $request->account_type,
            'currency' => $request->currency,
            'icon' => $request->icon,
            'is_default' => $request->is_default ?? false,
        ]);

        return response()->json([
            'message' => 'Account created successfully.',
            'account' => $account
        ], 201);
    }

    // ✅ Get a single account by ID
    public function show($id)
    {
        $user = Auth::user();

        $account = Account::where('user_id', $user->id)->find($id);

        if (!$account) {
            return response()->json(['message' => 'Account not found.'], 404);
        }

        return response()->json([
            'message' => 'Account retrieved successfully.',
            'account' => $account
        ]);
    }

    // ✅ Update an account
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $account = Account::where('user_id', $user->id)->find($id);

        if (!$account) {
            return response()->json(['message' => 'Account not found.'], 404);
        }

        $request->validate([
            'name' => 'nullable|string|max:255',
            'balance' => 'nullable|numeric',
            'account_type' => 'nullable|in:Savings,Checking,Credit,Investment,Loan',
            'currency' => 'nullable|string|max:3',
            'icon' => 'nullable|string',
            'is_default' => 'boolean',
        ]);

        // ✅ If setting as default, remove previous default account
        if ($request->is_default) {
            Account::where('user_id', $user->id)->update(['is_default' => false]);
        }

        $account->update($request->only(['name', 'balance', 'account_type', 'currency', 'icon', 'is_default']));

        return response()->json([
            'message' => 'Account updated successfully.',
            'account' => $account
        ]);
    }

    // ✅ Delete an account (Only if it has no transactions)
    public function destroy($id)
    {
        $user = Auth::user();
        $account = Account::where('user_id', $user->id)->find($id);

        if (!$account) {
            return response()->json(['message' => 'Account not found.'], 404);
        }

        // Prevent deleting an account that has transactions
        if ($account->transactions()->count() > 0) {
            return response()->json(['message' => 'Cannot delete an account with transactions.'], 400);
        }

        $account->delete();

        return response()->json([
            'message' => 'Account deleted successfully.'
        ]);
    }

    // ✅ Get the total balance of all user accounts
    public function totalBalance()
    {
        $user = Auth::user();
        $totalBalance = Account::where('user_id', $user->id)->sum('balance');

        return response()->json([
            'message' => 'Total balance retrieved successfully.',
            'total_balance' => $totalBalance
        ]);
    }

    // ✅ Set an account as default
    public function setDefault($id)
    {
        $user = Auth::user();
        $account = Account::where('user_id', $user->id)->find($id);

        if (!$account) {
            return response()->json(['message' => 'Account not found.'], 404);
        }

        // Remove default from all accounts
        Account::where('user_id', $user->id)->update(['is_default' => false]);

        // Set the selected account as default
        $account->update(['is_default' => true]);

        return response()->json([
            'message' => 'Default account set successfully.',
            'account' => $account
        ]);
    }
}
