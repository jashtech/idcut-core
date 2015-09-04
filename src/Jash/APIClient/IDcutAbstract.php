<?php

namespace IDcut\Jash\APIClient;

use GuzzleHttp\ClientInterface as HttpClientInterface;
use GuzzleHttp\Exception\RequestException as RequestException;
use GuzzleHttp\Exception\ConnectException as ConnectException;
use GuzzleHttp\Exception\BadResponseException as BadResponseException;
use GuzzleHttp\Exception\ClientException as ClientException;
use GuzzleHttp\Exception\TransferException as TransferException;
use IDcut\Jash\Exception\IDCut as IDCutException;

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
    }


    protected function send($request, $params = array())
    {
        return $this->httpClient->send($request, $params);
    }
}