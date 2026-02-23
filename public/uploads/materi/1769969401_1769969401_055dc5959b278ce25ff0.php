<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Filters extends BaseConfig
{
    /**
     * --------------------------------------------------------------------
     * Alias Definitions
     * --------------------------------------------------------------------
     */
    public $aliases = [
        'csrf'      => \CodeIgniter\Filters\CSRF::class,
        'toolbar'   => \CodeIgniter\Filters\DebugToolbar::class,
        'honeypot'  => \CodeIgniter\Filters\Honeypot::class,
        'auth'      => \App\Filters\AuthFilter::class,
        'role'      => \App\Filters\RoleFilter::class,
    ];

    /**
     * --------------------------------------------------------------------
     * Global Filters
     * --------------------------------------------------------------------
     */
    public $globals = [
        'before' => [
            // 'honeypot',
            // 'csrf',
        ],
        'after' => [
            // 'toolbar',
        ],
    ];

    /**
     * --------------------------------------------------------------------
     * Method Filters
     * --------------------------------------------------------------------
     */
    public $methods = [];

    /**
     * --------------------------------------------------------------------
     * Route Filters
     * --------------------------------------------------------------------
     */
    public $filters = [];
}
