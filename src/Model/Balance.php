<?php

namespace Fruitware\ProstorSms\Model;

class Balance
{
    /**
     * @var float
     */
    protected $credit = 0.0;

    /**
     * @var float
     */
    protected $amount = 0.0;

    /**
     * @var string
     */
    protected $currency;

    /**
     * @param float $credit
     *
     * @return $this
     */
    public function setCredit($credit)
    {
        $this->credit = $credit;

        return $this;
    }

    /**
     * @return float
     */
    public function getCredit()
    {
        return $this->credit;
    }

    /**
     * @param float $amount
     *
     * @return $this
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param float $currency
     *
     * @return $this
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * @return float
     */
    public function getCurrency()
    {
        return $this->currency;
    }
}