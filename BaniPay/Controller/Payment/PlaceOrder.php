<?php
namespace BaniPayPaymentGateway3\BaniPay\Controller\Payment;

use BaniPayPaymentGateway3\BaniPay\Model\BaniPay;
use Magento\Checkout\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Sales\Model\OrderFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use \Psr\Log\LoggerInterface;

class Index extends Action
{
    protected $banipay;

    protected $orderFactory;
    protected $coingatePayment;
    protected $checkoutSession;
    protected $scopeConfig;

    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $_eventManager;

    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $quoteRepository;

    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        BaniPay $banipay,

        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        Context $context,
        OrderFactory $orderFactory,
        Session $checkoutSession,
        CoinGatePayment $coingatePayment,
        ScopeConfigInterface $scopeConfig
       
    )
    {

        $this->banipay = $banipay;
        $this->_logger = $logger;

        parent::__construct($context);
        $this->quoteRepository = $quoteRepository;
        $this->_eventManager = $eventManager;
        $this->orderFactory = $orderFactory;
        $this->coingatePayment = $coingatePayment;
        $this->checkoutSession = $checkoutSession;
        $this->scopeConfig = $scopeConfig;
    }


    

    protected function _getCheckout()
    {
        return $this->_objectManager->get('Magento\Checkout\Model\Session');
    }


    public function execute()
    {
        $this->banipay->getTest();
        $id = $this->checkoutSession->getLastOrderId();

        $order = $this->orderFactory->create()->load($id);

        if (!$order->getIncrementId()) {
            $this->getResponse()->setBody(json_encode([
                'status' => false,
                'reason' => 'Order Not Found',
            ]));
            return;
        }

        ///Restores Cart
        $quote = $this->quoteRepository->get($order->getQuoteId());
        $quote->setIsActive(1);
        $this->quoteRepository->save($quote);

        $this->getResponse()->setBody(json_encode($this->coingatePayment->getCoinGateRequest($order)));
    }
}