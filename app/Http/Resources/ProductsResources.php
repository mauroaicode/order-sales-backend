<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Jenssegers\Date\Date;

class ProductsResources extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->product_name,
            'description' => $this->product_description,
            'price' => $this->price,
            'picture' => $this->picture,
            'created_at' => Date::parse($this->created_at)->locale('es')->format('l d F Y'),
        ];
    }
}
