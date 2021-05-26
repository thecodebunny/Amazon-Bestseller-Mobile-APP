<?php

namespace Thecodebunny\PWA\Http\Resources\Customer;

use Illuminate\Http\Resources\Json\JsonResource;
use Thecodebunny\PWA\Http\Resources\Catalog\Product as ProductResource;

class Comparison extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'product' => new ProductResource($this->product),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}