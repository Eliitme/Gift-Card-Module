<?php


namespace Mageplaza\GiftCard\Model\ResourceModel;

use \Magento\Framework\Model\ResourceModel\Db\Context;


class GiftCardHistory extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    /**
     * @inheritDoc
     */

    public function __construct(Context $context)
    {
        parent::__construct($context);
    }

    protected function _construct()
    {
        // TODO: Implement _construct() method.
        $this->_init('mageplaza_giftcard_history', 'history_id');
    }
}
