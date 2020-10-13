<?php


namespace Mageplaza\GiftCard\Controller\Adminhtml\Code;


use Magento\Backend\App\Action;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Exception\LocalizedException;
use Mageplaza\GiftCard\Model\GiftCardFactory;



class Delete extends \Magento\Backend\App\Action
{

    /**
     * @inheritDoc
     */

    protected $_giftCardFactory;

    public function __construct(Action\Context $context, GiftCardFactory $giftCardFactory)
    {
        $this->_giftCardFactory = $giftCardFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        // TODO: Implement execute() method.

        $id = $this->getRequest()->getParam('giftcard_id');

        $giftCard = $this->_giftCardFactory->create()->load($id);

        $code = $giftCard->getData('code');
        $giftCard->delete();

        $this->messageManager->addSuccess(__('The gift card '. strtoupper($code). ' have been deleted.'));

        return $this->_redirect('*/*/index')->sendResponse();
    }
}
