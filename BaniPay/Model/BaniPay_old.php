<?php

namespace BaniPayPaymentGateway3\BaniPay\Model;



/**
 * Pay In Store payment method model
 */
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


  

}
