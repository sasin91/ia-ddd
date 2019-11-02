<?php

namespace App\Http\Resources;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class AuthenticatedUserResource
 * @package App\Http\Resources
 * @mixin User
 */
class AuthenticatedUserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'username' => $this->username,
            'email' => $this->email,
            'photo_url' => $this->photo_url,
            'country_code' => $this->country_code,
            'phone' => $this->phone,

            'authorization' => [
                'viewNova' => $this->hasPermissionTo('view Nova')
            ]
        ];
    }
}
