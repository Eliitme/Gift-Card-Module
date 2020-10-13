<?php


namespace Mageplaza\GiftCard\Controller\Customer;


use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Mageplaza\GiftCard\Helper\Data;

class Index extends \Magento\Framework\App\Action\Action
{

    /**
     * @inheritDoc
     */

    protected $_session;

    protected $_helperData;

    public function __construct(Context $context, Session $session, Data $helperData)
    {
        $this->_helperData = $helperData;
        $this->_session = $session;
        parent::__construct($context);
    }

    public function execute()
    {
        if($this->_session->isLoggedIn()) {
            if($this->_helperData->getGeneralConfig('enable_gift_card') == 1) {
                $this->_view->loadLayout();
                $this->_view->renderLayout();
            } else {
                $this->messageManager->addWarningMessage('The Gift Card has been disable!');

                return $this->_redirect('customer/account')->sendResponse();
            }

        } else {

            $this->messageManager->addWarningMessage('Please Log In To Use!');

            return $this->_redirect('customer/account/login')->sendResponse();
        }


    }
}
