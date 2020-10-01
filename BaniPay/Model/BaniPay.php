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


    protected $_logger;


    public function __construct(
        \Vendor\Extension\Logger\Logger $logger
    ) {
        $this->_logger = $logger;
        $this->_logger->info('diebiandev');
    }
    
    public function execute()
    {
        $this->_logger->info('diebiandev2');
    }
  

}
