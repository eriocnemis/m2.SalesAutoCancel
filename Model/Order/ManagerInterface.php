<?php
/**
 * Copyright © Eriocnemis, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Eriocnemis\SalesAutoCancel\Model\Order;

use Magento\Sales\Api\Data\OrderInterface;

/**
 * Order manager interface
 */
interface ManagerInterface
{
    /**
     * Retrieve expired orders from specific store
     *
     * @param int $storeId
     * @return OrderInterface[]
     */
    public function getOrderList($storeId);

    /**
     * Order cancel
     *
     * @param int $orderId
     * @return void
     */
    public function cancel($orderId);
}
