<?php

namespace BaniPayPaymentGateway3\BaniPay\Model;

class BaniPay extends \Magento\Payment\Model\Method\AbstractMethod
{

    /**
     * Payment code
     *
     * @var string
     */
    protected $_code = 'banipay';

    /**
     * Availability option
     *
     * @var bool
     */
    protected $_isOffline = true;

    protected $_logger;

}