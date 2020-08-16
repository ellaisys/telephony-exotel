<?php

namespace Ellaisys\Exotel\Exceptions;

use Exception;
use Illuminate\Support\Facades\Log;

class ConfigNotDefinedException extends Exception
{
    /**
     * Report or log an exception.
     *
     * @return void
     */
    public function report()
    {
        Log::debug('Exotel SMS Config NotDefined Exception');
    }
}