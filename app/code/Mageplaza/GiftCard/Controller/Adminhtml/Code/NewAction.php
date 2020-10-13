<?php


namespace Mageplaza\GiftCard\Controller\Adminhtml\Code;


use Magento\Backend\App\Action;
use Magento\Backend\Model\View\Result\ForwardFactory;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\View\Result\PageFactory;

class NewAction extends \Magento\Backend\App\Action
{
    protected $_forwardFactory;

    public function __construct(Action\Context $context, ForwardFactory $forwardFactory)
    {
        $this->_forwardFactory = $forwardFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $resultPage = $this->_forwardFactory->create();

        $resultPage->forward('add');
        return $resultPage;

    }
}
