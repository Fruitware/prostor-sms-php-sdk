<?php

namespace Example;

require_once(__DIR__.'/vendor/autoload.php');
require_once __DIR__.'/src/SmsClient.php';
require_once __DIR__.'/src/Description.php';
require_once __DIR__.'/src/Exception/BadResponseException.php';

use Fruitware\ProstorSms\Client;
use Fruitware\ProstorSms\Model\Sms;
use GuzzleHttp\Client as GuzzleClient;

//set basic access authentication
$options = [
    'defaults' => [
        'auth'    => ['t89688817524', '839138'],
    ],
];

// Init GuzzleClient
$guzzleClient = new GuzzleClient($options);
$smsGate = new Client($guzzleClient);

var_dump('version', $smsGate->version());

var_dump('balance', $smsGate->balance());

// простой вариант
$sms = new Sms();
$sms
    ->setId(uniqid())
    ->setPhone('+37368720077')
    ->setText('тест sms')
    ->setSender('Консьерж')
;

var_dump('send sms', $smsGate->send($sms));
var_dump('balance', $smsGate->balance());
