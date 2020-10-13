<?php


namespace Mageplaza\GiftCard\Controller\Adminhtml\Code;


use Magento\Backend\App\Action;
use Magento\Framework\Exception\LocalizedException;
use Mageplaza\GiftCard\Helper\Data;
use Mageplaza\GiftCard\Model\GiftCardFactory;


class Save extends \Magento\Backend\App\Action
{


    protected $_helperData;

    protected $_giftCardModel;


    public function __construct(Action\Context $context, Data $helperData, GiftCardFactory $giftCardModel)
    {
        parent::__construct($context);

        $this->_helperData = $helperData;
        $this->_giftCardModel = $giftCardModel;
    }

    function generateGiftCardCode($length)
    {
        $characters = 'ABCDEFGHIJKLMLOPQRSTUVXYZ0123456789';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        return $randomString;
    }

    public function execute()
    {
//        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/test.log');
//        $logger = new \Zend\Log\Logger();
//        $logger->addWriter($writer);
//        $logger->info('abc');
        // TODO: Implement execute() method.
        $id = $this->getRequest()->getParam('id');
        $data = $this->getRequest()->getParams();

        if($id){

            print_r($data);
            $code = $this->_giftCardModel->create()->load($id);

            $code->setBalance($data['balance'])->save();

            if(!isset($data['back'])) {

                $this->messageManager->addSuccess(__('The gift card '. $data['code']. ' has been updated.'));

                return $this->_redirect('*/*/index')->sendResponse();
            } else {
                return $this->_redirect('*/*/edit/giftcard_id/' . $id);
            }

        } else {
            $code = $this->_giftCardModel->create();

            $codeRamdom = $this->generateGiftCardCode($data['code_length']);

            $code->setData(
                [
                    'code' => $codeRamdom,
                    'balance' => $data['balance'],
                    'amount_used' => 0,
                    'created_from' => $this->_session->getName()
                ]
            );

            $code->save();

            $newCode = $this->_giftCardModel->create()->load($codeRamdom, 'code')->getData('giftcard_id');

            if(isset($data['back'])){
                return $this->_redirect('*/*/edit/giftcard_id/'.$newCode);
            } else {
                $this->messageManager->addSuccess(__('A gift card has been created.'));

                return $this->_redirect('*/*/index')->sendResponse();
            }

        }

    }


}
