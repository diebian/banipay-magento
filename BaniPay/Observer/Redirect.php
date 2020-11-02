<?php

namespace BaniPayPaymentGateway3\BaniPay\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\ManagerInterface;
use \Psr\Log\LoggerInterface;

use BaniPayPaymentGateway3\BaniPay\Model\BaniPay;

use Magento\Framework\Encryption\EncryptorInterface as Encryptor;
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

    public $url = 'https://diebian.dev';
    protected $_httpClientFactory;

    const REDIRECT_URL = 'https://diebian.dev';
    protected $redirect;

    public function __construct(\Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\App\ResponseFactory $responseFactory,
        \Magento\Framework\UrlInterface $url,
        // \Magento\Payment\Model\Method\Logger $logger
        \Psr\Log\LoggerInterface $logger,

        BaniPay $banipay,
        Encryptor $encryptor,

        \Magento\Framework\HTTP\ZendClientFactory $httpClientFactory,
        \Magento\Framework\App\Response\RedirectInterface $redirect
        
    ) {
        $this->messageManager = $messageManager;
        $this->_responseFactory = $responseFactory;
        $this->_url = $url;
        $this->_logger = $logger;

        $this->banipay = $banipay;

        $this->encryptor = $encryptor;

        $this->_httpClientFactory   = $httpClientFactory;
        $this->redirect = $redirect;

        $this->affiliate_code_demo = '141581ae-fb1f-4cfb-b21e-040a8851c265';

        $this->title = $this->banipay->getDataconfig('title');
        $this->affiliate_code = $this->banipay->getDataconfig('affiliate_code');
        $this->expire_minutes = $this->banipay->getDataconfig('expire_minutes');
        $this->failed_url = $this->banipay->getDataconfig('failed_url');
        $this->success_url = $this->banipay->getDataconfig('success_url');

    }

    public function execute(EventObserver $observer) {

        // $observer->getEvent()->getFront()->getResponse()->setRedirect('https://diebian.dev');
        // header('Location: https://diebian.dev', true, 301); exit();
        // exit();
        
        // return $this->resultRedirectFactory->create()->setUrl('https://diebian.dev');


        // All items cart
        $debug = $observer->getEvent()->debug();        
        $order = $observer->getEvent()->getOrder();
        
        $encrypt = md5($this->encryptor->encrypt(json_encode($debug, true)));

        $increment_id = $order->getincrement_id();
        $items = $order->getAllVisibleItems();
        $address = $order->getShippingAddress()->getData();
        
        $payment = $order->getPayment();
        $method = $payment->getMethod();   

        if ($method != 'banipay') {
            return;
        }
        
        $this->_logger->debug('ADDRESSES_: '.print_r($address, true));
        // $this->_logger->debug('DEBUG: '.print_r($debug, true));
        // $this->_logger->debug('METHOD: '.print_r($method, true));

        // Products loading
        foreach($items as $item){
            $data = array(
                "concept"         => $item->getName(),
                // "productImageUrl" => "ID: ".$item->getStoreId(),
                "productImageUrl" => "https://i.blogs.es/322095/google-home-wifi/1366_2000.jpg",
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
            "administrativeArea" => $address['region_id'], 
            "country"            => $address['country_id'], 
            "firstName"          => $address['firstname'], 
            "identifierCode"     => $increment_id, 
            "identifierName"     => $address['lastname'], 
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



        if( (isset($_COOKIE["externalCode"])) && ($_COOKIE["externalCode"] == $encrypt) ) {
            
            // cart empry, redirect to $_COOKIE["url_transaction"]
            return;

        } else {

            // Cookie cart hash
            $this->banipay->createCookie('externalCode', $encrypt);
            
            // Start class Transaction
            $affiliate = $this->banipay->getAffiliate( $this->affiliate_code );

            if ( ($this->affiliate_code_demo != $this->affiliate_code ) && isset( $affiliate->withInvoice ) ) {
                $data["withInvoice"] = $affiliate->withInvoice;
            }

            // Registration a transaction
            $this->_logger->debug('Data: '.print_r($data, true));
            $this->_logger->debug('Params: '.print_r($params, true));
            $transaction = $this->banipay->register($data, $params);
            $this->_logger->debug('Transaction___: '.($transaction));


            $client = $this->_httpClientFactory->create();
            $client->setUri('https://banipay.me:8443/api/payments/transaction');
            // $client->getUri()->setPort($port);
            $client->setConfig(['timeout' => 300]);
            $client->setHeaders(['Content-Type: application/json']);
            $client->setMethod(\Zend_Http_Client::POST);
            $client->setRawData($transaction);

            try {
                $responseBody = $client->request()->getBody();

                $this->_logger->debug('POST : '.print_r($responseBody, true));
            } catch (\Exception $e) {
                $this->_logger->debug('ERROR : '.print_r($e->getMessage(), true));
            }


            return ; die();

            if( isset($transaction) && !isset($transaction->status) ){

                $this->banipay->createCookie('externalCode', $transaction->externalCode);
                $this->banipay->createCookie('transaction_generated', $transaction->transactionGenerated);
                $this->banipay->createCookie('url_transaction', $transaction->urlTransaction);
                $this->banipay->createCookie('payment_id', $transaction->paymentId);
                
                // cart empty

                // redirect to banipay url transaction
                /* return array(
                    'result' => 'success',
                    'redirect' => $transaction->urlTransaction
                ); */

            } else {
                // wc_add_notice(  'El Código de Afiliado de BaniPay no es correcto.', 'error' );
                return;
            }
        }



        return;



        $this->_logger->debug('AFFILIATE: '.print_r($affiliate , true));        
        // $this->_logger->debug('DETAILS: '.print_r($this->details , true));
        $this->_logger->debug('DATA: '.print_r($data , true));

        return; // stop
        exit; // next

    }
    
}