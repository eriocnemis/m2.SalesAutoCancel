<?php
/**
 * Copyright © Eriocnemis, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Eriocnemis\SalesAutoCancel\Cron;

use Magento\Store\Api\StoreRepositoryInterface;
use Eriocnemis\SalesAutoCancel\Helper\Data as Helper;
use Eriocnemis\SalesAutoCancel\Model\Order\ManagerInterface;
use Eriocnemis\SalesAutoCancel\Model\Order\ScheduleBulk;

/**
 * Handling expired orders job
 */
class HandlingExpiredOrders
{
    /**
     * Schedule bulk
     *
     * @var ScheduleBulk
     */
    private $scheduleBulk;

    /**
     * Store repository
     *
     * @var StoreRepositoryInterface
     */
    private $storeRepository;

    /**
     * Order manager
     *
     * @var ManagerInterface
     */
    private $manager;

    /**
     * Helper
     *
     * @var Helper
     */
    private $helper;

    /**
     * Initialize job
     *
     * @param ScheduleBulk $scheduleBulk
     * @param StoreRepositoryInterface $storeRepository
     * @param ManagerInterface $manager
     * @param Helper $helper
     */
    public function __construct(
        ScheduleBulk $scheduleBulk,
        StoreRepositoryInterface $storeRepository,
        ManagerInterface $manager,
        Helper $helper
    ) {
        $this->scheduleBulk = $scheduleBulk;
        $this->storeRepository = $storeRepository;
        $this->manager = $manager;
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
                $orders = $this->manager->getOrderList($store->getId());
                $this->scheduleBulk->execute($orders);
            }
        }
    }
}
