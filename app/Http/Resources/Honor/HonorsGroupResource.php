<?php

namespace App\Http\Resources\Honor;

use Illuminate\Http\Resources\Json\ResourceCollection;

class HonorsGroupResource extends ResourceCollection
{
    public $collects = HonorResource::class;

    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return parent::toArray($request);
    }
}
