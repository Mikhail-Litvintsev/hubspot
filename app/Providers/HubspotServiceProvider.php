<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use UseDesk\Hubspot\API\Auth\OAuthClientInterface;
use UseDesk\Hubspot\API\Auth\OAuthClient;
use UseDesk\Hubspot\API\ApiClients\ApiClientInterface;
use UseDesk\Hubspot\API\ApiClients\DiscoveryApiClient;

class HubspotServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->bind(
            OAuthClientInterface::class,
            OAuthClient::class
        );
        $this->app->bind(
            ApiClientInterface::class,
            DiscoveryApiClient::class
        );

    }
}
