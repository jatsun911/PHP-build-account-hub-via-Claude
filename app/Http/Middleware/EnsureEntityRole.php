<?php

namespace App\Http\Middleware;

use App\Models\EntityUser;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureEntityRole
{
    // Role hierarchy: higher index = more permissions
    private const HIERARCHY = ['Read-only', 'Inputter', 'Manager', 'Admin'];

    public function handle(Request $request, Closure $next, string ...$roles)
    {
        $user = Auth::user();
        $entityId = $request->session()->get('active_entity_id');

        if (!$entityId) {
            return redirect()->route('dashboard');
        }

        $entityUser = EntityUser::where('user_id', $user->id)
            ->where('entity_id', $entityId)
            ->first();

        if (!$entityUser) {
            abort(403, 'You are not a member of this entity.');
        }

        $userRank = array_search($entityUser->role, self::HIERARCHY);

        foreach ($roles as $required) {
            $requiredRank = array_search($required, self::HIERARCHY);
            if ($userRank !== false && $userRank >= $requiredRank) {
                return $next($request);
            }
        }

        abort(403, 'You need at least ' . implode(' or ', $roles) . ' role for this action.');
    }
}
