<?php

namespace Fruitware\ProstorSms\Exception;

class BadResponseStatusException extends BaseResponseException
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
    public function __construct($status, $message, $code = 0, \Exception $previous = null)
    {
        parent::__construct(sprintf('Error response status: %s. Description: %s', $status, $message) , $code, $previous);
    }
}