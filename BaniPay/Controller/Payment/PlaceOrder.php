<?php
namespace Khipu\Payment\Controller\Payment;

use BaniPayPaymentGateway3\BaniPay\Model\BaniPay;
use Magento\Checkout\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Sales\Model\OrderFactory;
use Psr\Log\LoggerInterface;

class PlaceOrder extends Action
{
    protected $orderFactory;
    protected $banipay;
    protected $checkoutSession;
    protected $logger;

    public function __construct(
        Context $context,
        OrderFactory $orderFactory,
        Session $checkoutSession,
        BaniPay $banipay,
        LoggerInterface $logger
    )
    {
        parent::__construct($context);

        $this->orderFactory = $orderFactory;
        $this->banipay = $banipay;
        $this->checkoutSession = $checkoutSession;
        $this->logger = $logger;
    }

    public function execute()
    {

        $id = $this->checkoutSession->getLastOrderId();


        $order = $this->orderFactory->create()->load($id);


        if (!$order->getIncrementId()) {
            $this->getResponse()->setBody(json_encode(array(
                'status' => false,
                'reason' => 'Order Not Found',
            )));

            return;
        }

        // $this->getResponse()->setBody(json_encode($this->banipay->getKhipuRequest($order)));

        return $this->banipay->getTest();
    }
}