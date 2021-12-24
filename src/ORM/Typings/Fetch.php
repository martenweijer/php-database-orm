<?php

namespace Electronics\Database\ORM\Typings;

enum Fetch
{
    case EAGER;
    case LAZY;
}