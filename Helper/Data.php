<?php
/**
 * Copyright © Eriocnemis, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Eriocnemis\SalesAutoCancel\Helper;

use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\Serialize\SerializerInterface;

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
     * Payments methods config path
     */
    const XML_PAYMENTS = 'sales/eriocnemis_sales_autocancel/payments';

    /**
     * Order statuses config path
     */
    const XML_STATUSES = 'sales/eriocnemis_sales_autocancel/statuses';

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * Initialize helper
     *
     * @param Context $context
     * @param SerializerInterface $serializer
     */
    public function __construct(
        Context $context,
        SerializerInterface $serializer
    ) {
        $this->serializer = $serializer;

        parent::__construct(
            $context
        );
    }

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
     * Retrieve Payments methods config
     *
     * @param int|null $storeId
     * @return mixed[]
     */
    public function getPayments($storeId = null)
    {
        $payments = [];
        $configData = (array)$this->serializer->unserialize(
            $this->getValue(self::XML_PAYMENTS, $storeId)
        );

        foreach ($configData as $data) {
            $payments[$data['method']] = $data['age'];
        }
        return $payments;
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
