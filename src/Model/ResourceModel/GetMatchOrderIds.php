<?php
/**
 * Copyright © Eriocnemis, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Eriocnemis\SalesAutoCancel\Model\ResourceModel;

use Psr\Log\LoggerInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Stdlib\DateTime;
use Magento\Framework\Stdlib\DateTime\DateTime as Date;
use Eriocnemis\SalesAutoCancel\Api\GetMatchOrderIdsInterface;
use Eriocnemis\SalesAutoCancel\Helper\Data as Helper;

/**
 * Retrieve match order ids
 */
class GetMatchOrderIds implements GetMatchOrderIdsInterface
{
    /**
     * Sales order table name
     */
    private const ORDER_TABLE_NAME = 'sales_order';

    /**
     * Sales order payment table name
     */
    private const PAYMENT_TABLE_NAME = 'sales_order_payment';

    /**
     * @var ResourceConnection
     */
    private $resource;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var Helper
     */
    private $helper;

    /**
     * @var DateTime
     */
    private $dateTime;

    /**
     * @var Date
     */
    private $date;

    /**
     * Initialize resource
     *
     * @param ResourceConnection $resource
     * @param LoggerInterface $logger
     * @param Helper $helper
     * @param DateTime $dateTime
     * @param Date $date
     */
    public function __construct(
        ResourceConnection $resource,
        LoggerInterface $logger,
        Helper $helper,
        DateTime $dateTime,
        Date $date
    ) {
        $this->resource = $resource;
        $this->logger = $logger;
        $this->helper = $helper;
        $this->dateTime = $dateTime;
        $this->date = $date;
    }

    /**
     * Retrieve match order ids
     *
     * @param int $storeId
     * @param string $method
     * @param int $age
     * @return int[]
     * @throws LocalizedException
     */
    public function execute(int $storeId, string $method, int $age): array
    {
        $orderIds = [];
        try {
            $createdAt = $this->dateTime->formatDate(
                (string)($this->date->gmtTimestamp() - 3600 * 24 * (int)$age)
            );

            $connection = $this->resource->getConnection();
            $select = $connection->select()->from(
                ['order' => $this->resource->getTableName(self::ORDER_TABLE_NAME)],
                [OrderInterface::ENTITY_ID]
            )->joinLeft(
                ['payment' => $this->resource->getTableName(self::PAYMENT_TABLE_NAME)],
                'order.entity_id = payment.parent_id',
                []
            )
            ->where('order.state IN(?)', $this->helper->getStatuses($storeId))
            ->where('order.store_id = ?', (string)$storeId)
            ->where('order.created_at <= ?', (string)$createdAt)
            ->where('payment.method = ?', (string)$method);

            $result = $connection->fetchCol($select);
            if ($result) {
                $orderIds = $result;
            }
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
            throw new LocalizedException(
                __('Could not retrieve match order ids')
            );
        }
        return $orderIds;
    }
}
