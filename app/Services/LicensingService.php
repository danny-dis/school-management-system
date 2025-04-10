<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class LicensingService
{
    /**
     * Cache key for license
     *
     * @var string
     */
    protected $cacheKey = 'system_license';
    
    /**
     * Cache TTL in minutes
     *
     * @var int
     */
    protected $cacheTtl = 1440; // 24 hours
    
    /**
     * License server URL
     *
     * @var string
     */
    protected $licenseServer = 'https://license.zophlic.com';
    
    /**
     * Validate license
     *
     * @param string $licenseKey
     * @return array
     */
    public function validateLicense($licenseKey)
    {
        try {
            $response = Http::post($this->licenseServer . '/api/validate', [
                'license_key' => $licenseKey,
                'domain' => request()->getHost(),
                'ip' => request()->ip(),
                'app_version' => config('app.version'),
            ]);
            
            if ($response->successful()) {
                $data = $response->json();
                
                if ($data['valid']) {
                    $this->storeLicense($data);
                    return [
                        'success' => true,
                        'message' => 'License validated successfully.',
                        'data' => $data
                    ];
                }
                
                return [
                    'success' => false,
                    'message' => $data['message'] ?? 'Invalid license key.',
                    'data' => null
                ];
            }
            
            return [
                'success' => false,
                'message' => 'Failed to validate license. Please try again later.',
                'data' => null
            ];
        } catch (Exception $e) {
            Log::error('Error validating license: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            
            return [
                'success' => false,
                'message' => 'An error occurred while validating the license.',
                'data' => null
            ];
        }
    }
    
    /**
     * Store license in cache
     *
     * @param array $data
     * @return void
     */
    protected function storeLicense($data)
    {
        Cache::put($this->cacheKey, $data, $this->cacheTtl);
    }
    
    /**
     * Get license from cache
     *
     * @return array|null
     */
    public function getLicense()
    {
        return Cache::get($this->cacheKey);
    }
    
    /**
     * Check if license is valid
     *
     * @return bool
     */
    public function isLicenseValid()
    {
        $license = $this->getLicense();
        
        if (!$license) {
            return false;
        }
        
        // Check if license has expired
        if (isset($license['expires_at']) && !empty($license['expires_at'])) {
            $expiresAt = strtotime($license['expires_at']);
            
            if ($expiresAt < time()) {
                return false;
            }
        }
        
        return $license['valid'] ?? false;
    }
    
    /**
     * Check if module is licensed
     *
     * @param string $moduleKey
     * @return bool
     */
    public function isModuleLicensed($moduleKey)
    {
        $license = $this->getLicense();
        
        if (!$license || !isset($license['modules']) || !is_array($license['modules'])) {
            return false;
        }
        
        return in_array($moduleKey, $license['modules']);
    }
    
    /**
     * Get licensed modules
     *
     * @return array
     */
    public function getLicensedModules()
    {
        $license = $this->getLicense();
        
        if (!$license || !isset($license['modules']) || !is_array($license['modules'])) {
            return [];
        }
        
        return $license['modules'];
    }
    
    /**
     * Clear license cache
     *
     * @return void
     */
    public function clearLicenseCache()
    {
        Cache::forget($this->cacheKey);
    }
    
    /**
     * Get license details
     *
     * @return array
     */
    public function getLicenseDetails()
    {
        $license = $this->getLicense();
        
        if (!$license) {
            return [
                'valid' => false,
                'customer_name' => null,
                'customer_email' => null,
                'license_key' => null,
                'expires_at' => null,
                'modules' => [],
                'max_students' => 0,
                'max_teachers' => 0,
                'max_employees' => 0,
                'support_expires_at' => null
            ];
        }
        
        return [
            'valid' => $license['valid'] ?? false,
            'customer_name' => $license['customer_name'] ?? null,
            'customer_email' => $license['customer_email'] ?? null,
            'license_key' => $license['license_key'] ?? null,
            'expires_at' => $license['expires_at'] ?? null,
            'modules' => $license['modules'] ?? [],
            'max_students' => $license['max_students'] ?? 0,
            'max_teachers' => $license['max_teachers'] ?? 0,
            'max_employees' => $license['max_employees'] ?? 0,
            'support_expires_at' => $license['support_expires_at'] ?? null
        ];
    }
    
    /**
     * Check if license has expired
     *
     * @return bool
     */
    public function hasLicenseExpired()
    {
        $license = $this->getLicense();
        
        if (!$license || !isset($license['expires_at']) || empty($license['expires_at'])) {
            return true;
        }
        
        $expiresAt = strtotime($license['expires_at']);
        
        return $expiresAt < time();
    }
    
    /**
     * Get days until license expires
     *
     * @return int
     */
    public function getDaysUntilExpiration()
    {
        $license = $this->getLicense();
        
        if (!$license || !isset($license['expires_at']) || empty($license['expires_at'])) {
            return 0;
        }
        
        $expiresAt = strtotime($license['expires_at']);
        $now = time();
        
        if ($expiresAt <= $now) {
            return 0;
        }
        
        return floor(($expiresAt - $now) / (60 * 60 * 24));
    }
    
    /**
     * Check if support has expired
     *
     * @return bool
     */
    public function hasSupportExpired()
    {
        $license = $this->getLicense();
        
        if (!$license || !isset($license['support_expires_at']) || empty($license['support_expires_at'])) {
            return true;
        }
        
        $expiresAt = strtotime($license['support_expires_at']);
        
        return $expiresAt < time();
    }
    
    /**
     * Get days until support expires
     *
     * @return int
     */
    public function getDaysUntilSupportExpiration()
    {
        $license = $this->getLicense();
        
        if (!$license || !isset($license['support_expires_at']) || empty($license['support_expires_at'])) {
            return 0;
        }
        
        $expiresAt = strtotime($license['support_expires_at']);
        $now = time();
        
        if ($expiresAt <= $now) {
            return 0;
        }
        
        return floor(($expiresAt - $now) / (60 * 60 * 24));
    }
}
