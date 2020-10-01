<?php

declare(strict_types=1);

namespace TerryApiBundle\HttpClient;

use Symfony\Component\HttpFoundation\Request;

final class HttpClientFactory
{
    private ServerSettingsFactory $serverSettingsFactory;

    public function __construct(ServerSettingsFactory $serverSettingsFactory)
    {
        $this->serverSettingsFactory = $serverSettingsFactory;
    }

    public function fromRequest(Request $request): HttpClient
    {
        return HttpClient::new($request, $this->serverSettingsFactory->fromConfig());
    }
}
