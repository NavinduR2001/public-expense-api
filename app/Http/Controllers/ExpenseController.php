<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    // Get all expenses
    public function index()
    {
        $expenses = Expense::with('category')->get();
        return response()->json($expenses);
    }

    // Get single expense
    public function show($id)
    {
        $expense = Expense::with('category')->findOrFail($id);
        return response()->json($expense);
    }

    // Create expense
    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
            'description' => 'nullable|string',
        ]);

        $expense = Expense::create([
            'category_id' => $request->category_id,
            'amount' => $request->amount,
            'expense_date' => $request->expense_date,
            'description' => $request->description,
        ]);

        return response()->json($expense->load('category'), 201);
    }

    // Update expense
    public function update(Request $request, $id)
    {
        $expense = Expense::findOrFail($id);

        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
            'description' => 'nullable|string',
        ]);

        $expense->update([
            'category_id' => $request->category_id,
            'amount' => $request->amount,
            'expense_date' => $request->expense_date,
            'description' => $request->description,
        ]);

        return response()->json($expense->load('category'));
    }

    // Delete expense
    public function destroy($id)
    {
        $expense = Expense::findOrFail($id);
        $expense->delete();

        return response()->json(['message' => 'Expense deleted successfully']);
    }

    // Get expenses by date range
    public function byDateRange(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $expenses = Expense::whereBetween('expense_date', [
                $request->start_date,
                $request->end_date,
            ])
            ->with('category')
            ->get();

        return response()->json($expenses);
    }
}
