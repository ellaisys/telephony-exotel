<?php

namespace Ellaisys\Exotel;

use Config;
use Illuminate\Support\Facades\Log;

use Ellaisys\Exotel\Traits\ExotelAction;
use Ellaisys\Exotel\Traits\ExotelCallAction;
use Ellaisys\Exotel\Enums\Call\CallType;

use Exception;
use Ellaisys\Exotel\Exceptions\ExotelException;
use Ellaisys\Exotel\Exceptions\ConfigNotDefinedException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;;

class ExotelCall {
    use ExotelAction;
    use ExotelCallAction;


    /**
     * Exotel Settings
     *
     * @var \array|null
     */
    protected $settings;

    
    /**
     * Default Constructor.
     *
     * @return void
     */
    public function __construct(array $settings=null)
    {
        if (!empty($settings)) {
            $this->settings = $settings;
        } //End if        
    }


    /**
     * Static Method
     */
    public static function dial(string $numberFrom,  string $numberTo, string $callerId,
        array $settings=null, string $callbackUrl=null, $callType=null)
    {
        return (new static())->makeCall(
            $numberFrom,  $numberTo, $callerId, 
            $settings, $callbackUrl, $callType
        );
    } //Function ends


    /**
    * Make an Exotel Connection Call
    *
    * @return array objReturnValue
    */
    public function makeCall(string $numberFrom, string $numberTo, string $callerId, 
        array $settings=null, string $callbackUrl=null, $callType=null
    )
    {
        $objReturnValue = null;

        try {
            //Set settings if it does not exists
            if (empty($settings)) {
                $settings = $this->settings;

                if (! isset($settings)) {
                    throw new ConfigNotDefinedException('Exotel call config not defined');
                } //End if
            } //End if

            //Build payload array
            $payload = [];
            $payload['From']            = $numberFrom;
            $payload['To']              = $numberTo;
            $payload['CallerId']        = $callerId;
            $payload['StatusCallback']  = $callbackUrl;
            $payload['CallType']        = empty($callType)?CallType::TRANSACTIONAL:$callType;
            $payload['TimeOut']         = config('exotel.configuration.call.time_out');                
            $payload['MaxRetries']      = config('exotel.configuration.call.max_retries');
            //$payload['Record']          = config('exotel.configuration.call.allow_call_recording');
    
            log::debug('Exotel Call Content -> '. json_encode($payload, JSON_PRETTY_PRINT));

            //Build Exotel Call URL
            $urlExotelCall = $this->getExotelURL('exotel.configuration.call.endpoint', $settings);

            $response = $this->makeExotelCall($urlExotelCall, $payload);

            $objReturnValue = $response;
        } catch(ExotelException $e) {
            log::error('ExotelCall:makeCall:ExotelException:' . $e->getMessage());
            throw new ExotelException();
        } catch(Exception $e) {
            log::error('ExotelCall:makeCall:Exception:' . $e->getMessage());
            throw new Exception();
        } //Try-Catch ends

        return $objReturnValue;
    } //Function ends

} //Class ends