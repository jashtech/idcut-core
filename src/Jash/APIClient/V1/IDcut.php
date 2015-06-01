<?php

namespace IDcut\Jash\APIClient\V1;

use IDcut\Jash\APIClient\IDcutAbstract as IDcutAbstract;
use IDcut\Jash\APIClient\IDcutInterface as IDcutInterface;
use GuzzleHttp\ClientInterface as HttpClientInterface;

class IDcut extends IDcutAbstract implements IDcutInterface
{
    protected $version    = 1;
    protected $serviceUrl = "https://api.kickass.jash.fr";

    public function get($query, $headers=true)
    {
        try {
            if($headers){
                return $this->httpClient->get($query);
            }else{
                return $this->httpClient->get($query)->getBody();
            }

        } catch (\Exception $e) {
            //echo $e->getMessage();
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