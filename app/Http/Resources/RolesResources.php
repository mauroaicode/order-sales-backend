<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Jenssegers\Date\Date;

class RolesResources extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

        return [
            'id' => $this->id,
            'name' => $this->name,
            'created_at' => Date::parse($this->created_at)->locale('es')->format('l d F Y'),
        ];
    }
}
