<?php

namespace Mageplaza\GiftCard\Block\Customer\Code;

use Magento\Customer\Helper\Session\CurrentCustomer;
use Magento\Framework\Pricing\Helper\Data;
use Magento\Framework\View\Element\Template;
use Mageplaza\GiftCard\Model\ResourceModel\GiftCardHistory\CollectionFactory;

class History extends Template
{

    const HISTORY_LIMIT = 5;

    const MAIN_TABLE = 'mageplaza_giftcard_history';

    const JOIN_TABLE = 'mageplaza_giftcard_code';

    protected $_currentCustomer;

    protected $_collectionFactory;
    /**
     * @var Data
     */
    private $price;

    public function __construct(Template\Context $context,
                                array $data = [],
                                Data $price,
                                CurrentCustomer $currentCustomer,
                                CollectionFactory $collectionFactory)
    {


        $this->_collectionFactory = $collectionFactory;
        $this->_currentCustomer = $currentCustomer;
        $this->price = $price;

        return parent::__construct($context, $data);
    }

    protected function _construct()
    {
        parent::_construct(); // TODO: Change the autogenerated stub
        return $this->getGiftCardHistory();
    }

    public function getGiftCardHistory() {
        $id = $this->getCurrentCustomer()->getCustomerId();

        $history = $this->_collectionFactory->create()
            ->addFieldToSelect('*')
            ->addFieldToFilter('customer_id', $id)
            ->setOrder('action_time', 'desc')
            ->setPageSize(self::HISTORY_LIMIT)
            ->join(self::JOIN_TABLE,
                'mageplaza_giftcard_code.giftcard_id = main_table.giftcard_id',
                '*')
            ->load();

        $this->setHistory($history);
    }

    /**
     * @param Data $price
     */
    public function setPrice($price)
    {
        return $this->price->currency($price, true, true);
    }

    /**
     * @return CurrentCustomer
     */
    public function getCurrentCustomer()
    {
        return $this->_currentCustomer;
    }


}