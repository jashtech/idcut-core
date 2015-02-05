<?php

namespace Kickass\Jash\APIClient;

interface KickassInterface {
    public function getVersion();
    public function setAccessToken($token);
}
