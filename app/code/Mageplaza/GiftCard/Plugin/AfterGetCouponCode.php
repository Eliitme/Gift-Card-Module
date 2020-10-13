<?php


namespace Mageplaza\GiftCard\Plugin;


use Magento\Checkout\Block\Cart\Coupon;

class AfterGetCouponCode
{
    public function afterGetCouponCode(Coupon $coupon, $result)
    {
        if ($coupon->getQuote()->getGiftcardCode()) {

            $result = $coupon->getQuote()->getGiftcardCode();

        } else {

            $result = $coupon->getQuote()->getCouponCode();
        }

        return $result;
    }
}
