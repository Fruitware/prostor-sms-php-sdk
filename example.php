<?php

namespace Example;

require 'vendor/autoload.php';

use Fruitware\ProstorSms\Client;
use Fruitware\ProstorSms\Exception\BadSmsStatusException;
use Fruitware\ProstorSms\Model\Sms;
use GuzzleHttp\Client as GuzzleClient;

//set basic access authentication
$options = [
    'defaults' => [
        'auth' => ['user', 'password'],
    ],
];
$smsGate = new Client(new GuzzleClient($options));

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

try {
    $smsGate->send($sms);
}
catch (BadSmsStatusException $ex) {
    var_dump($ex);
}

var_dump('sms', $sms);
