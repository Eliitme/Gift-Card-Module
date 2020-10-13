<?php


namespace Mageplaza\GiftCard\Controller\Index;


use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ProductFactory;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory;

class Test extends \Magento\Framework\App\Action\Action
{
    /**
     * @var Order
     */
    protected $order;
    /**
     * @var Order\Item
     */
    private $item;
    /**
     * @var Product
     */
    private $product;

    /**
     * @inheritDoc
     */
    public function __construct(Context $context, Order $order, CollectionFactory $item, ProductFactory $product)
    {
        $this->order = $order;
        $this->item = $item;
        $this->product = $product;
        parent::__construct($context);

    }

    public function execute()
    {
        // TODO: Implement execute() method.
        $order = $this->order->load(45);
//
//        $item = $this->item->create()
//            ->addAttributeToSelect('*')
//            ->addAttributeToFilter('order_id', 36)->loadData();
//
        foreach ($order->getItems() as $item):
            $id = $item->getProductId();

            $itemOrder = $item;
//
            $product = $this->product->create()->load($id);
//
            print_r(json_encode($itemOrder->getData()));

            echo "<br>";
        endforeach;

//        print_r(json_encode($item->getData()));


    }
}
