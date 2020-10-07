<?php

namespace BaniPayPaymentGateway3\BaniPay\Model;

// class BaniPay extends \Magento\Payment\Model\Method\AbstractMethod
// class BaniPay extends AbstractCarrier implements CarrierInterface
// class BaniPay extends \Magento\Payment\Model\Method\Cc {
class BaniPay extends \Magento\Payment\Model\Method\Cc {

    // protected $_code = 'banipay';

    const CODE = 'banipay';
    protected $_code = self::CODE;
    protected $_canAuthorize = true;
    protected $_canCapture = true;
    protected $_isGateway = true;
    protected $_countryFactory;
    protected $cart = null;
    
    public function __construct( \Magento\Framework\Model\Context $context,
    \Magento\Framework\Registry $registry, 
    \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
    \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory,
    \Magento\Payment\Helper\Data $paymentData, 
    \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig, 
    \Magento\Payment\Model\Method\Logger $logger, 
    \Magento\Framework\Module\ModuleListInterface $moduleList,
    \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
    \Magento\Directory\Model\CountryFactory $countryFactory,
    \Magento\Checkout\Model\Cart $cart,
    array $data = array() 
   ) {
      parent::__construct( $context, $registry, $extensionFactory, $customAttributeFactory, $paymentData, $scopeConfig, $logger, $moduleList, $localeDate, null, null, $data );
        $this->cart = $cart; $this->_countryFactory = $countryFactory;
   }

    public function capture(\Magento\Payment\Model\InfoInterface $payment, $amount) {
        //todo add functionality later
        // error_log(print_r("VULCANBO VULCANBO", true));
        //$this->_logger->debug('vulcanbo vulcanbo vulcanbo '); 
        // $this->debug('vulcanbo vulcanbo vulcanbo ');
        // Mage::log('vulcanbo vulcanbo vulcanbo capture', null, 'debug.log', true);
        // error_log('vulcanbo vulcanbo vulcanbo capture');

        // $order = $payment->getOrder();

        // $this->_logger->error(__('vulcanbo capture model'));

        return $this;

    }

    /* public function getOrderPlaceRedirectUrl() {
        Mage::Log('Returning Redirect URL:: ' . Mage::getSingleton('customer/session')->getLocalRedirect());
        // return Mage::getSingleton('customer/session')->getLocalRedirect();
        // return Mage::getUrl('customcard/standard/redirect', array('_secure' => true));
        $this->_redirectUrl('https://diebian.dev');

    } */

    /* public function authorize(\Magento\Payment\Model\InfoInterface $payment, $amount) {
        // Mage::log('vulcanbo vulcanbo vulcanbo authorize', null, 'debug.log', true);
    } */

  /*   public function assignData($data)
    {
        // Mage::log('vulcanbo vulcanbo vulcanbo assignData', null, 'debug.log', true);
    } */
}