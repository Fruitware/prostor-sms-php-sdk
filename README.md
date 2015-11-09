Имплементация api для iqsms.ru
=======================

## Ссылки на внешнюю документацию: 
- [iqsms.ru](http://iqsms.ru/api/api_rest/)
- [prostor-sms.ru](http://prostor-sms.ru/smsapi/api_json.pdf)

## Установка

```bash
composer require fruitware/prostor-sms-php-sdk
```

## Инициализация

```php
use Fruitware\ProstorSms\Client;
use GuzzleHttp\Client as GuzzleClient;

//set basic access authentication
$options = [
	'defaults' => [
		'auth'    => ['username', 'password'],
	],
];

$smsGate = new Client(new GuzzleClient($options));
```

## Включение логов (необязательно)

### Требуются зависимости
```bash
composer require guzzlehttp/log-subscriber monolog/monolog
```    

```php
use GuzzleHttp\Subscriber\Log\Formatter;
use GuzzleHttp\Subscriber\Log\LogSubscriber;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

$log = new Logger('maib_guzzle_request');
$log->pushHandler(new StreamHandler(__DIR__.'/logs/prostor_sms_guzzle_request.log', Logger::DEBUG));
$subscriber = new LogSubscriber($log, Formatter::SHORT);
$smsGate->getHttpClient()->getEmitter()->attach($subscriber);
```

## Примеры использования

### Проверить баланс

```php
$balance = $smsGate->balance();
var_dump('balance', $balance);
```

## Отослать sms

### Простой вариант

```php
use Fruitware\ProstorSms\Model\Sms;
use Fruitware\ProstorSms\Exception\BadSmsStatusException;

$sms = new Sms();
$sms
	->setId(unique()) // id sms в вашей системе
    ->setPhone('+71234567890')
    ->setText('тест sms')
;

try {
    $smsGate->send($sms);
}
catch (BadSmsStatusException $ex) {
    // что-то сделать с ошибкой
}

var_dump('sms', $sms);
```

### Отсылка нескольких

```php
$sms = new Sms();
$sms
	->setId(unique()) // id sms в вашей системе
    ->setPhone('+71234567890')
    ->setText('тест sms')
    ->sender('TEST') // Подпись отправителя (например TEST)
;

// Название очереди статусов отправленных сообщений
$queueName = 'myQueue1';
// Дата для отложенной отправки сообщения
$scheduleTime = (new \DateTime())->modify('+1 day');

$smsCollection = $smsGate->sendQueue([$sms, $sms], $queueName, $scheduleTime);

foreach ($smsCollection as $sms) {
    if ($sms->getStatus() !== $sms::STATUS_ACCEPTED) {
        // что-то сделать с ошибкой
    }
}
```