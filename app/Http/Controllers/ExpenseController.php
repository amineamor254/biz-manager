<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index()
    {
        $expenses = Expense::query()
            ->orderByDesc('expense_date')
            ->orderByDesc('id')
            ->get();

        return view('expenses.index', compact('expenses'));
    }

    public function create()
    {
        $categories = Expense::CATEGORIES;

        return view('expenses.create', compact('categories'));
    }

    public function store(Request $request)
    {
        Expense::create($request->validate($this->rules()));

        return redirect()->route('expenses.index')->with('success', 'Expense added successfully.');
    }

    public function show(Expense $expense)
    {
        return view('expenses.show', compact('expense'));
    }

    public function edit(Expense $expense)
    {
        $categories = Expense::CATEGORIES;

        return view('expenses.edit', compact('expense', 'categories'));
    }

    public function update(Request $request, Expense $expense)
    {
        $expense->update($request->validate($this->rules()));

        return redirect()->route('expenses.show', $expense)->with('success', 'Expense updated successfully.');
    }

    public function destroy(Expense $expense)
    {
        $expense->delete();

        return redirect()->route('expenses.index')->with('success', 'Expense deleted successfully.');
    }

    private function rules(): array
    {
        return [
            'description' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'gt:0', 'max:9999999999.99'],
            'category' => ['required', 'string', 'max:100'],
            'expense_date' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:5000'],
        ];
    }
}