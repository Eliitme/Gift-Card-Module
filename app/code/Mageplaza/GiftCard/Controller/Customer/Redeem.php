<?php


namespace Mageplaza\GiftCard\Controller\Customer;


use Magento\Customer\Helper\Session\CurrentCustomer;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Stdlib\DateTime;
use Mageplaza\GiftCard\Helper\Data;
use Mageplaza\GiftCard\Model\GiftCardFactory;
use Mageplaza\GiftCard\Model\ResourceModel\GiftCard\CollectionFactory;

class Redeem extends \Magento\Framework\App\Action\Action
{

    /**
     * @inheritDoc
     */

    const GIFTCARD_CUSTOMER_BALANCE = 'giftcard_customer_balance';

    protected $_giftCardFactory;
    protected $_helperData;
    /**
     * @var CollectionFactory
     */
    protected $_collectionFactory;
    /**
     * @var \Mageplaza\GiftCard\Model\GiftCardHistoryFactory
     */
    protected $_giftCardHistoryFactory;

    protected $_resourceConnection;
    /**
     * @var CurrentCustomer
     */
    protected $_currentCustomer;

    protected $_giftCardCustomerBalanceFactory;


    public function __construct(Context $context,
                                Data $helperData,
                                \Mageplaza\GiftCard\Model\GiftCardHistoryFactory $giftCardHistoryFactory,
                                \Mageplaza\GiftCard\Model\ResourceModel\GiftCardCustomerBalance\CollectionFactory $giftCardCustomerBalanceFactory,
                                CurrentCustomer $currentCustomer,
                                CollectionFactory $collectionFactory,
                                ResourceConnection $resourceConnection,
                                GiftCardFactory $giftCardFactory)
    {
        $this->_helperData = $helperData;
        $this->_giftCardFactory = $giftCardFactory;
        $this->_collectionFactory = $collectionFactory;
        $this->_giftCardHistoryFactory = $giftCardHistoryFactory;
        $this->_resourceConnection = $resourceConnection;
        $this->_currentCustomer = $currentCustomer;
        $this->_giftCardCustomerBalanceFactory = $giftCardCustomerBalanceFactory;

        return parent::__construct($context);
    }


    public function execute()
    {
        // Kiem tra co dc redeem hay khong?
        $allowRedeem = $this->_helperData->getGeneralConfig('allow_redeem_gift_card');
        $enableGiftCard = $this->_helperData->getGeneralConfig('enable_gift_card');

        if ($allowRedeem == 1 && $enableGiftCard == 1) {
            $param = $this->getRequest()->getParams();

            $code_redeem = $param['code_redeem'];

            // Kiem tra code co ton tai hay khong?
            $giftCardCollection = $this->_collectionFactory->create()->addFieldToFilter('code', $code_redeem)->load()->getFirstItem();

            if ($giftCardCollection->getData() == null) {
                $this->messageManager->addWarningMessage("This is not a valid gift card. Please try again!");
            } else {

                $balanceGiftCard = $giftCardCollection['balance'];
                $amountUsedGiftCard = $giftCardCollection['amount_used'];

                if ($balanceGiftCard <= $amountUsedGiftCard) {
                    $this->messageManager->addWarningMessage("This gift card has been used. Please try other gift card!");
                } else {
                    $connection = $this->_resourceConnection->getConnection()->beginTransaction();

                    try {
                        $balance = $balanceGiftCard - $amountUsedGiftCard;

                        $giftCard = $this->_giftCardFactory->create()->load($giftCardCollection['giftcard_id']);

                        $giftCard->setAmountUsed($balance)->save();

                        $customerId = $this->getCurrentCustomer()->getCustomerId();

                        $customerBalance = $this->_giftCardCustomerBalanceFactory->create()->addFieldToFilter('customer_id', $customerId);

                        if ($customerBalance->getData() == null) {

                            $dataBalance = [
                                'customer_id' => $customerId,
                                'balance' => $balance
                            ];

                            $this->_giftCardCustomerBalanceFactory->create()->getConnection()->insert(self::GIFTCARD_CUSTOMER_BALANCE, $dataBalance);
                        } else {
                            $customerBalance = $customerBalance->getFirstItem();

                            $dataBalance = $balance + $customerBalance->getData('balance');

                            $customerBalance->setData('balance', $dataBalance)->save();

                        }

                        $action = $this->getRequest()->getActionName();

                        $dataHistory = [
                            'giftcard_id' => $giftCard->getData('giftcard_id'),
                            'customer_id' => $customerId,
                            'amount' => $balance,
                            'action' => $action,
                        ];

                        $this->_giftCardHistoryFactory->create()->addData($dataHistory)->save();

                        $this->messageManager->addSuccessMessage("Redeem gift card successfully!");

                        $connection->commit();
                    } catch (\Exception $exception) {
                        $this->messageManager->addErrorMessage("A problem occured when redeem gift card. Please try again!");
                        $connection->rollBack();
                    }

                }

            }

//            $this->messageManager->addSuccessMessage($code_redeem);
        } else {
            $this->messageManager->addWarningMessage("Not allow redeem gift card now, please contact");
        }

        return $this->_redirect($this->_redirect->getRefererUrl())->sendResponse();
    }


    /**
     * @return CurrentCustomer
     */
    public function getCurrentCustomer()
    {
        return $this->_currentCustomer;
    }

}
