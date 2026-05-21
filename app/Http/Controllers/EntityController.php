<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Entity;
use App\Models\EntityUser;
use Database\Seeders\SystemLedgerSeeder;
use Illuminate\Support\Facades\Auth;

class EntityController extends Controller
{
    public function create()
    {
        return view('entities.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'owner_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'mobile' => 'required|string|max:20',
            'constitution' => 'required|in:Proprietorship,Partnership,LLP,Company,AOP,Trust,Other',
            'pan' => 'nullable|string|max:20',
            'gstin' => 'nullable|string|max:20',
            'is_msme' => 'boolean',
            'msme_no' => 'nullable|string|max:50',
            'msme_date' => 'nullable|date',
            'address' => 'nullable|string',
            'nature_of_business' => 'required|in:Service,Trading,Manufacturing',
            'accounting_period_year' => 'required|integer|min:1900|max:2100',
        ]);

        if ($request->session()->get('email_verified_for_entity') !== $validated['email']) {
            return back()->withErrors(['email' => 'You must verify your email address via OTP before creating an entity.'])->withInput();
        }

        $user = Auth::user();
        
        // Enforce 3 entity limit
        $entityCount = Entity::where('created_by_user_id', $user->id)->count();
        if ($entityCount >= 3) {
            return back()->withErrors(['limit' => 'You have reached the maximum limit of 3 entities for your current plan.']);
        }

        $validated['created_by_user_id'] = $user->id;
        $validated['is_msme'] = $request->has('is_msme');
        
        $entity = Entity::create($validated);

        // Assign Admin role to the creator
        EntityUser::create([
            'user_id' => $user->id,
            'entity_id' => $entity->id,
            'role' => 'Admin'
        ]);
        
        // Seed system ledgers for this entity
        (new SystemLedgerSeeder())->run($entity->id);

        // Set as active entity in session
        $request->session()->put('active_entity_id', $entity->id);

        return redirect()->route('dashboard')->with('success', 'Entity created successfully!');
    }
}
