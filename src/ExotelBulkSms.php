<?php

namespace Ellaisys\Exotel;

use Config;
use Illuminate\Support\Facades\Log;

use Ellaisys\Exotel\Traits\ExotelAction;
use Ellaisys\Exotel\Traits\ExotelSmsAction;
use Ellaisys\Exotel\Enums\Sms\Priority;
use Ellaisys\Exotel\Enums\Sms\EncodingType;

use Exception;
use Ellaisys\Exotel\Exceptions\ExotelException;
use Ellaisys\Exotel\Exceptions\ConfigNotDefinedException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class ExotelBulkSms {
    use ExotelAction;
    use ExotelSmsAction;

    public static function send(string $numberFrom,  string $numberTo, string $message, 
        array $settings, string $callbackUrl, $encodingType=null, $priority=null)
    {
        return (new static())->sendSMS(
            $numberFrom,  $numberTo, $message, 
            $settings, $callbackUrl, $encodingType, $priority
        );
    } //Function ends

    /**
     * Send an Exotel Bulk SMS (Static)
     * https://developer.exotel.com/api/#send-bulk-static-sms
     * 
     * @param string $numberFrom
     * @param array $numberTo
     * @param string $message
     * @param array $settings
     * @param string $callbackUrl
     *
     * @return array objReturnValue
     * 
     * @throws ConfigNotDefinedException
     * @throws ExotelException
     * @throws HttpException
     */
    public function sendStaticBulk(string $numberFrom, array $numberTo, string $message, 
        array $settings, string $callbackUrl, $encodingType=null, $priority=null)
    {
        $objReturnValue = null;
        try {

            //Set settings if it does not exists
            if (empty($settings)) {
                $settings = [];
                $settings['exotel_sid']         = config('exotel.configuration.sms.exotel_sid');
                $settings['exotel_api_key']     = config('exotel.configuration.sms.exotel_api_key');
                $settings['exotel_api_token']   = config('exotel.configuration.sms.exotel_api_token');

                if (! isset($settings)) {
                    throw new ConfigNotDefinedException('Exotel Sms Config not defined');
                } //End if
            } //End if

            //Check params and send SMS
            if(!empty($callbackUrl)) {

                //Build payload array
                $payload = [];
                $payload['From']  = $numberFrom;
                $payload['To']    = $numberTo;
                $payload['Body']  = $message;

                $payload['EncodingType'] = empty($encodingType)?EncodingType::PLAIN:$encodingType;
                $payload['Priority']     = empty($priority)?Priority::NORMAL:$priority;
                $payload['StatusCallback'] = $callbackUrl;
                log::debug('Message Content -> '. json_encode($payload, JSON_PRETTY_PRINT));

                //Build Exotel Bulk SMS URL
                $urlExotelSMS = $this->getExotelURL('exotel.configuration.bulk-sms.endpoint', $settings);

                //Send Request With Data
                $response = $this->sendMessage($urlExotelSMS, $payload);

                $objReturnValue = $response;
            } else {
                throw new ConfigNotDefinedException();
            } //End id

        } catch(ConfigNotDefinedException $e) {
            Log::error(json_encode($e));
            throw new ConfigNotDefinedException();
        } catch(ExotelException $e) {
            Log::error(json_encode($e));
            throw new ExotelException();
        } catch(Exception $e) {
            Log::error(json_encode($e));
            throw new HttpException(500);
        } //Try-Catch ends

        return $objReturnValue;
    } //Function ends


    /**
     * Send an Exotel Bulk SMS (Dynamic)
     * https://developer.exotel.com/api/#send-bulk-dynamic-sms
     * 
     * @param string $numberFrom
     * @param string $numberTo
     * @param string $message
     * @param array $settings
     * @param string $callbackUrl
     *
     * @return array objReturnValue
     * 
     * @throws ConfigNotDefinedException
     * @throws ExotelException
     * @throws HttpException
     */
    public function sendDynamicBulk(string $numberTo, string $message, 
        array $settings, string $callbackUrl, $encodingType=null, $priority=null)
    {
        $objReturnValue = null;
        try {

            //Set settings if it does not exists
            if (empty($settings)) {
                $settings = [];
                $settings['exotel_sid']         = config('exotel.configuration.sms.exotel_sid');
                $settings['exotel_api_key']     = config('exotel.configuration.sms.exotel_api_key');
                $settings['exotel_api_token']   = config('exotel.configuration.sms.exotel_api_token');

                if (! isset($settings)) {
                    throw new ConfigNotDefinedException('Exotel Sms Config not defined');
                } //End if
            } //End if

            //Check params and send SMS
            if(!empty($callbackUrl)) {

                //Build payload array
                $payload = [];
                $payload['From']  = $settings['exotel_sid'];
                $payload['Body']  = $message;

                $payload['EncodingType'] = empty($encodingType)?EncodingType::PLAIN:$encodingType;
                $payload['Priority']     = empty($priority)?Priority::NORMAL:$priority;
                $payload['StatusCallback'] = $callbackUrl;
                log::debug('Message Content -> '. json_encode($payload, JSON_PRETTY_PRINT));

                //Build Exotel Bulk SMS URL
                $urlExotelSMS = $this->getExotelURL('exotel.configuration.bulk-sms.endpoint', $settings);

                //Send Request With Data
                $response = $this->sendMessage($urlExotelSMS, $payload);

                $objReturnValue = $response;
            } else {
                throw new ConfigNotDefinedException();
            } //End id

        } catch(ConfigNotDefinedException $e) {
            Log::error(json_encode($e));
            throw new ConfigNotDefinedException();
        } catch(ExotelException $e) {
            Log::error(json_encode($e));
            throw new ExotelException();
        } catch(Exception $e) {
            Log::error(json_encode($e));
            throw new HttpException(500);
        } //Try-Catch ends

        return $objReturnValue;
    } //Function ends

} //Class ends