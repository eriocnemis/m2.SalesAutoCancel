<?php
/**
 * Copyright © Eriocnemis, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Eriocnemis\SalesAutoCancel\Cron;

use Magento\Store\Api\StoreRepositoryInterface;
use Eriocnemis\SalesAutoCancel\Api\GetMatchOrderIdsInterface;
use Eriocnemis\SalesAutoCancel\Api\ScheduleBulkInterface;
use Eriocnemis\SalesAutoCancel\Helper\Data as Helper;

/**
 * Handling expired orders job
 */
class HandlingExpiredOrders
{
    /**
     * @var ScheduleBulkInterface
     */
    private $scheduleBulk;

    /**
     * @var StoreRepositoryInterface
     */
    private $storeRepository;

    /**
     * @var GetMatchOrderIdsInterface
     */
    private $getMatchOrderIds;

    /**
     * @var Helper
     */
    private $helper;

    /**
     * Initialize job
     *
     * @param ScheduleBulkInterface $scheduleBulk
     * @param StoreRepositoryInterface $storeRepository
     * @param GetMatchOrderIdsInterface $getMatchOrderIds
     * @param Helper $helper
     */
    public function __construct(
        ScheduleBulkInterface $scheduleBulk,
        StoreRepositoryInterface $storeRepository,
        GetMatchOrderIdsInterface $getMatchOrderIds,
        Helper $helper
    ) {
        $this->scheduleBulk = $scheduleBulk;
        $this->storeRepository = $storeRepository;
        $this->getMatchOrderIds = $getMatchOrderIds;
        $this->helper = $helper;
    }

    /**
     * Canceling expired orders
     *
     * @return void
     */
    public function execute()
    {
        foreach ($this->storeRepository->getList() as $store) {
            if ($this->helper->isEnabled($store->getId())) {
                foreach ($this->helper->getPayments($store->getId()) as $method => $age) {
                    $orderIds = $this->getMatchOrderIds->execute($store->getId(), $method, $age);
                    $this->scheduleBulk->execute($orderIds, $age);
                }
            }
        }
    }
}
