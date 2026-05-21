<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Entity;
use App\Models\BankStatement;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        
        $entities = $user->entities;
        
        if ($entities->count() === 0) {
            return redirect()->route('entities.create');
        }
        
        // Check if an active entity is selected in session, otherwise default to first
        $activeEntityId = $request->session()->get('active_entity_id');
        $activeEntity = $entities->where('id', $activeEntityId)->first() ?? $entities->first();
        
        // Ensure session always has a valid active entity
        if (!$activeEntityId || !$entities->where('id', $activeEntityId)->first()) {
            $request->session()->put('active_entity_id', $activeEntity->id);
        }

        // Fetch data scoped to active entity
        $statements = BankStatement::where('entity_id', $activeEntity->id)->latest()->get();
        
        // We will build out the rest of the dashboard UI later, for now pass entities for the sidebar
        return view('welcome', compact('statements', 'entities', 'activeEntity'));
    }
}
