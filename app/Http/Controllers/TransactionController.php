<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        // Allowed sort columns to prevent SQL injection
        $allowedSorts = ['transaction_date', 'amount', 'type', 'status', 'description'];
        
        $sortColumn = $request->get('sort', 'transaction_date');
        if (!in_array($sortColumn, $allowedSorts)) {
            $sortColumn = 'transaction_date';
        }
        
        $sortDirection = $request->get('direction', 'desc') === 'asc' ? 'asc' : 'desc';

        $transactions = Transaction::orderBy($sortColumn, $sortDirection)->paginate(15);

        return view('transactions.index', compact('transactions', 'sortColumn', 'sortDirection'));
    }
}
