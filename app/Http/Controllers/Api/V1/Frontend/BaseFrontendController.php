<?php

namespace App\Http\Controllers\Api\V1\Frontend;

use App\Http\Controllers\Api\V1\BaseV1Controller;

class BaseFrontendController extends BaseV1Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        
        // Apply any common Frontend API controller logic here
        $this->middleware('auth:sanctum');
    }
}
