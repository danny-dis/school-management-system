<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\LicensingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LicenseController extends Controller
{
    /**
     * @var LicensingService
     */
    protected $licensingService;

    /**
     * Create a new controller instance.
     *
     * @param LicensingService $licensingService
     * @return void
     */
    public function __construct(LicensingService $licensingService)
    {
        $this->middleware('auth');
        $this->middleware('role:Super Admin');
        $this->licensingService = $licensingService;
    }

    /**
     * Display the license management page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $licenseDetails = $this->licensingService->getLicenseDetails();
        $licensedModules = $this->licensingService->getLicensedModules();
        $daysUntilExpiration = $this->licensingService->getDaysUntilExpiration();
        $daysUntilSupportExpiration = $this->licensingService->getDaysUntilSupportExpiration();
        
        return view('admin.license.index', compact(
            'licenseDetails',
            'licensedModules',
            'daysUntilExpiration',
            'daysUntilSupportExpiration'
        ));
    }

    /**
     * Validate a license key.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function validateLicense(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'license_key' => 'required|string|min:16|max:64',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        $result = $this->licensingService->validateLicense($request->license_key);
        
        if ($result['success']) {
            return redirect()->route('admin.license.index')
                ->with('success', $result['message']);
        }
        
        return redirect()->back()
            ->with('error', $result['message'])
            ->withInput();
    }

    /**
     * Clear the license cache.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function clearCache()
    {
        $this->licensingService->clearLicenseCache();
        
        return redirect()->route('admin.license.index')
            ->with('success', 'License cache cleared successfully.');
    }
}
