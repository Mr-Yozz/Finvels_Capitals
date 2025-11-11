<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExpenseController extends Controller
{
    public function index()
    {
        $expenses = Expense::with('category')->latest()->paginate(10);
        $categories = ExpenseCategory::all();

        return view('accounts.expenses', compact('expenses', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required',
            'amount' => 'required|numeric|min:1',
            'expense_date' => 'required|date',
        ]);

        Expense::create([
            'category_id' => $request->category_id,
            'amount' => $request->amount,
            'payment_mode' => $request->payment_mode,
            'expense_date' => $request->expense_date,
            'description' => $request->description,
            'added_by' => Auth::id(),
        ]);

        return back()->with('success', 'Expense added successfully.');
    }

    public function update(Request $request, $id)
    {
        $expense = Expense::findOrFail($id);

        $request->validate([
            'category_id' => 'required',
            'amount' => 'required|numeric|min:1',
            'expense_date' => 'required|date',
        ]);

        $expense->update($request->all());

        return back()->with('success', 'Expense updated successfully.');
    }

    public function destroy($id)
    {
        Expense::findOrFail($id)->delete();

        return back()->with('success', 'Expense deleted successfully.');
    }
}
