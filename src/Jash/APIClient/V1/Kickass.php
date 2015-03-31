<?php

namespace Kickass\Jash\APIClient\V1;

use Kickass\Jash\APIClient\KickassAbstract as KickassAbstract;
use Kickass\Jash\APIClient\KickassInterface as KickassInterface;

class Kickass extends KickassAbstract implements KickassInterface
{

    protected $version = 1;
    protected $serviceUrl = "https://api.kickass.jash.fr";

    }
