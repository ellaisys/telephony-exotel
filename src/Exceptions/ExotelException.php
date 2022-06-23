<?php

namespace Ellaisys\Exotel\Exceptions;

use Exception;
use Illuminate\Support\Facades\Log;

class ExotelException extends Exception
{

    /**
     * HTTP Status Codes: Exotel uses standard HTTP status codes to communicate errors
     * 
     * Status Code: Value
     * 200: OK - Everything went as planned.
     * 202: Accepted - Request accepted.
     * 400: Bad Request - Something in your header or request body was malformed.
     * 401: Unauthorised - Necessary credentials were either missing or invalid.
     * 402: Payment Required - The action is not available on your plan, or you have exceeded usage limits for your current plan.
     * 403: Your credentials are valid, but you don’t have access to the requested resource.
     * 404: Not Found - The object you’re requesting doesn’t exist.
     * 409: Conflict - You might be trying to update the same resource concurrently.
     * 429: Too Many Requests - You are calling our APIs more frequently than we allow.
     */

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