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

    public function __construct(
        \BaniPayPaymentGateway3\BaniPay\Logger\Logger $logger
    ) {
        // Mage::log('model from contrusct', null, 'system.log', true);
        $this->_logger = $logger;
        // $this->_logger->info('from construct model vulcanbo');
    }

    public function getTest() {

        Mage::log('model from test vulcanbo', null, 'system.log', true);
        return 'from model banipay'; 
    }

}
