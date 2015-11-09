<?php

namespace Fruitware\ProstorSms\Exception;

class BadSmsStatusException extends BadResponseStatusException
{
    /**
     * @var string
     */
    protected $status;

    /**
     * @param string     $status
     * @param string     $message
     * @param int        $code
     * @param \Exception $previous
     */
    public function __construct($status, $message = null, $code = 0, \Exception $previous = null)
    {
        parent::__construct(sprintf('Error sms send status: %s', $status), $code, $previous);

        $this->status = $status;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }
}