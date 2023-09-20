<?php

namespace App\Http\Resources\Integrations\Hubspot;

use Illuminate\Http\Resources\Json\JsonResource;
use Symfony\Component\HttpFoundation\Response;

class HubspotErrorResource extends JsonResource
{
    /**
     * @param $request
     * @param $response
     * @return void
     */
    public function withResponse($request, $response)
    {
        $response->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
