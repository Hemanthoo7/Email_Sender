<?php

namespace Email\Sender\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

class Success implements ObserverInterface
{
    /**
     * @var TransportBuilder
     */
    protected $transportBuilder;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @param TransportBuilder $transportBuilder
     * @param StoreManagerInterface $storeManager
     * @param LoggerInterface $logger
     */
    public function __construct(
        TransportBuilder $transportBuilder,
        StoreManagerInterface $storeManager,
        LoggerInterface $logger
    ) {
        $this->transportBuilder = $transportBuilder;
        $this->storeManager = $storeManager;
        $this->logger = $logger;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $customer = $observer->getEvent()->getCustomer();
        if (!$customer) {
            return $this;
        }

        /* Receiver Detail */
        $receiverInfo = [
            'name' => 'Hemanth Kumar',
            'email' => 'hemanthkumarpatnala1@gmail.com'
        ];

        $store = $this->storeManager->getStore();

        $templateParams = ['store' => $store, 'customer' => $customer, 'administrator_name' => $receiverInfo['name']];

        $transport = $this->transportBuilder->setTemplateIdentifier(
            'email_sender_template'
        )->setTemplateOptions(
            ['area' => 'frontend', 'store' => $store->getId()]
        )->addTo(
            $receiverInfo['email'], $receiverInfo['name']
        )->setTemplateVars(
            $templateParams
        )->setFrom(
            'general'
        )->getTransport();

        try {
            $transport->sendMessage();
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
        }
        return $this;
    }
}
