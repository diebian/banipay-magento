<?php

namespace BaniPayPaymentGateway3\BaniPay\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\ManagerInterface;
use \Psr\Log\LoggerInterface;

class Redirect implements ObserverInterface
{ 

    protected $messageManager;
    protected $_responseFactory;
    protected $_url;
    protected $_logger;

    public function __construct(\Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\App\ResponseFactory $responseFactory,
        \Magento\Framework\UrlInterface $url,
        // \Magento\Payment\Model\Method\Logger $logger
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->messageManager = $messageManager;
        $this->_responseFactory = $responseFactory;
        $this->_url = $url;
        $this->_logger = $logger;
    }



    /**
    * @param EventObserver $observer
    * @return $this
    * @SuppressWarnings(PHPMD.CyclomaticComplexity)
    */
    /* public function execute(EventObserver $observer, LoggerInterface $logger) {
        $order = $observer->getEvent()->getOrder();

        $logger->info($order->getData());
        $logger->debug($order->getData());

        var_dump($order->getData());
        exit;
    } */


    public function execute(EventObserver $observer) {
        $order = $observer->getEvent()->getOrder();

        $this->_logger->info('info ORDER');
        $this->_logger->info(print_r($order->getData(), true));
        $this->_logger->debug('debug ORDER');
        $this->_logger->debug(print_r($order->getData(), true));
        $this->_logger->debug('diebiandev diebiandev');

        //var_dump($order->getData());
        exit;
    }
    
}