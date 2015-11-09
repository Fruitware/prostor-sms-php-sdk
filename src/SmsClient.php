<?php

namespace Fruitware\ProstorSms;

use Fruitware\ProstorSms\Exception\BadResponseStatusException;
use Fruitware\ProstorSms\Model\Balance;
use Fruitware\ProstorSms\Model\Sms;
use Fruitware\ProstorSms\Model\SmsInterface;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Command\Guzzle\DescriptionInterface;
use GuzzleHttp\Command\Guzzle\GuzzleClient;
use GuzzleHttp\Stream\Stream;

class Client extends GuzzleClient
{
	/**
	 * @param ClientInterface      $client
	 * @param DescriptionInterface $description
	 * @param array                $config
	 */
	public function __construct(ClientInterface $client, DescriptionInterface $description = null, array $config = [])
	{
		$description = $description instanceof DescriptionInterface ? $description : new Description();
		parent::__construct($client, $description, $config);

		$this->getHttpClient()->setDefaultOption('headers', [
			'Content-Type' => 'application/json; charset=utf-8'
		]);
	}

	/**
	 * Проверка активной версии API
	 *
	 * @return int
	 */
	public function version()
	{
		$response = parent::version();

		return $response['version'];
	}

	/**
	 * Проверка состояния счета
	 *
	 * @return float
	 */
	public function balance()
	{
		$response = parent::balance();

		$aBalance = $response['balance'][0];
		$balance = new Balance();
		$balance
			->setCredit($aBalance['credit'])
			->setAmount($aBalance['balance'])
			->setCurrency($aBalance['type'])
		;

		return $balance;
	}

	/**
	 * Передача сообщения
	 *
	 * @param SmsInterface|SmsInterface[] $smsCollection   Массив sms объектов
	 * @param string                      $statusQueueName Название очереди статусов отправленных сообщений
	 * @param \DateTime                   $scheduledAt    Дата для отложенной отправки сообщения
	 *
	 * @return SmsInterface[] with defined status and code
	 */
	public function send($smsCollection, $statusQueueName = 'default', \DateTime $scheduledAt = null)
	{
		if ($smsCollection instanceof SmsInterface) {
			$smsCollection = [$smsCollection];
		}

		$messages = [];
		foreach ($smsCollection as $sms) {
			$sms
				->setQueueName($statusQueueName)
				->setScheduledAt($scheduledAt)
			;

			$messages[] = [
				'clientId'        => $sms->getId(),
				'phone'           => (string)$sms->getPhone(),
				'text'            => $sms->getText(),
				'sender'          => (string)$sms->getSender(),
			];
		}

		$args = [
			'statusQueueName' => $statusQueueName,
			'scheduleTime'    => $scheduledAt ? $scheduledAt->format('Y-m-d\TH:i:s\Z') : '', //2015-11-09T14:30:01Z
			'messages'        => $messages,
		];

		$response = parent::send($args);

		foreach ($response['messages'] as $smsStatuses) {
			if (empty($smsStatuses['clientId'])) {
				// todo log this
				continue;
			}

			foreach ($smsCollection as $sms) {
				if ($sms->getId() != $smsStatuses['clientId']) {
					continue;
				}

				$sms
					->setStatus(str_ireplace(' ', '_', $smsStatuses['status']))
					->setInternalId((int)@$smsStatuses['smscId'])
				;
			}
		}

		return $smsCollection;
	}

	/**
	 * @param string $name
	 * @param array $arguments
	 *
	 * @return array
	 * @throws BadResponseStatusException
	 * @throws \Exception
	 */
	public function __call($name, array $arguments)
	{
		$response = parent::__call($name, $arguments);

		/** @var Stream $stream */
		$stream = $response['additionalProperties'];
		$data = json_decode($stream->getContents(), true);

		if ($data['status'] !== 'ok') {
			throw new BadResponseStatusException($data['status'], $data['description']);
		}
		else {
			return $data;
		}
	}
}
