<?php

namespace App\Http\Resources\Ranks;

use Illuminate\Http\Resources\Json\ResourceCollection;

class RanksGroupResource extends ResourceCollection
{
    public $collects = RankResource::class;

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
