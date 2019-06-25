<?php

namespace Fruitware\ProstorSms;

use GuzzleHttp\Command\Guzzle\Description as GuzzleDescription;

class Description extends GuzzleDescription
{
	/**
	 * @param array $options Custom options to apply to the description
	 *     - formatter: Can provide a custom SchemaFormatter class
	 */
	public function __construct(array $options = [])
	{
		parent::__construct([
			'name'       => 'prostor-sms.ru API',
            'baseUrl' => 'http://api.prostor-sms.ru',
//            'baseUrl' => 'http://gate.iqsms.ru',
			'operations' => [
				'version'   => [
					'httpMethod'    => 'GET',
					'uri'           => '/messages/v2/version.json/',
					'description'   => 'Проверка активной версии API. При успешной авторизации, в ответ сервис должен вернуть plain/text ответ вида: "2", где выводится номер активной версии API',
					'responseModel' => 'getResponse',
				],
				'balance'   => [
					'httpMethod'    => 'GET',
					'uri'           => '/messages/v2/balance.json/',
					'description'   => 'Проверка состояния счета. При успешной авторизации, в ответ сервис должен вернуть plain/text ответ вида: "RUB;540.15;0.0\nSMS;589;100", где в каждой строке 1 значение – тип баланса, 2 значение – баланс, 3 значение – кредит (возможность использовать сервис при отрицательномбалансе)',
					'responseModel' => 'getResponse',
				],
				'send'   => [
					'httpMethod'    => 'GET',
					'uri'           => '/messages/v2/send.json/',
					'description'   => 'Передача сообщения. При успешной авторизации, в ответ сервис должен вернуть plain/text ответ вида accepted;A132571BC',
					'responseModel' => 'getResponse',
					'parameters'    => [
						'statusQueueName'  => [
							'type'     => 'string',
							'location' => 'json',
							'required' => false,
							'min'      => 3,
							'max'      => 16,
							'description' => 'Название очереди статусов отправленных сообщений, в случае, если вы хотите использовать очередь статусов отправленных сообщений. От 3 до 16 символов, буквы и цифры (например myQueue1)',
						],
						'scheduleTime'  => [
							'type'     => 'string',
							'location' => 'json',
							'required' => false,
							'description' => 'Дата для отложенной отправки сообщения, в UTC (2008-07-12T14:30:01Z)',
						],
						'messages'      => [
							'type' => 'array',
							'location' => 'json',
							'required' => true,
							'items' => [
								'clientId'        => [
									'type'     => 'string',
									'location' => 'json',
									'required' => true,
									'description' => 'ID в вашей системе',
								],
								'phone'        => [
									'type'     => 'string',
									'location' => 'json',
									'required' => true,
									'description' => 'Номер телефона, в формате +71234567890',
								],
								'text'      => [
									'type'     => 'string',
									'location' => 'json',
									'required' => true,
									'description' => 'Текст сообщения, в UTF-8 кодировке',
								],
								'sender'    => [
									'type'     => 'string',
									'location' => 'json',
									'required' => false,
									'description' => 'Подпись отправителя (например TEST)',
								],
							],
						],
					],
				],
			],
			'models'     => [
				'getResponse' => [
					'type'       => 'object',
					'properties' => [
						'additionalProperties' => [
							'location' => 'body'
						]
					]
				]
			]
		], $options);
	}
}
