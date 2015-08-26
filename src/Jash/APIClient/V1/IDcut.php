<?php

namespace IDcut\Jash\APIClient\V1;

use IDcut\Jash\APIClient\IDcutAbstract as IDcutAbstract;
use IDcut\Jash\APIClient\IDcutInterface as IDcutInterface;
use GuzzleHttp\ClientInterface as HttpClientInterface;

class IDcut extends IDcutAbstract implements IDcutInterface
{
    protected $version    = 1;
    protected $serviceUrl = "http://api.dev.idealcutter.com";

    public function get($query)
    {
        try {
             return $this->httpClient->get($query);
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    public function put($query, $body = null)
    {
        try {
            return $request = $this->httpClient->put($query,
                array(
                'body' => $body,
                'headers' => array('Content-type' => 'application/json')
            ));
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }
    
    public function post($query, $body = null)
    {
        try {
            return $request = $this->httpClient->post($query,
                array(
                'body' => $body,
                'headers' => array('Content-type' => 'application/json')
            ));
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    public function getTokenInfo()
    {
        return $this->get(\IDcut\Jash\OAuth2\Client\Provider\IDcut::$tokenInfoUrl);
    }

    public function test()
    {
        return $response = $this->get('/ping');
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
}