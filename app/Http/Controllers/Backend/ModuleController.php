<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Modules\ModuleManager;
use App\Http\Helpers\AppHelper;

/**
 * ModuleController - Handles module management functionality
 * 
 * This controller provides functionality for administrators to manage
 * system modules, including viewing, enabling, and disabling modules.
 * 
 * @package App\Http\Controllers\Backend
 * @author Zophlic Development Team
 */
class ModuleController extends Controller
{
    /**
     * Display a listing of the modules.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Get all modules
        $modules = ModuleManager::getAllModules();
        
        return view('backend.modules.index', compact('modules'));
    }
    
    /**
     * Enable a module.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function enable(Request $request)
    {
        // Validate the request
        $this->validate($request, [
            'module_key' => 'required|string|max:50',
        ]);
        
        $moduleKey = $request->input('module_key');
        
        // Check if the module exists
        $moduleInfo = ModuleManager::getModuleInfo($moduleKey);
        if (!$moduleInfo) {
            return redirect()->route('modules.index')->with('error', 'Module not found.');
        }
        
        // Check dependencies
        foreach ($moduleInfo['dependencies'] as $dependency) {
            if (!ModuleManager::isModuleEnabled($dependency)) {
                $dependencyInfo = ModuleManager::getModuleInfo($dependency);
                $dependencyName = $dependencyInfo ? $dependencyInfo['name'] : $dependency;
                
                return redirect()->route('modules.index')->with('error', "This module depends on the {$dependencyName} module, which is not enabled.");
            }
        }
        
        // Enable the module
        if (ModuleManager::enableModule($moduleKey)) {
            return redirect()->route('modules.index')->with('success', "{$moduleInfo['name']} module has been enabled.");
        } else {
            return redirect()->route('modules.index')->with('error', "Failed to enable the {$moduleInfo['name']} module.");
        }
    }
    
    /**
     * Disable a module.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function disable(Request $request)
    {
        // Validate the request
        $this->validate($request, [
            'module_key' => 'required|string|max:50',
        ]);
        
        $moduleKey = $request->input('module_key');
        
        // Check if the module exists
        $moduleInfo = ModuleManager::getModuleInfo($moduleKey);
        if (!$moduleInfo) {
            return redirect()->route('modules.index')->with('error', 'Module not found.');
        }
        
        // Disable the module
        if (ModuleManager::disableModule($moduleKey)) {
            return redirect()->route('modules.index')->with('success', "{$moduleInfo['name']} module has been disabled.");
        } else {
            return redirect()->route('modules.index')->with('error', "Failed to disable the {$moduleInfo['name']} module. It may be required by other enabled modules.");
        }
    }
}
