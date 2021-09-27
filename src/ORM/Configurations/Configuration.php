<?php

namespace Electronics\Database\ORM\Configurations;

use Electronics\Database\ORM\Mappings\EntityMap;

interface Configuration
{
    function retrieveEntityMap(string|\stdClass $entity): EntityMap;
}