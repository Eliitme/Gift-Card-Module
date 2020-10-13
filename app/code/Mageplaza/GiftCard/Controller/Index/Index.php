<?php


namespace Mageplaza\GiftCard\Controller\Index;


use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\View\Result\PageFactory;
use Mageplaza\GiftCard\Model\GiftCard;
use Mageplaza\GiftCard\Model\GiftCardFactory;
use Mageplaza\GiftCard\Model\ResourceModel\GiftCardCustomerBalance\CollectionFactory;

class Index extends \Magento\Framework\App\Action\Action
{

    /**
     * @inheritDoc
     */
    protected $_pageFactory;

    protected $_giftCardFactory;

    private $giftCardCustomerBalanceFactory;

    public function __construct(Context $context, PageFactory $pageFactory,
                                GiftCardFactory $giftCardFactory,
                                CollectionFactory $giftCardCustomerBalanceFactory
                                )
    {
        $this->_giftCardFactory = $giftCardFactory;
        $this->_pageFactory = $pageFactory;
        $this->giftCardCustomerBalanceFactory = $giftCardCustomerBalanceFactory;


        return parent::__construct($context);
    }

    public function getList()
    {
        $post = $this->_giftCardFactory->create();
        $collection = $post->getCollection();
    }

    public function execute()
    {

        $data = [
            'customer_id' => 1,
            'balance' => 123
        ];

        $a = $this->giftCardCustomerBalanceFactory->create()->getConnection()->insert('giftcard_customer_balance', $data);
//        $data = [
//            'giftcard_id' => 13,
//            'customer_id' => 1,
//            'amount' => 0,
//            'action' => 'redeem',
//        ];
//
//        $b = $this->giftCardHistoryFactory->create()->addData($data)->save();
//        $test = $this->giftCardCustomerBalanceFactory->create()->load(1, 'customer_id');

    }

}
