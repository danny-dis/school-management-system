<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BaseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return parent::toArray($request);
    }

    /**
     * Create a new resource instance with success message.
     *
     * @param  mixed  $resource
     * @param  string|null  $message
     * @return static
     */
    public static function make($resource, ?string $message = null)
    {
        $instance = parent::make($resource);

        if ($message) {
            $instance->additional(['message' => $message]);
        }

        return $instance->additional(['success' => true]);
    }

    /**
     * Create a new anonymous resource collection with success message.
     *
     * @param  mixed  $resource
     * @param  string|null  $message
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public static function collection($resource, ?string $message = null)
    {
        $collection = parent::collection($resource);

        if ($message) {
            $collection->additional(['message' => $message]);
        }

        return $collection->additional(['success' => true]);
    }
}
