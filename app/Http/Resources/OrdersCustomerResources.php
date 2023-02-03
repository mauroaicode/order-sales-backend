<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Jenssegers\Date\Date;

class OrdersCustomerResources extends JsonResource
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
            'products' => $this->products,
            'purchase_date' =>  Date::parse($this->purchase_date)->locale('es')->format('l d F Y'),
            'total' => $this->total,
            'created_at' => Date::parse($this->created_at)->locale('es')->format('l d F Y'),
            'customer' => $this->customer->user->name,
        ];
    }
}
