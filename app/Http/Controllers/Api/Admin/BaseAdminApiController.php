<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\BaseApiController;

class BaseAdminApiController extends BaseApiController
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // Apply any common admin API controller logic here
        $this->middleware('auth:sanctum');
        $this->middleware('api.role:Admin');
    }
}
