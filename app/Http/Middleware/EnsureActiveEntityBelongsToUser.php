<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureActiveEntityBelongsToUser
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        $activeEntityId = $request->session()->get('active_entity_id');

        if ($activeEntityId) {
            $owns = $user->entities()->wherePivot('entity_id', $activeEntityId)->exists();
            if (!$owns) {
                $request->session()->forget('active_entity_id');
                return redirect()->route('dashboard')
                    ->withErrors(['entity' => 'Access denied: that entity does not belong to your account.']);
            }
        }

        return $next($request);
    }
}
