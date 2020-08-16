<?php

namespace Ellaisys\Exotel\Exceptions;

use Exception;
use Illuminate\Support\Facades\Log;

class ExotelException extends Exception
{

    /**
     * Create exotel exception instance.
     *
     * @param string     $message
     * @param \Exception $previous
     * @param array      $headers
     * @param int        $code
     *
     * @return void
     */
    public function __construct(Exception $previous = null, $headers = [], $code = 0)
    {
        $message = 'EXCEPTION_EXOTEL_EXCEPTION';
        parent::__construct(400, $message ?: 'You have an exotel exception.', $previous, $headers, $code);
    }
}