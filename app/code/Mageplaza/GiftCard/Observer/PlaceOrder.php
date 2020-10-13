<?php


namespace Mageplaza\GiftCard\Observer;


use Magento\Catalog\Model\ProductFactory;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Event\Observer;
use Magento\Payment\Gateway\Http\Client\Zend;
use Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory;
use Mageplaza\GiftCard\Helper\Data;
use Mageplaza\GiftCard\Model\GiftCardFactory;
use Mageplaza\GiftCard\Model\GiftCardHistoryFactory;

class PlaceOrder implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var CollectionFactory
     */
    protected $item;
    /**
     * @var Data
     */
    protected $helperData;
    /**
     * @var GiftCardFactory
     */
    protected $giftCardFactory;
    /**
     * @var ResourceConnection
     */
    protected $resourceConnection;
    /**
     * @var ProductFactory
     */
    protected $productFactory;
    /**
     * @var GiftCardHistoryFactory
     */
    protected $giftCardHistoryFactory;

    /**
     * @inheritDoc
     */

    public function __construct(CollectionFactory $item,
                                Data $helperData,
                                GiftCardFactory $giftCardFactory,
                                GiftCardHistoryFactory $giftCardHistoryFactory,
                                ResourceConnection $resourceConnection,
                                ProductFactory $productFactory)
    {
        $this->item = $item;
        $this->helperData = $helperData;
        $this->giftCardFactory = $giftCardFactory;
        $this->resourceConnection = $resourceConnection;
        $this->productFactory = $productFactory;
        $this->giftCardHistoryFactory = $giftCardHistoryFactory;
    }

    public function execute(Observer $observer)
    {
        // TODO: Implement execute() method.
        $order = $observer->getEvent()->getData('order');

//        $test = $observer->getEvent()->getData('order');
//        $writer = new \Zend\Log\Writer\Stream(BP. '/var/log/test.log');
//        $logger = new \Zend\Log\Logger();
//        $logger->addWriter($writer);
//        $logger->info('Observer');
//
//        $logger->info(json_encode($test->getData()));

        $incrementId = $order->getIncrementId();
        $orderId = $order->getEntityId();
        $customerId = $order->getCustomerId();

        $item = $this->item->create()->addAttributeToFilter('order_id', $orderId)->loadData();

        $code_length = $this->helperData->getCodeConfig('code_length');

        $connection = $this->resourceConnection->getConnection()->beginTransaction();

        try {
            foreach ($item->getItems() as $item):
                $isVirtual = $item->getIsVirtual();
                $productId = $item->getProductId();

                $product = $this->productFactory->create()->load($productId);
                $amount = $product->getData('giftcard_amount');

                if ($isVirtual == 1 && $amount != null) {
                    for($i = 1; $i <= (int)$item->getQtyOrdered(); $i++) {
                        $code = $this->generateGiftCardCode($code_length);
                        $dataGiftCard = [
                            'code' => $code,
                            'balance' => $amount,
                            'amount_used' => 0,
                            'created_from' => 'Created From Order #' . $incrementId
                        ];

                        $this->giftCardFactory->create()->addData($dataGiftCard)->save();

                        $newGiftCard = $this->giftCardFactory->create()->getCollection()->addFieldToFilter('code', $code)->getFirstItem();

                        $dataHistory = [
                            'giftcard_id' => $newGiftCard->getData('giftcard_id'),
                            'customer_id' => $customerId,
                            'amount' => $amount,
                            'action' => 'Created From Order #' . $incrementId
                        ];

                        $this->giftCardHistoryFactory->create()->addData($dataHistory)->save();
                    }
                }
            endforeach;

            $connection->commit();
        } catch (\Exception $exception) {
            $connection->rollBack();
        }

    }

    function generateGiftCardCode($length)
    {
        $characters = 'ABCDEFGHIJKLMLOPQRSTUVXYZ0123456789';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        return $randomString;
    }

}
