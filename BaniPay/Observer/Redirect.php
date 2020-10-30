<?php

namespace BaniPayPaymentGateway3\BaniPay\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\ManagerInterface;
use \Psr\Log\LoggerInterface;

use BaniPayPaymentGateway3\BaniPay\Model\BaniPay;

use Magento\Framework\Controller\ResultFactory;

use Magento\Framework\Encryption\EncryptorInterface as Encryptor;

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
        Encryptor $encryptor,

        \Magento\Framework\Controller\ResultFactory $result
        
    ) {
        $this->messageManager = $messageManager;
        $this->_responseFactory = $responseFactory;
        $this->_url = $url;
        $this->_logger = $logger;

        $this->banipay = $banipay;

        $this->resultRedirect = $result;
        $this->encryptor = $encryptor;

        $this->affiliate_code_demo = '141581ae-fb1f-4cfb-b21e-040a8851c265';

        $this->title = $this->banipay->getDataconfig('title');
        $this->affiliate_code = $this->banipay->getDataconfig('affiliate_code');
        $this->expire_minutes = $this->banipay->getDataconfig('expire_minutes');
        $this->failed_url = $this->banipay->getDataconfig('failed_url');
        $this->success_url = $this->banipay->getDataconfig('success_url');

    }

    public function execute(EventObserver $observer) {
        
        // All items cart
        $debug = $observer->getEvent()->debug();        
        $order = $observer->getEvent()->getOrder();
        
        $encrypt = md5($this->encryptor->encrypt(json_encode($debug, true)));
        $this->banipay->createCookie('externalCode', $encrypt);
        // $this->_logger->debug($encrypt);

        $increment_id = $order->getincrement_id();
        $items = $order->getAllVisibleItems();
        $address = $order->getShippingAddress()->getData();
        
        $payment = $order->getPayment();
        $method = $payment->getMethod();   

        if ($method != 'banipay') {
            return;
        }
        
        // $this->_logger->debug('ADDRESSES_: '.print_r($address, true));
        // $this->_logger->debug('DEBUG: '.print_r($debug, true));
        // $this->_logger->debug('METHOD: '.print_r($method, true));

        // Products loading
        foreach($items as $item){
            $data = array(
                "concept"         => $item->getName(),
                "productImageUrl" => "ID: ".$item->getStoreId(),
                "productImageUrl_" => 12,
                "quantity"        => $item->getQtyOrdered(),
                "unitPrice"       => $item->getPrice(),
            );
            array_push($this->details, $data);   
        }

        // Products services to register
        $data = array(
            "withInvoice"        => false,
            "externalCode"       => $encrypt,
            "paymentDescription" => $this->title,
            
            "address"            => $address['street'], 
            "administrativeArea" => $address['region'], 
            "country"            => $address['country_id'], 
            "firstName"          => $address['firstname'], 
            "identifierCode"     => $increment_id, 
            "identifierName"     => $address['email'], 
            "lastName"           => $address['lastname'], 
            "locality"           => $address['city'], 
            "email"              => $address['email'], 
            "nit"                => '', 
            "nameOrSocialReason" => '',  
            "phoneNumber"        => $address['telephone'], 
            "postalCode"         => $address['postcode'], 
            
            "details"            => $this->details
        );

        // Settings needed to record transaction
        $params = array(
            "affiliateCode"   => $this->affiliate_code,
            "expireMinutes"   => $this->expire_minutes,
            "failedUrl"       => $this->failed_url,
            "successUrl"      => $this->success_url,
        );

        $affiliate = $this->banipay->getAffiliate( $this->affiliate_code );

        if ( ($this->affiliate_code_demo != $this->affiliate_code ) && isset( $affiliate->withInvoice ) ) {
            $data["withInvoice"] = $affiliate->withInvoice;
        }

        $this->_logger->debug('AFFILIATE: '.print_r($affiliate , true));        
        // $this->_logger->debug('DETAILS: '.print_r($this->details , true));
        $this->_logger->debug('DATA: '.print_r($data , true));

        // model test get data
        $this->_logger->debug(print_r($this->banipay->getTest(), true));

        exit;
        return;

    }
    
}