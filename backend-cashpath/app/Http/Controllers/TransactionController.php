<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Transaction;
use App\Models\Account;
use App\Models\Category;

class TransactionController extends Controller
{
    // ✅ Get all transactions (With Filters)
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = $user->transactions()->with('category');

        // ✅ Apply Filters
        if ($request->has('type') && in_array($request->type, ['Income', 'Expense'])) {
            $query->where('type', $request->type);
        }
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        if ($request->has('start_date')) {
            $query->whereDate('date', '>=', $request->start_date);
        }
        if ($request->has('end_date')) {
            $query->whereDate('date', '<=', $request->end_date);
        }

        $transactions = $query->orderBy('date', 'desc')->paginate($request->get('per_page', 10));

        return response()->json([
            'message' => 'Transactions retrieved successfully.',
            'transactions' => $transactions
        ]);
    }

    // ✅ Create a new transaction
    public function store(Request $request)
    {
        // ✅ Log incoming request for debugging
        \Log::info('Transaction Create Request:', $request->all());
    
        $validatedData = $request->validate([
            'account_id' => 'required|exists:accounts,id',
            'category_id' => 'nullable|exists:categories,id',
            'amount' => 'required|numeric|min:0.01',
            'type' => 'required|in:Income,Expense',
            'date' => 'required|date',
            'time' => 'required',
            'note' => 'nullable|string|max:255',
            'receipt_image' => 'nullable|string',
            'is_recurring' => 'boolean',
        ]);
    
        $user = Auth::user();
        $account = $user->accounts()->where('id', $validatedData['account_id'])->first();
    
        if (!$account) {
            return response()->json(['message' => 'Account not found or does not belong to you.'], 404);
        }
    
        $transaction = $user->transactions()->create($validatedData);
    
        return response()->json([
            'message' => 'Transaction created successfully.',
            'transaction' => $transaction
        ], 201);
    }
    
    // ✅ Get a single transaction
    public function show($id)
    {
        $transaction = Auth::user()->transactions()->with('category')->find($id);

        if (!$transaction) {
            return response()->json(['message' => 'Transaction not found.'], 404);
        }

        return response()->json([
            'message' => 'Transaction retrieved successfully.',
            'transaction' => $transaction
        ]);
    }

    // ✅ Update a transaction
    public function update(Request $request, $id)
    {
        $transaction = Auth::user()->transactions()->find($id);

        if (!$transaction) {
            return response()->json(['message' => 'Transaction not found.'], 404);
        }

        $validatedData = $request->validate([
            'amount' => 'nullable|numeric',
            'note' => 'nullable|string',
        ]);

        $transaction->update($validatedData);

        return response()->json([
            'message' => 'Transaction updated successfully.',
            'transaction' => $transaction
        ]);
    }

    // ✅ Delete a transaction
    public function destroy($id)
    {
        $transaction = Auth::user()->transactions()->find($id);

        if (!$transaction) {
            return response()->json(['message' => 'Transaction not found.'], 404);
        }

        $transaction->delete();

        return response()->json(['message' => 'Transaction deleted successfully.']);
    }

    // ✅ Get transactions for a specific year (Grouped by Month)
    public function getYearlyTransactions($year)
    {
        $user = Auth::user();
        $transactions = $user->transactions()
            ->whereYear('date', $year)
            ->orderBy('date', 'desc')
            ->with('category')
            ->get()
            ->groupBy(function ($transaction) {
                return $transaction->date->format('F');
            });

        return response()->json([
            'message' => "Transactions for year $year retrieved successfully.",
            'transactions' => $transactions
        ]);
    }

    // ✅ Get transactions for a specific month (Grouped by Days)
    public function getMonthlyTransactions($year, $month)
    {
        $user = Auth::user();
    
        // Fetch transactions for the given month and year
        $transactions = $user->transactions()
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->orderBy('date', 'desc')
            ->with('category')
            ->get();
    
        // Group transactions by day
        $groupedTransactions = $transactions->groupBy(function ($transaction) {
            return \Carbon\Carbon::parse($transaction->date)->format('d');
        });
    
        // Calculate totals
        $totalIncome = $transactions->where('type', 'Income')->sum('amount');
        $totalExpenses = $transactions->where('type', 'Expense')->sum('amount');
    
        return response()->json([
            'message' => "Transactions for $year-$month retrieved successfully.",
            'total_income' => $totalIncome,
            'total_expenses' => $totalExpenses,
            'transactions' => $groupedTransactions
        ]);
    }
    

    // ✅ Get transactions for a specific day
    public function getDailyTransactions($year, $month, $day)
    {
        $user = Auth::user();
        $transactions = $user->transactions()
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->whereDay('date', $day)
            ->orderBy('date', 'desc')
            ->with('category')
            ->get();

        return response()->json([
            'message' => "Transactions for $year-$month-$day retrieved successfully.",
            'transactions' => $transactions
        ]);
    }

    // ✅ Get transactions for calendar view
    public function getCalendarTransactions($year, $month)
    {
        $user = Auth::user();
        $transactions = $user->transactions()
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->selectRaw('date, 
                SUM(CASE WHEN type = "Income" THEN amount ELSE 0 END) as total_income, 
                SUM(CASE WHEN type = "Expense" THEN amount ELSE 0 END) as total_expenses')
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->get();

        return response()->json([
            'message' => "Calendar transactions for $year-$month retrieved successfully.",
            'transactions' => $transactions
        ]);
    }

    // ✅ Get income & expense summary
    public function getIncomeAndExpenses()
    {
        // ✅ Get the authenticated user
        $user = Auth::user();
    
        if (!$user) {
            return response()->json(['message' => 'User not authenticated.'], 401);
        }
    
        // ✅ Fetch the user's transactions
        $totalIncome = Transaction::where('user_id', $user->id)
            ->where('type', 'Income')
            ->sum('amount');
    
        $totalExpenses = Transaction::where('user_id', $user->id)
            ->where('type', 'Expense')
            ->sum('amount');
    
        return response()->json([
            'message' => 'Income and expenses retrieved successfully.',
            'total_income' => $totalIncome ?? 0.00,
            'total_expenses' => $totalExpenses ?? 0.00
        ]);
    }
    

    // ✅ Get transactions filtered by description
    public function getTransactionsByDescription(Request $request)
    {
        $user = Auth::user();
        $keyword = $request->query('keyword', '');

        $transactions = $user->transactions()
            ->where('note', 'LIKE', "%{$keyword}%")
            ->orderBy('date', 'desc')
            ->get();

        return response()->json([
            'message' => 'Transactions filtered by description retrieved successfully.',
            'transactions' => $transactions
        ]);
    }
}
