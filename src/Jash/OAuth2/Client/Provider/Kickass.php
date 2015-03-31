<?php

namespace Kickass\Jash\OAuth2\Client\Provider;

use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Entity\User;

class Kickass extends AbstractProvider {

    public $scopes = ['basic'];
    public $responseType = 'json';

    public function urlAuthorize()
    {
        return 'https://kickass.jash.fr/oauth/authorize';
    }

    public function urlAccessToken()
    {
        return 'https://kickass.jash.fr/oauth/token';
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
