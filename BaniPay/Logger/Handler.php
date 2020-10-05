<?php
namespace BaniPayPaymentGateway3\BaniPay\Logger;
 
use Monolog\Logger;
 
class Handler extends \Magento\Framework\Logger\Handler\Base
{
    protected $loggerType = Logger::INFO;
    protected $fileName = '/var/log/banipay.log';
    // protected $loggerType = Logger::DEBUG;
 
}

