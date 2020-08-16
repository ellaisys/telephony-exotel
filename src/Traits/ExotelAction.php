<?php

namespace Ellaisys\Exotel\Traits;

use Config;
use Illuminate\Support\Facades\Log;

use Exception;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

/**
 * Action methods on Exotel Common
 */
trait ExotelAction
{

    /**
     * Build the Exotel Connection URL
     *
     * @return string returnValue
     */
    public function getExotelURL(string $configKey, array $settings) {
        $returnValue = null;
        try {
            //Build Exotel URL
            $strURL = config($configKey);
            $strURL = str_replace('{exotel_api_key}', $settings['exotel_api_key'], $strURL);
            $strURL = str_replace('{exotel_api_token}', $settings['exotel_api_token'], $strURL);
            $strURL = str_replace('{exotel_sid}', $settings['exotel_sid'], $strURL);

            $returnValue = $strURL;
        } catch(Exception $e) {
            Log::error(json_encode($e));
            throw new NotFoundHttpException();
        } //Try-Catch ends

        return $returnValue;
    } //Function ends

} //Trait ens