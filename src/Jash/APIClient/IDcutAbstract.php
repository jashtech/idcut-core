<?php

namespace IDcut\Jash\APIClient;

use GuzzleHttp\ClientInterface as HttpClientInterface;
use GuzzleHttp\Exception\RequestException as RequestException;

abstract class IDcutAbstract implements IDcutInterface
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
        return $this;
    }

    public function setHttpClient(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
        $this->httpClient->setDefaultOption('headers',
            array(
            'Accept' => 'application/vnd.kickass.'.$this->version,
            'Accept-Language' => 'en',
            'Authorization' => 'Bearer '.$this->accessToken
        ));
    }

    public function getTokenInfo()
    {
        try {
            $response = $this->httpClient->get(\IDcut\Jash\OAuth2\Client\Provider\IDcut::$tokenInfoUrl);
        } catch (RequestException $e) {
            echo $e->getRequest()."\n";
            if ($e->hasResponse()) {
                $response = $e->getResponse()."\n";
            }
        }

        return $response;
    }

    public function test()
    {
        try {
            return $response = $this->httpClient->get('/ping');
        } catch (RequestException $e) {
            echo $e->getRequest()."\n";
            if ($e->hasResponse()) {
                return $e->getResponse()."\n";
            }
        }
    }

   
}