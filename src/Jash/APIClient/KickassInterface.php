<?php

namespace Kickass\Jash\APIClient;

use GuzzleHttp\ClientInterface as HttpClientInterface;

interface KickassInterface
{

    public function getVersion();

    public function setAccessToken($token);

    public function setHttpClient(HttpClientInterface $httpClient);
}
