<?php


namespace Mageplaza\GiftCard\Helper;


use Magento\Store\Model\ScopeInterface;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    const XML_PATH_GIFTCARD = 'giftcard/';

    public function getConfigValue($field, $storeId = null)
    {
        return $this->scopeConfig->getValue(
            $field, ScopeInterface::SCOPE_STORE, $storeId
        );
    }

    public function getGeneralConfig($code, $storeId = null)
    {
        $configValue = $this->getConfigValue(self::XML_PATH_GIFTCARD .'general/'. $code, $storeId);
        return $configValue;
    }

    public function getCodeConfig($code, $storeId = null)
    {
        $configValue = $this->getConfigValue(self::XML_PATH_GIFTCARD .'code/'. $code, $storeId);
        return $configValue;
    }
}
