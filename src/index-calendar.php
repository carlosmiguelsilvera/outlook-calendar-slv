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

    static function createEvent($token, $data, $emailOutlook )
    {
        /*$data = [
            'Subject' => 'Discuss the Calendar REST API',
            'Body' => [
                'ContentType' => 'HTML',
                'Content' => 'I think it will meet our requirements!',
            ],
            'Start' => [
                'DateTime' => '2022-11-02T10:00:00',
                'TimeZone' => 'Pacific Standard Time',
            ],
            'End' => [
                'DateTime' => '2022-11-02T11:00:00',
                'TimeZone' => 'Pacific Standard Time',
            ],
        ];*/

        $graph = new Graph();
        $graph->setAccessToken($token);
        $url = "/users/".$emailOutlook."/calendar/events";
        $response = $graph->createRequest("POST", $url)
            ->attachBody($data)
            ->setReturnType(BetaModel\User::class)
            ->execute();
        return $response;
    }

    static function deleteSingleEvent($token,$calendar_id,$emailOutlook){
        $graph = new Graph();
        $graph->setBaseUrl("https://graph.microsoft.com/")
            ->setApiVersion("beta")
            ->setAccessToken($token);

        $delete_calendar = $graph->createRequest("delete", "/users/".$emailOutlook."/events/".$calendar_id)
            ->addHeaders(array("Content-Type" => "application/json"))
            ->setReturnType(BetaModel\User::class)
            ->setTimeout("1000")
            ->execute();
        return $delete_calendar;
    }
}