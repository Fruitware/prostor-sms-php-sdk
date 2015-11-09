<?php


namespace Fruitware\ProstorSms\Model;


interface SmsInterface
{
    /**
     * Дефотный статус
     */
    const STATUS_NONE = 'none'; // Статус не указан

    /**
     * Статусы добавления в очередь
     */
    // Сообщение принято сервисом
    const STATUS_ACCEPTED                     = 'accepted';
    // Неверно задан номер тефона (формат 71234567890)
    const STATUS_INVALID_MOBILE_PHONE         = 'invalid_mobile_phone';
    // Отсутствует текст
    const STATUS_TEXT_IS_EMPTY                = 'text_is_empty';
    // Неверная (незарегистрированная) подпись отправителя
    const STATUS_SENDER_ADDRESS_INVALID       = 'sender_address_invalid';
    // Неправильный формат wap-push ссылки
    const STATUS_WAPURL_INVALID               = 'wapurl_invalid';
    // Неверный формат даты отложенной отправки сообщения
    const STATUS_INVALID_SCHEDULE_TIME_FORMAT = 'invalid_schedule_time_format';
    // Неверное название очереди статусов сообщений
    const STATUS_INVALID_STATUS_QUEUE_NAME    = 'invalid_status_queue_name';
    // Баланс пуст (проверьте баланс)
    const STATUS_NOT_ENOUGH_CREDITS           = 'not_enough_credits';

    /**
     * Статусы проверки состояния отправленного сообщения
     */
    // Сообщение находится в очереди
    const STATUS_QUEUED         = 'queued';
    // Сообщение доставлено
    const STATUS_DELIVERED      = 'delivered';
    // Ошибка доставки SMS (абонент в течение времени доставки находился вне зоны действия сети или номер абонента заблокирован)
    const STATUS_DELIVERY_ERROR = 'delivery_error';
    // Сообщение доставлено в SMSC
    const STATUS_SUBMITTED      = 'smsc_submit';
    // Сообщение отвергнуто SMSC (номер заблокирован или не существует)
    const STATUS_REJECTED       = 'smsc_reject';
    // Неверный идентификатор сообщения
    const STATUS_INCORRECT_ID   = 'incorrect_id';

    /**
     * @param int $id
     *
     * @return $this
     */
    public function setId($id);

    /**
     * @return int
     */
    public function getId();

    /**
     * @return array
     */
    static public function getStatuses();

    /**
     * @param string $phone
     *
     * @return $this
     */
    public function setPhone($phone);

    /**
     * @return string
     */
    public function getPhone();

    /**
     * @param string $text
     *
     * @return $this
     */
    public function setText($text);

    /**
     * @return string
     */
    public function getText();

    /**
     * @param string|null $sender
     *
     * @return $this
     */
    public function setSender($sender = null);

    /**
     * @return string|null
     */
    public function getSender();

    /**
     * @param \DateTime|null $scheduledAt
     *
     * @return $this
     */
    public function setScheduledAt(\DateTime $scheduledAt = null);

    /**
     * @return \DateTime
     */
    public function getScheduledAt();

    /**
     * @param string|null $queueName
     *
     * @return $this
     */
    public function setQueueName($queueName = null);

    /**
     * @return string|null
     */
    public function getQueueName();

    /**
     * @param int|null $internalId
     *
     * @return $this
     */
    public function setInternalId($internalId = null);

    /**
     * @return int|null
     */
    public function getInternalId();

    /**
     * @param string $status
     *
     * @return $this
     */
    public function setStatus($status);

    /**
     * @return string
     */
    public function getStatus();
}