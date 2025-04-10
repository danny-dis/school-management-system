<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class BaseApiController extends Controller
{
    use ApiResponses;

    /**
     * Default success status code
     *
     * @var int
     */
    protected $successStatusCode = 200;

    /**
     * Default error status code
     *
     * @var int
     */
    protected $errorStatusCode = 400;

    // Using methods from ApiResponses trait
}
