# prostor-sms-php-sdk

Имплементация rest api для prostor-sms.ru

Ссылки на внешнюю документацию: 
http://prostor-sms.ru/smsapi/api_json.pdf
http://iqsms.ru/api/api_rest/

## Установка

```bash
composer require fruitware/prostor-sms-php-sdk
```

## Инициализация

```php
namespace MyProject;

require_once(__DIR__ . '/vendor/autoload.php');

use Fruitware\ProstorSms\Client;
use Fruitware\ProstorSms\Description;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Subscriber\Log\Formatter;
use GuzzleHttp\Subscriber\Log\LogSubscriber;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

//set basic access authentication
$options = [
	'base_url' => 'http://api.prostor-sms.ru/messages/v2/'
	'defaults' => [
		'auth'    => ['username', 'password'],
	],
];

// Инициализация клиена
$guzzleClient = new GuzzleClient($options);
$smsGate = new Client($guzzleClient);

// Если вы хотите, то можете подключить логирование запросов (monolog/monolog required)
$log = new Logger('maib_guzzle_request');
$log->pushHandler(new StreamHandler(__DIR__.'/logs/prostor_sms_guzzle_request.log', Logger::DEBUG));
$subscriber = new LogSubscriber($log, Formatter::SHORT);
$smsGate->getHttpClient()->getEmitter()->attach($subscriber);
```

## Примеры использования

### Проверить версии

```php
$version = $smsGate->balance();
var_dump('version', $version);
```

### Проверить баланса

```php
$balance = $smsGate->balance();
var_dump('balance', $balance);
```

### Отослать sms

#### Простой вариант

```php
$sms = new Sms();
$sms
	->setId(unique()) // id sms в вашей системе
    ->setPhone('+71234567890')
    ->setText('тест sms')
;

var_dump('sms', $smsGate->send($sms));
```

#### Максимальный вариант

```php
$sms = new Sms();
$sms
	->setId(unique()) // id sms в вашей системе
    ->setPhone('+71234567890')
    ->setText('тест sms')
    ->sender('TEST') // Подпись отправителя (например TEST)
;

/** 
* Название очереди статусов отправленных сообщений, в случае, если вы хотите использовать очередь статусов отправленных сообщений. 
* От 3 до 16 символов, буквы и цифры (например myQueue1)
*/
$queueName = 'myQueue1';
// Дата для отложенной отправки сообщения
$scheduleTime = (new \DateTime())->modify('+7 days');

var_dump('sms', $smsGate->send([$sms, $sms], $queueName, $scheduleTime));
```