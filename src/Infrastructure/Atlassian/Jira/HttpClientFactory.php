<?php

namespace Marble\JiraKpi\Infrastructure\Atlassian\Jira;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class HttpClientFactory
{
    public function createClient(string $basePath): HttpClientInterface
    {
        return HttpClient::create([
            'base_uri'   => 'https://autarco.atlassian.net' . $basePath,
            'headers'    => [
                'Accept'       => 'application/json',
                'Content-Type' => 'application/json',
            ],
            'auth_basic' => [
                $_ENV['ATLASSIAN_API_USER'],
                $_ENV['ATLASSIAN_API_KEY'],
            ],
        ]);
    }
}
