<?php

namespace BaniPayPaymentGateway3\BaniPay\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\ManagerInterface;
use \Psr\Log\LoggerInterface;

use BaniPayPaymentGateway3\BaniPay\Model\BaniPay;

use Magento\Framework\Controller\ResultFactory;


class Redirect implements ObserverInterface
{ 

    protected $messageManager;
    protected $_responseFactory;
    protected $_url;
    protected $_logger;

    protected $banipay;

    public $details = array();
    public $transaction = array();

    const REDIRECT_URL = 'https://diebian.dev';
    protected $resultRedirect;   

    public function __construct(\Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\App\ResponseFactory $responseFactory,
        \Magento\Framework\UrlInterface $url,
        // \Magento\Payment\Model\Method\Logger $logger
        \Psr\Log\LoggerInterface $logger,

        BaniPay $banipay,

        \Magento\Framework\Controller\ResultFactory $result
        
    ) {
        $this->messageManager = $messageManager;
        $this->_responseFactory = $responseFactory;
        $this->_url = $url;
        $this->_logger = $logger;

        $this->banipay = $banipay;

        $this->resultRedirect = $result;

    }

    public function execute(EventObserver $observer) {       
     
        $this->_logger->info('from OBSERVER root: info ORDER');
        // $this->_logger->debug(var_dump($order , true));
        $this->_logger->debug('from OBSERVER root: debug ORDER');
        // $this->_logger->debug(print_r($order->getState(), true));
        $this->_logger->debug('from OBSERVER root: diebiandev diebiandev');
        
        $order = $observer->getEvent()->debug();
        // $order = $observer->getEvent()->getOrder();    
        $this->_logger->debug(print_r($order , true));
        
        $order = $observer->getEvent()->getOrder();
        $increment_id = $order->getincrement_id();
        $items = $order->getAllVisibleItems();

        // Products loading
        foreach($items as $item){
            $this->_logger->debug(print_r($item->getName(), true));
            $this->_logger->debug(print_r($item->getPrice(), true));
            $this->_logger->debug(print_r($item->getQtyOrdered(), true));

            $data = array(
                "concept"         => $item->getName(),
                "productImageUrl" => $item->getThumbnail(),
                "quantity"        => $item->getQtyOrdered(),
                "unitPrice"       => $item->getPrice(),
            );
            array_push($this->details, $data);   
        }

        // Products services to register
        $data = array(
            "withInvoice"        => false,
            "externalCode"       => '$_COOKIE["woocommerce_cart_hash"]',
            "paymentDescription" =>  'get_bloginfo()',
            
            "address"            => '$order->get_billing_address_1()', 
            "administrativeArea" => '$order->get_billing_state()', 
            "country"            => '$order->get_billing_country()', 
            "firstName"          => '$order->get_billing_first_name()', 
            "identifierCode"     => '$order_id', 
            "identifierName"     => '$order->get_billing_first_name()', 
            "lastName"           => '$order->get_billing_last_name()', 
            "locality"           => '$order->get_billing_city()', 
            "email"              => '$order->get_billing_email()', 
            "nit"                => '$this->checkBilling(  )', 
            "nameOrSocialReason" => '$this->checkBilling( )',  
            "phoneNumber"        => '$order->get_billing_phone()', 
            "postalCode"         => '$order->get_billing_postcode()',
            
            "details"            => $this->details
        );

        // Settings needed to record transaction
        $params = array(
            "affiliateCode"   => '141581ae-fb1f-4cfb-b21e-040a8851c265',
            "expireMinutes"   => 30,
            "failedUrl"       => 'https://facebook.com',
            "successUrl"      =>  'https://diebian.dev'
        );

        $affiliate = $this->banipay->getAffiliate('141581ae-fb1f-4cfb-b21e-040a8851c265');
        $this->_logger->debug(print_r($affiliate , true));
        
        $this->_logger->debug(print_r($this->details , true));

        $result          = $observer->getEvent()->getResult();
        $method_instance = $observer->getEvent()->getMethodInstance();
        // $this->_logger->debug(print_r($observer->getEvent(), true));


        $this->_logger->debug(print_r($this->banipay->getTest(), true));


        /* $resultRedirect = $this->resultRedirect->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setUrl(self::REDIRECT_URL);
        return $resultRedirect; */

        $observer->getEvent()->getFront()->getResponse()->setRedirect('https://diebian.dev')->sendResponse();
        $observer->getEvent()->getResponse()->setRedirect($url)->sendResponse();
        exit;


        return;

        exit;
    }
    
}