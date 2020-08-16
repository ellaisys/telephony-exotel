<?php

namespace Ellaisys\Exotel\Exceptions;

use Exception;
use Illuminate\Support\Facades\Log;

class ExotelException extends Exception
{
    /**
     * Report or log an exception.
     *
     * @return void
     */
    public function report()
    {
        Log::debug('Exotel SMS Exception');
    }
}