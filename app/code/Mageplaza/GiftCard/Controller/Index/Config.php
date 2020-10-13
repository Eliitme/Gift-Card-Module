<?php


namespace Mageplaza\GiftCard\Controller\Index;


use Magento\Framework\App\ResponseInterface;

class Config extends \Magento\Framework\App\Action\Action
{

    protected $helperData;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Mageplaza\GiftCard\Helper\Data $helperData

    )
    {
        $this->helperData = $helperData;
        return parent::__construct($context);
    }

    public function execute()
    {
        // TODO: Implement execute() method.

        echo $this->helperData->getGeneralConfig('allow_redeem_gift_card');

        exit();

    }
}
