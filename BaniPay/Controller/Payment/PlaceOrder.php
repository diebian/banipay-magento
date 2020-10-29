<?php
namespace BaniPayPaymentGateway3\BaniPay\Controller\Payment;

use BaniPayPaymentGateway3\BaniPay\Model\BaniPay;
use Magento\Checkout\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Sales\Model\OrderFactory;
// use Psr\Log\LoggerInterface;

//use BaniPayPaymentGateway3\BaniPay\Logger\Logger;

class PlaceOrder extends Action
{
    protected $orderFactory;
    protected $banipay;
    protected $checkoutSession;
    // protected $logger;
    protected $_logger;

    public function __construct(
        Context $context,
        OrderFactory $orderFactory,
        Session $checkoutSession,
        BaniPay $banipay
        // LoggerInterface $logger,
        // Logger $_logger
    )
    {
        parent::__construct($context);

        $this->orderFactory = $orderFactory;
        $this->banipay = $banipay;
        $this->checkoutSession = $checkoutSession;
        // $this->logger = $logger;
        // $this->_logger = $_logger;
        // Mage::log('controller from construct vulcanbo', null, 'banipay.log', true);
        // Mage::log('controller from construct vulcanbo', null, 'system.log', true);
    }

    public function execute()
    {

        // $id = $this->checkoutSession->getLastOrderId();
        // $order = $this->orderFactory->create()->load($id);

        // $this->_logger->info('vulcanbo vulcanbo vulcanbo');
        /* Mage::log('controller from execute vulcanbo', null, 'banipay.log', true);
        Mage::log('controller from execute vulcanbo', null, 'system.log', true);


        if (!$order->getIncrementId()) {
            $this->getResponse()->setBody(json_encode(array(
                'status' => false,
                'reason' => 'Order Not Found',
            )));

            return;
        } */

        // return $this->banipay->getTest();
        return exit;
    }
}