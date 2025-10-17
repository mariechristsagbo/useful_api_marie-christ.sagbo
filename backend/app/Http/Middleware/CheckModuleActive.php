<?php

namespace App\Http\Middleware;

use App\Models\Module;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckModuleActive
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $moduleName = null): Response
    {
        $user = $request->user();
        
        if (!$user) {
            return response()->json(['message' => 'Not authentificated'], 401);
        }

        if (!$moduleName) {
            $hasActiveModule = $user->modules()
                ->wherePivot('active', true)
                ->exists();
            
            if (!$hasActiveModule) {
                return response()->json([
                    'message' => 'Aucun module actif.'
                ], 403);
            }
            
            return $next($request);
        }

        $module = Module::where('name', $moduleName)->first();
        
        if (!$module) {
            return response()->json(['message' => 'Module not found'], 404);
        }

        $userModule = $user->modules()
            ->where('module_id', $module->id)
            ->wherePivot('active', true)
            ->first();

        if (!$userModule) {
            return response()->json([
                'message' => 'Module inactive. Please activate this module to use it.',
                'module' => $moduleName
            ], 403);
        }

        return $next($request);
    }
}
