<?php

namespace IDcut\Jash\APIClient;

use GuzzleHttp\ClientInterface as HttpClientInterface;

interface IDcutInterface
{

    public function getVersion();

    public function setAccessToken($token);

    public function setHttpClient(HttpClientInterface $httpClient);
}
