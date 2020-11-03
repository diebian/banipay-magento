<?php

namespace BaniPayPaymentGateway3\BaniPay\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\ManagerInterface;
use \Psr\Log\LoggerInterface;

use BaniPayPaymentGateway3\BaniPay\Model\BaniPay;
use BaniPayPaymentGateway3\BaniPay\Cookie\Custom;

use Magento\Framework\Encryption\EncryptorInterface as Encryptor;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Framework\Stdlib\CookieManagerInterface;

class Redirect implements ObserverInterface
{ 

    protected $_logger;
    protected $_logo; 

    protected $banipay;
    protected $custom;

    public $details = array();
    public $transaction = array();
    
    protected $cookieManager;
    protected $cookieMetadataFactory;
    protected $messageManager;

    public function __construct(
        \Magento\Framework\App\ResponseFactory $responseFactory,
        \Magento\Framework\UrlInterface $url,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Theme\Block\Html\Header\Logo $logo,

        \Magento\Framework\Message\ManagerInterface $messageManager,

        BaniPay $banipay,
        Custom $custom,
        Encryptor $encryptor,

        CookieManagerInterface $cookieManager,
        CookieMetadataFactory $cookieMetadataFactory
        
        
    ) {
        $this->_logger = $logger;
        $this->_logo = $logo;

        $this->banipay = $banipay;
        $this->custom = $custom;
        $this->encryptor = $encryptor;

        $this->affiliate_code_demo = '141581ae-fb1f-4cfb-b21e-040a8851c265';
        $this->title = $this->banipay->getDataconfig('title');
        $this->affiliate_code = $this->banipay->getDataconfig('affiliate_code');
        $this->expire_minutes = $this->banipay->getDataconfig('expire_minutes');
        $this->failed_url = $this->banipay->getDataconfig('failed_url');
        $this->success_url = $this->banipay->getDataconfig('success_url');

        $this->cookieManager = $cookieManager;
        $this->cookieMetadataFactory = $cookieMetadataFactory;
        $this->messageManager = $messageManager;      
        
    }

    public function execute(EventObserver $observer) {

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
        
        // $this->_logger->debug('DEBUG: '.print_r($debug, true));

        // Products loading
        foreach($items as $item){
            $data = array(
                "concept"         => $item->getName(),
                "productImageUrl" =>  null, // $item->getProductId(),
                // "productImageUrl" => "https://i.blogs.es/322095/google-home-wifi/1366_2000.jpg",
                "quantity"        => $item->getQtyOrdered(),
                "unitPrice"       => $item->getPrice(),
            );
            array_push($this->details, $data);   
        }

        $this->add_shipping_method( $order->getShippingAmount(), $order->getShippingDescription() );

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



        if( (isset($_COOKIE["external_code"])) && ($_COOKIE["external_code"] == $encrypt) ) {
            
            // cart empry, redirect to $_COOKIE["url_transaction"]
            return;


        } else {

            // Cookie cart hash
            
            
            // Start class Transaction
            $affiliate = $this->banipay->getAffiliate( $this->affiliate_code );

            if ( ($this->affiliate_code_demo != $this->affiliate_code ) && isset( $affiliate->withInvoice ) ) {
                $data["withInvoice"] = $affiliate->withInvoice;
            }

            // Registration a transaction
            $this->_logger->debug('Data: '.print_r($data, true));
            // $this->_logger->debug('Params: '.print_r($params, true));
            $transaction = $this->banipay->register($data, $params);
            $this->_logger->debug('Transaction: '.(print_r($transaction, true)));
            // $this->_logger->debug('externalCode externalCode: '.($transaction->externalCode));




            if( isset($transaction) && !isset($transaction->status) ){
                    
                $this->custom->set('external_code', $transaction->externalCode);
                $this->custom->set('transaction_generated', $transaction->transactionGenerated);
                $this->custom->set('url_transaction', $transaction->urlTransaction);
                $this->custom->set('payment_id', $transaction->paymentId);

                $this->custom->set('increment_id', $increment_id);
                
                    // cart empty

                } else {
                    $this->messageManager->addError(__("El Código de Afiliado de BaniPay no es correcto."));
                    exit;
                }
        }


        // $this->banipay->getTest();
       
        return; // success
        exit; // return

    }

    function add_shipping_method( $shipping_total, $shipping_name) {

        if ($shipping_total > 0) {
            $data = array(
                "concept"         => $shipping_name,
                "productImageUrl" => $this->getLogoSrc(),
                "quantity"        => 1,
                "unitPrice"       => $shipping_total,
            );
            array_push($this->details, $data);   
        }
        return;

    }

    public function getLogoSrc() {    
        return $this->_logo->getLogoSrc();
    }

    
}