<?php

namespace App\Domains\Listings\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class CategoryCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'data' => $this->collection->map(function ($category) {
                return new CategoryResource($category);
            }),
            'links' => [
                'self' => url()->current(),
            ],
        ];
    }
}
