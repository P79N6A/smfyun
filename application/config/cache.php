<?php defined('SYSPATH') or die('No direct script access.');

return array
(
    'memcache' => array
    (
        'driver'             => 'memcached',
        'default_expire'     => 3600,
        'compression'        => FALSE,              // Use Zlib compression (can cause issues with integers)
        'servers'            => array
        (
            array
            (
                // 'host'             => 'localhost',
                'host'             => '180.76.243.74',
                'port'             => 11211,
                'persistent'       => FALSE,
                'weight'           => 1,
                'timeout'          => 1,
                'retry_interval'   => 15,
                'status'           => TRUE,
            ),
        ),
        'instant_death'      => TRUE,               // Take server offline immediately on first fail (no retry)
    ),
);
