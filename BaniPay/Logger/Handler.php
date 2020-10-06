<?php
namespace BaniPayPaymentGateway3\BaniPay\Logger;
 
// use Monolog\Logger;
 
class Handler extends \Magento\Framework\Logger\Handler\Base
{
    protected $loggerType = \Monolog\Logger::INFO;
    
    public function __construct(
        \Magento\Framework\Filesystem\DriverInterface $filesystem,
        $filePath = null,
        $fileName = null
        ){
            
            $fileName = '/var/log/banipay.log';
            parent::__contruct($filesystem, filePath, $fileName);
    }
 
}

