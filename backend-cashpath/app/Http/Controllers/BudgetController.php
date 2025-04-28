<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Budget;
use App\Models\Transaction;
use App\Models\Category;
use Illuminate\Support\Str;

class BudgetController extends Controller
{
    // ✅ Create Budget (Manual)
    public function createBudget(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'amount' => 'required|numeric',
            'period' => 'required|in:Monthly,Weekly,Custom',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date',
        ]);

        $budget = Budget::create([
            'id' => Str::uuid(),
            'user_id' => auth()->id(),
            'category_id' => $request->category_id,
            'amount' => $request->amount,
            'period' => $request->period,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'status' => 'Active',
        ]);

        return response()->json(['message' => 'Budget created successfully', 'budget' => $budget], 201);
    }

    // ✅ Automatic Allocation
    public function autoAllocate(Request $request)
    {
        $request->validate([
            'percentages' => 'required|array',
        ]);

        $userId = auth()->id();
        $totalIncome = Transaction::where('user_id', $userId)->where('type', 'Income')->sum('amount');

        if ($totalIncome <= 0) {
            return response()->json(['message' => 'No income found to allocate budgets'], 400);
        }

        foreach ($request->percentages as $categoryId => $percentage) {
            $amount = ($percentage / 100) * $totalIncome;

            Budget::create([
                'id' => Str::uuid(),
                'user_id' => $userId,
                'category_id' => $categoryId,
                'amount' => round($amount, 2),
                'period' => 'Monthly',
                'start_date' => now()->startOfMonth(),
                'end_date' => now()->endOfMonth(),
                'status' => 'Active',
            ]);
        }

        return response()->json(['message' => 'Budgets allocated automatically']);
    }

    // ✅ Get User Budgets
    public function getBudgets()
    {
        $budgets = Budget::with('category')
            ->where('user_id', auth()->id())
            ->get();

        return response()->json(['budgets' => $budgets]);
    }

    // ✅ Update Budget
    public function updateBudget(Request $request, $id)
    {
        $budget = Budget::where('user_id', auth()->id())->findOrFail($id);

        $request->validate([
            'amount' => 'nullable|numeric',
            'status' => 'nullable|in:Active,Expired',
        ]);

        $budget->update($request->only('amount', 'status'));

        return response()->json(['message' => 'Budget updated successfully']);
    }

    // ✅ Delete Budget
    public function deleteBudget($id)
    {
        $budget = Budget::where('user_id', auth()->id())->findOrFail($id);
        $budget->delete();

        return response()->json(['message' => 'Budget deleted successfully']);
    }

    // ✅ Get Budget Summary
    public function getBudgetSummary()
    {
        $userId = auth()->id();

        $totalBudget = Budget::where('user_id', $userId)->sum('amount');
        $totalSpent = Budget::where('user_id', $userId)->sum('spent_amount');

        return response()->json([
            'total_budget' => $totalBudget,
            'total_spent' => $totalSpent,
            'remaining' => $totalBudget - $totalSpent,
        ]);
    }
}
