<?php


namespace Mageplaza\GiftCard\Controller\Adminhtml\Code;


use Magento\Framework\App\ResponseInterface;

class Edit extends \Magento\Backend\App\Action
{
    protected $_resultPageFactory;

    public function __construct(
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Backend\App\Action\Context $context
    )
    {
        $this->_resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }


    public function execute()
    {
        $resultPage = $this->_resultPageFactory->create();
//
        return $resultPage;

  }

}
