<?php

namespace IDcut\Jash\APIClient\V1;

use IDcut\Jash\APIClient\IDcutAbstract as IDcutAbstract;
use IDcut\Jash\APIClient\IDcutInterface as IDcutInterface;

class IDcut extends IDcutAbstract implements IDcutInterface
{

    protected $version = 1;
    protected $serviceUrl = "https://api.kickass.jash.fr";

    }
