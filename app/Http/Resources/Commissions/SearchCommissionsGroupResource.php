<?php

namespace App\Http\Resources\Commissions;

use Illuminate\Http\Resources\Json\ResourceCollection;

class SearchCommissionsGroupResource extends ResourceCollection
{
    public $collects = SearchCommissionResource::class;

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
