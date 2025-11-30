<?php

namespace App\Http\Controllers;

use App\Models\ExpenseCategory;
use Illuminate\Http\Request;

class AccountCategoryController extends Controller
{
    //
    public function index()
    {
        $categories = ExpenseCategory::latest()->get();
        return view('accounts.categories', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:income,expense',
        ]);

        ExpenseCategory::create($request->all());
        return back()->with('success', 'Category added successfully!');
    }

    public function destroy($id)
    {
        ExpenseCategory::findOrFail($id)->delete();
        return back()->with('success', 'Category deleted!');
    }

    public function Accounts(){
        
    }
}
