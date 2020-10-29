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

    public function execute(EventObserver $observer) {
    // public function execute (\Magento\Framework\Event\Observer $observer) {
        

     
        $this->_logger->info('from OBSERVER root: info ORDER');
        // $this->_logger->debug(var_dump($order , true));
        $this->_logger->debug('from OBSERVER root: debug ORDER');
        // $this->_logger->debug(print_r($order->getState(), true));
        $this->_logger->debug('from OBSERVER root: diebiandev diebiandev');
        
        $this->_logger->debug(print_r($observer->getEvent()->debug(), true));

        $result          = $observer->getEvent()->getResult();
        $method_instance = $observer->getEvent()->getMethodInstance();
        // $this->_logger->debug(print_r($observer->getEvent(), true));

        // $event = $observer->getEvent();
        // $this->redirect->redirect($event->getResponse(), 'https://diebian.dev');

        // var_dump($order);
        // return true;


        exit;
    }
    
}