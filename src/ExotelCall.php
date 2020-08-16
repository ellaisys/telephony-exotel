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

    public static function dial(string $numberFrom,  string $numberTo, string $callerId,
        array $settings, string $callbackUrl=null, $callType=null)
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
        array $settings, string $callbackUrl=null, $callType=null
    )
    {
        $objReturnValue = null;

        try {
            $configPath = base_path('packages/ellaisys/exotel/config/config.php');
        log::info($configPath);

            log::info(config('ellaisys-exotel.configuration.call'));

            //Set settings if it does not exists
            if (empty($settings)) {
                $settings = [];
                $settings['exotel_sid']         = config('ellaisys-exotel.configuration.sms.exotel_sid');
                $settings['exotel_api_key']     = config('ellaisys-exotel.configuration.sms.exotel_api_key');
                $settings['exotel_api_token']   = config('ellaisys-exotel.configuration.sms.exotel_api_token');

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
            $payload['TimeOut']         = config('ellaisys-exotel.configuration.call.time_out');                
            $payload['MaxRetries']      = config('ellaisys-exotel.configuration.call.max_retries');
            $payload['Record']          = config('ellaisys-exotel.configuration.call.allow_call_recording');
    
            log::debug('Exotel Call Content -> '. json_encode($payload, JSON_PRETTY_PRINT));

            //Build Exotel Call URL
            $urlExotelCall = $this->getExotelURL('ellaisys-exotel.configuration.call.endpoint', $settings);

            $response = $this->makeExotelCall($urlExotelCall, $payload);

            $objReturnValue = $response;
        } catch(Exception $e) {
            Log::error(json_encode($e));
            throw new NotFoundHttpException($e->getMessage());
        } //Try-Catch ends

        return $objReturnValue;
    } //Function ends

} //Class ends