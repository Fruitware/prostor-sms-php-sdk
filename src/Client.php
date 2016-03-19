<?php

namespace Fruitware\ProstorSms;

use Fruitware\ProstorSms\Exception\BadResponseStatusException;
use Fruitware\ProstorSms\Exception\BadSmsStatusException;
use Fruitware\ProstorSms\Model\Balance;
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
	 * @param SmsInterface $sms
	 *
	 * @throws BadSmsStatusException
	 * @return SmsInterface with defined status and code
	 */
	public function send(SmsInterface $sms)
	{
		$smsCollection = [$sms];
		$smsCollection = $this->sendQueue($smsCollection, $sms->getScheduledAt());
		$sms = $smsCollection[0];
		unset($smsCollection);

		if ($sms->getStatus() !== $sms::STATUS_ACCEPTED) {
			throw new BadSmsStatusException($sms->getStatus());
		}

		return $sms;
	}

	/**
	 * Передача сообщения
	 *
	 * @param SmsInterface[] $smsCollection   Массив sms объектов
	 * @param \DateTime|null $scheduledAt     Дата для отложенной отправки сообщения
	 * @param string         $statusQueueName Название очереди статусов отправленных сообщений
	 *
	 * @return SmsInterface[] with defined status and code
	 */
	public function sendQueue(array &$smsCollection, \DateTime $scheduledAt = null, $statusQueueName = 'default')
	{
		if (!$scheduledAt instanceof \DateTime) {
			$scheduledAt = new \DateTime();
		}
		$scheduledAt->setTimezone(new \DateTimeZone('UTC'));

		$messages = [];
		$lastSmsIndex = count($smsCollection) - 1;
		foreach ($smsCollection as $index => $sms) {
			$sms
				->setQueueName($statusQueueName)
				->setScheduledAt($scheduledAt)
			;

			$messages[] = [
				'clientId' => $sms->getId(),
				'phone'    => preg_replace('/\D/', '', $sms->getPhone()),
				'text'     => $sms->getText(),
				'sender'   => (string)$sms->getSender(),
			];

			if (($index > 0 && $index % 200 == 199) || $index == $lastSmsIndex) {
				$this->sendBulk($messages, $smsCollection, $scheduledAt, $statusQueueName);
				$messages = [];
			}
		}

		return $smsCollection;
	}

	/**
	 * Передача сообщения по 200 шт
	 *
	 * @param array          $messages
	 * @param SmsInterface[] $smsCollection   Массив sms объектов
	 * @param \DateTime|null $scheduledAt     Дата для отложенной отправки сообщения
	 * @param string         $statusQueueName Название очереди статусов отправленных сообщений
	 *
	 * @return SmsInterface[] with defined status and code
	 */
	private function sendBulk(array $messages, array &$smsCollection, \DateTime $scheduledAt = null, $statusQueueName = 'default')
	{
		if (!$scheduledAt instanceof \DateTime) {
			$scheduledAt = new \DateTime();
		}
		$scheduledAt->setTimezone(new \DateTimeZone('UTC'));

		$args = [
			'statusQueueName' => $statusQueueName,
			'scheduleTime'    => $scheduledAt->format('Y-m-d\TH:i:s\Z'),
			'messages'        => $messages,
		];

		$response = parent::send($args);

		foreach ($response['messages'] as $smsStatuses) {
			foreach ($smsCollection as $sms) {
				if ($sms->getId() != $smsStatuses['clientId']) {
					continue;
				}

				$status = strtolower(str_ireplace(' ', '_', $smsStatuses['status']));
				$sms->setStatus($status);

				if (isset($smsStatuses['smscId'])) {
					$sms->setInternalId($smsStatuses['smscId']);
				}
			}
		}
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
