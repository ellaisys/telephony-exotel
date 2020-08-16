<?php

namespace Ellaisys\Exotel\Traits;

use Config;
use Illuminate\Support\Facades\Log;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;

use Exception;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

/**
 * Action methods on Exotel Call
 */
trait ExotelCallAction
{

	/**
	 * Make an Exotel Call Between two parties
	 */
	public function makeExotelCall(string $url, $data) {

        try {
        	$client = new \GuzzleHttp\Client();
			$response = $client->post($url, ['http_errors' => false, 'form_params' => $data]);

            if($response->getStatusCode()!=200) {
            	Log::error($response->getBody());
                return json_decode($response->getBody(), TRUE);
            } //End if

            return json_decode($response->getBody(), TRUE);
        } catch (ClientException $e) {
		    Log::error(Psr7\str($e->getRequest()));
            Log::error(Psr7\str($e->getResponse()));
            throw new ExotelException();
        } catch(Exception $e) {
            Log::error(json_encode($e));
            throw new HttpException(500);
        }
	} //Function ends

} //Trait ens