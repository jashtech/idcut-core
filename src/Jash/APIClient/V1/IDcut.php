<?php
namespace IDcut\Jash\APIClient\V1;

use IDcut\Jash\APIClient\IDcutAbstract as IDcutAbstract;
use IDcut\Jash\APIClient\IDcutInterface as IDcutInterface;
use GuzzleHttp\ClientInterface as HttpClientInterface;
use GuzzleHttp\Exception\RequestException as RequestException;
use GuzzleHttp\Exception\ConnectException as ConnectException;
use GuzzleHttp\Exception\BadResponseException as BadResponseException;
use GuzzleHttp\Exception\ClientException as ClientException;
use GuzzleHttp\Exception\TransferException as TransferException;
use GuzzleHttp\Psr7\Request as Request;

class IDcut extends IDcutAbstract implements IDcutInterface
{
    protected $version    = 1;
    protected $serviceUrl = "https://api.dev.idealcutter.com";
    protected $defaultOptions = array();

    public function get($url, Array $options = array())
    {
        $options = array_merge_recursive($this->defaultOptions, $options);
        $request = new Request("GET", $url);

        return $this->send($request, $options);
    }

    public function put($url, $body = null)
    {
        $request = new Request("PUT", $url);
        $options = array_merge_recursive($this->defaultOptions, array(
            'body' => $body,
            'headers' => array('Content-type' => 'application/json')
        ));

        return $this->send($request, $options);
    }

    public function post($url, $body = null)
    {
        $request = new Request("POST", $url);
        $options = array_merge_recursive($this->defaultOptions, array(
            'body' => $body,
            'headers' => array('Content-type' => 'application/json')
        ));

        return $this->send($request, $options);
    }

    public function getTokenInfo()
    {
        return $this->get(\IDcut\Jash\OAuth2\Client\Provider\IDcut::$tokenInfoUrl);
    }

    public function test()
    {
        return $response = $this->get('/ping');
    }

    public function setHttpClient(HttpClientInterface $httpClient,
                                  $lang = 'en;q=1.0')
    {
        $this->httpClient = $httpClient;

        $this->defaultOptions = array(
            'headers' => array(
                'Accept' => 'application/vnd.kickass.'.$this->version,
                'Accept-Language' => $lang,
                'Authorization' => 'Bearer '.$this->accessToken
            ),
            'verify' => false
        );
    }
}