<?php

namespace NabilAnam\SimpleUpload\Facades;

use Illuminate\Support\Facades\Facade;

class SimpleUpload extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'simpleupload';
    }
}
