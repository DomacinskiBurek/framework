<?php

namespace DomacinskiBurek\System;

use Redis;

class Cache
{
    function __construct ()
    {
        $redis = new Redis();
    }
}