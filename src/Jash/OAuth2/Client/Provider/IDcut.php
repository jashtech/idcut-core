<?php

namespace IDcut\Jash\OAuth2\Client\Provider;

use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Entity\User;

class IDcut extends AbstractProvider {

    public $scopes = ['basic'];
    public $responseType = 'json';
    static $tokenInfoUrl = 'https://kickass.jash.fr/oauth/token/info';

    public function urlAuthorize()
    {
        return 'https://store.kickass.jash.fr/oauth/token/info';
    }

    public function urlAccessToken()
    {
        return 'https://kickass.jash.fr/oauth/token/info';
    }

    public function urlUserDetails(\League\OAuth2\Client\Token\AccessToken $token)
    {
        return '';
    }

    public function userDetails($response, \League\OAuth2\Client\Token\AccessToken $token)
    {
        $user = new User();
        return $user;
    }

    public function userUid($response, \League\OAuth2\Client\Token\AccessToken $token)
    {
        return $response->data->id;
    }

    public function userEmail($response, \League\OAuth2\Client\Token\AccessToken $token)
    {
        return;
    }

    public function userScreenName($response, \League\OAuth2\Client\Token\AccessToken $token)
    {
        return $response->data->full_name;
    }

}
