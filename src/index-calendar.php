<?php

// Code within app\Helpers\Helper.php

namespace Miguelsilvera\OutlookCalendar;

use Beta\Microsoft\Graph\Model as BetaModel;
use GuzzleHttp\Client;
use Microsoft\Graph\Graph;

class CalendarOutlook
{

    static function getToken($tenantId,$clientId,$clientSecret){

        $guzzle = new \GuzzleHttp\Client();
        $url = 'https://login.microsoftonline.com/' . $tenantId . '/oauth2/v2.0/token';
        $token = json_decode($guzzle->post($url, [
            'form_params' => [
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
                'scope' => 'https://graph.microsoft.com/.default',
                'grant_type' => 'client_credentials',
            ],
        ])->getBody()->getContents());

        return json_encode($token->access_token);
    }

    static function getInfoUser($accessToken,$emailOutlook)
    {
        $graph = new Graph();
        $graph->setAccessToken($accessToken);
        $user = $graph->createRequest("GET", "/users/".$emailOutlook)
            ->setReturnType(BetaModel\User::class)
            ->execute();
        return json_encode($user);
    }

    static function getEvent($token, $calendar_id,$emailOutlook){


        $graph = new Graph();
        $graph->setBaseUrl("https://graph.microsoft.com/")
            ->setApiVersion("beta")
            ->setAccessToken($token);

        $event = $graph->createRequest("get", "/users/".$emailOutlook."/events/".$calendar_id)
            ->addHeaders(array("Content-Type" => "application/json"))
            ->setReturnType(BetaModel\User::class)
            ->setTimeout("1000")
            ->execute();

        return $event;

    }
}