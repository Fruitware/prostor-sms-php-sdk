<?php

namespace Fruitware\ProstorSms\Model;

class Sms implements SmsInterface
{
    /**
     * @var int
     */
    protected $id;

    /**
     * Номер телефона, в формате +71234567890
     *
     * @var string
     */
    protected $phone;

    /**
     * Текст сообщения
     *
     * @var string
     */
    protected $text;

    /**
     * Подпись отправителя (например TEST)
     *
     * @var string|null
     */
    protected $sender;

    /**
     * Дата для отложенной отправки сообщения
     *
     * @var \DateTime|null
     */
    protected $scheduledAt;

    /**
     * Название очереди статусов отправленных сообщений
     *
     * @var string|null
     */
    protected $queueName;

    /**
     * ID в системе prostor-sms.ru
     *
     * @var string
     */
    protected $internalId;

    /**
     * Статус в системе prostor-sms.ru
     *
     * @var string
     */
    protected $status;

    public function __construct()
    {
        $this->setStatus(static::STATUS_NONE);
    }

    /**
     * @return array
     */
    static public function getStatuses()
    {
        return [
            static::STATUS_NONE,
            static::STATUS_DELIVERED,
            static::STATUS_QUEUED,
            static::STATUS_SUBMITTED,
        ];
    }

    /**
     * @param int $id
     *
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $phone
     *
     * @return $this
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param string $text
     *
     * @return $this
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param string|null $sender
     *
     * @return $this
     */
    public function setSender($sender = null)
    {
        $this->sender = $sender;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getSender()
    {
        return $this->sender;
    }

    /**
     * @param \DateTime|null $scheduledAt
     *
     * @return $this
     */
    public function setScheduledAt(\DateTime $scheduledAt = null)
    {
        $this->scheduledAt = $scheduledAt;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getScheduledAt()
    {
        return $this->scheduledAt;
    }

    /**
     * @param string|null $queueName
     *
     * @return $this
     */
    public function setQueueName($queueName = null)
    {
        $this->queueName = $queueName;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getQueueName()
    {
        return $this->queueName;
    }

    /**
     * @param int|null $internalId
     *
     * @return $this
     */
    public function setInternalId($internalId = null)
    {
        $this->internalId = $internalId;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getInternalId()
    {
        return $this->internalId;
    }

    /**
     * @param string $status
     *
     * @return $this
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }
}