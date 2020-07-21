<?php
/**
 * Copyright © Eriocnemis, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Eriocnemis\SalesAutoCancel\Helper;

use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Helper\AbstractHelper;

/**
 * Helper
 */
class Data extends AbstractHelper
{
    /**
     * Module enabled config path
     */
    const XML_ENABLED = 'sales/eriocnemis_sales_autocancel/enabled';

    /**
     * Order age config path
     */
    const XML_AGE = 'sales/eriocnemis_sales_autocancel/age';

    /**
     * Order statuses config path
     */
    const XML_STATUSES = 'sales/eriocnemis_sales_autocancel/statuses';

    /**
     * Check module functionality should be enabled
     *
     * @param int|null $storeId
     * @return bool
     */
    public function isEnabled($storeId = null)
    {
        return $this->isSetFlag(static::XML_ENABLED, $storeId);
    }

    /**
     * Retrieve order age
     *
     * @param int|null $storeId
     * @return string
     */
    public function getAge($storeId = null)
    {
        return $this->getValue(self::XML_AGE, $storeId);
    }

    /**
     * Retrieve order statuses
     *
     * @param int|null $storeId
     * @return string[]
     */
    public function getStatuses($storeId = null)
    {
        return explode(',', $this->getValue(self::XML_STATUSES, $storeId));
    }

    /**
     * Retrieve config value by path and scope
     *
     * @param string $path
     * @param int|null $storeId
     * @return mixed
     */
    protected function getValue($path, $storeId = null)
    {
        return $this->scopeConfig->getValue($path, ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * Retrieve config flag
     *
     * @param string $path
     * @param int|null $storeId
     * @return bool
     */
    protected function isSetFlag($path, $storeId = null)
    {
        return $this->scopeConfig->isSetFlag($path, ScopeInterface::SCOPE_STORE, $storeId);
    }
}
