<?php


namespace Mageplaza\GiftCard\Plugin;


use Magento\Checkout\Controller\Cart;
use Magento\Checkout\Controller\Cart\CouponPost;
use Magento\Checkout\Model\Cart as CustomerCart;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Setup\Exception;
use Mageplaza\GiftCard\Helper\Data;
use Mageplaza\GiftCard\Model\GiftCardFactory;

class CheckCouponCart extends Cart
{
    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    private $quoteRepository;
    /**
     * @var \Magento\SalesRule\Model\CouponFactory
     */
    private $couponFactory;
    /**
     * @var Data
     */
    private $dataHelper;
    /**
     * @var GiftCardFactory
     */
    private $giftCardFactory;

    public function __construct(\Magento\Framework\App\Action\Context $context,
                                \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
                                \Magento\Checkout\Model\Session $checkoutSession,
                                \Magento\Store\Model\StoreManagerInterface $storeManager,
                                \Magento\Quote\Api\CartRepositoryInterface $repository,
                                Data $dataHelper,
                                GiftCardFactory $giftCardFactory,
                                \Magento\SalesRule\Model\CouponFactory $couponFactory,
                                \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
                                CustomerCart $cart)
    {
        parent::__construct($context, $scopeConfig, $checkoutSession, $storeManager, $formKeyValidator, $cart);
        $this->quoteRepository = $repository;
        $this->couponFactory = $couponFactory;
        $this->dataHelper = $dataHelper;
        $this->giftCardFactory = $giftCardFactory;
    }

    public function execute()
    {
        // TODO: Implement execute() method.

    }

    public function aroundExecute(CouponPost $subject, callable $proceed)
    {
        $giftCardCode = $subject->getRequest()->getParam('remove') == 1
            ? ''
            : trim($subject->getRequest()->getParam('coupon_code'));

        $cartQuote = $this->cart->getQuote();
        $oldGiftCardCode = $cartQuote->getGiftcardCode();

        $codeLength = strlen($giftCardCode);
        $itemsCount = $cartQuote->getItemsCount();

        if (!$codeLength && !strlen($oldGiftCardCode)) {
            return $proceed();
        }

        $isAllowGiftCardCheckout = $this->dataHelper->getGeneralConfig('allow_use_gift_card');
        $giftCard = $this->giftCardFactory->create()->load($giftCardCode, 'code');
        if ($isAllowGiftCardCheckout) {
            if ($codeLength) {
                if($itemsCount && $giftCard->getId()) {
                    $restBalance = $giftCard['balance'] - $giftCard['amount_used'];

                    if ($restBalance > 0) {
                        $subject->_checkoutSession->getQuote()->setGiftcardCode($giftCardCode)->save();

                        $this->messageManager->addSuccessMessage('You use gift card code successfully!');
                        return $subject->_goBack();
                    } else {
                        return $proceed();
                    }
                } else {
                    return $proceed();
                }

            } else {
                $this->_checkoutSession->getQuote()->setGiftcardCode($giftCardCode)->save();
                $this->_checkoutSession->getQuote()->setGiftcardDiscount(0)->save();

                $this->messageManager->addSuccessMessage(__('You canceled the gift card code'));

                return $subject->_goBack();
            }
        } else {
            return $proceed();
        }
    }
}
