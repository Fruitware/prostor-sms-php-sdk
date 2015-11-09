<?php

namespace Example;

require_once(__DIR__.'/vendor/autoload.php');
require_once __DIR__.'/src/SmsClient.php'; // only here
require_once __DIR__.'/src/Description.php'; // only here
require_once __DIR__.'/src/Exception/BadResponseException.php'; // only here

use Fruitware\ProstorSms\Client;
use Fruitware\ProstorSms\Model\Sms;
use GuzzleHttp\Client as GuzzleClient;

//set basic access authentication
$options = [
    'defaults' => [
        'auth'    => ['user', 'password'],
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
    ->setPhone('+71234567890')
    ->setText('тест sms')
    ->setSender('Test sender')
;

var_dump('send sms', $smsGate->send($sms));
var_dump('balance', $smsGate->balance());
