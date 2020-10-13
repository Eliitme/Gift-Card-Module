<?php


namespace Mageplaza\GiftCard\Model\ResourceModel\GiftCardCustomerBalance;


class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'customer_id';
    protected $_eventPrefix = 'giftcard_customer_balance_collection';
    protected $_eventObject = 'balance_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Mageplaza\GiftCard\Model\GiftCardCustomerBalance', 'Mageplaza\GiftCard\Model\ResourceModel\GiftCardCustomerBalance');
    }

}
