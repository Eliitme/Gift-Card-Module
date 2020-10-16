define(
    [
        'jquery',
        'Magento_Checkout/js/view/summary/abstract-total',
        'Magento_Checkout/js/model/quote',
        'Magento_Catalog/js/price-utils',
        'Magento_Checkout/js/model/totals',
    ],
    function ($, Component, quote, priceUtils, totals) {
        "use strict";
        return Component.extend({
            defaults: {
                template: 'Mageplaza_GiftCard/checkout/summary/customdiscount'
            },

            totals: quote.getTotals(),

            isLoading: totals.isLoading,

            isDisplayGiftCardDiscount: function () {
                if (totals.getSegment('customer_discount')) {
                    const price = parseFloat(totals.getSegment('customer_discount').value);

                    if (this.isFullMode() && price != 0) {
                        return true;
                    } else {
                        return false;
                    }
                } else {
                    return false;
                }
            },

            getCustomDiscount: function () {
                const price = parseFloat(totals.getSegment('customer_discount').value);
                return priceUtils.formatPrice(price);
                // return 10;
            },
        });
    }
);