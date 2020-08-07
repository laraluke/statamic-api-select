<?php

namespace Laraluke\ApiSelectFieldtype;

use Statamic\Providers\AddonServiceProvider;

class ServiceProvider extends AddonServiceProvider
{
    protected $fieldtypes = [
        ApiSelectFieldtype::class,
    ];

    protected $scripts = [
        __DIR__.'/../dist/js/api_select-fieldtype.js',
    ];
}
