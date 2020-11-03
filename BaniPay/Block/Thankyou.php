<?php
namespace BaniPayPaymentGateway3\BaniPay\Block;

use BaniPayPaymentGateway3\BaniPay\Model\BaniPay;

class Thankyou extends \Magento\Sales\Block\Order\Totals
{
    protected $checkoutSession;
    protected $customerSession;
    protected $_orderFactory;

    protected $banipay;
    protected $resultRedirectFactory;
    
    public function __construct(
        \Magento\Framework\Controller\Result\Redirect $resultRedirectFactory,

        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = [],
        BaniPay $banipay
    ) {
        parent::__construct($context, $registry, $data);
        $this->checkoutSession = $checkoutSession;
        $this->customerSession = $customerSession;
        $this->_orderFactory = $orderFactory;
        $this->banipay = $banipay;
        $this->resultRedirectFactory = $resultRedirectFactory;
    }

    public function execute()
    {
        $this->banipay->getTest();
        $resultRedirect = $this->resultRedirectFactory->create();
        $redirectLink = 'https://diebian.dev'; 
        $resultRedirect->setUrl($redirectLink);
        return $resultRedirect;
    }

    public function getOrder()
    {

        $this->banipay->getTest();

        return  $this->_order = $this->_orderFactory->create()->loadByIncrementId(
            $this->checkoutSession->getLastRealOrderId());
    }

    public function getCustomerId()
    {
        $this->banipay->getTest();
        return $this->customerSession->getCustomer()->getId();
    }
}