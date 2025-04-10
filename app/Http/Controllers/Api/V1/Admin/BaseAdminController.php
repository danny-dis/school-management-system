<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Api\V1\BaseV1Controller;

class BaseAdminController extends BaseV1Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        
        // Apply any common Admin API controller logic here
        $this->middleware('auth:sanctum');
        $this->middleware('api.role:Admin');
    }
}
