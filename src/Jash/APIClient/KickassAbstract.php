<?php

namespace Kickass\Jash\APIClient;

use GuzzleHttp\ClientInterface as HttpClientInterface;
use GuzzleHttp\Exception\RequestException as RequestException;

abstract class KickassAbstract implements KickassInterface
{

    protected $version;
    protected $serviceUrl;
    protected $accessToken;
    protected $httpClient;

    public function getVersion()
    {
        return $this->version;
    }

    public function getServiceUrl()
    {
        return $this->serviceUrl;
    }

    public function setAccessToken($token)
    {
        $this->accessToken = $token;
    }

    public function setHttpClient(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
        $this->httpClient->setDefaultOption('headers', array(
            'Accept' => 'application/vnd.kickass.' . $this->version,
            'Accept-Language' => 'en',
            'Authorization' => 'Bearer ' . $this->accessToken
        ));
    }

    public function test()
    {
        try {
            return $response = $this->httpClient->get('/ping');
        } catch (RequestException $e) {
            echo $e->getRequest() . "\n";
            if ($e->hasResponse()) {
                return $e->getResponse() . "\n";
            }
        }
    }

}
