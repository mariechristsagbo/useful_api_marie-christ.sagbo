<?php

namespace App\Http\Controllers;

use App\Models\Module;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ModuleController extends Controller
{

    //récuperer tout les modules

    public function index()
    {
        $modules = Module::all();
        
        return response()->json([
            'modules' => $modules
        ]);
    }

   //activer un module
    public function activate(Request $request, $moduleId)
    {
        $user = $request->user();
        $module = Module::findOrFail($moduleId);

        $existingModule = $user->modules()->where('module_id', $moduleId)->first();
        
        if ($existingModule) {
            $user->modules()->updateExistingPivot($moduleId, ['active' => true]);
        } else {
            $user->modules()->attach($moduleId, ['active' => true]);
        }

        return response()->json([
            'message' => 'Module activated',
            'module' => $module
        ], 200);
    }

    //désactiver un module
    public function deactivate(Request $request, $moduleId)
    {
        $user = $request->user();
        $module = Module::findOrFail($moduleId);

        $existingModule = $user->modules()->where('module_id', $moduleId)->first();
        
        if (!$existingModule) {
            return response()->json([
                'message' => 'Module not found'
            ], 404);
        }

        $user->modules()->updateExistingPivot($moduleId, ['active' => false]);

        return response()->json([
            'message' => 'Module deactivated',
            'module' => $module
        ], 200);
    }
}
