<?php
/**
 * Copyright © Eriocnemis, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Eriocnemis\SalesAutoCancel\Api;

use Magento\Framework\Exception\LocalizedException;

/**
 * Schedule bulk cancel of orders
 */
interface ScheduleBulkInterface
{
    /**
     * Schedule new bulk
     *
     * @param int[] $orderIds
     * @param int $age
     * @throws LocalizedException
     * @return void
     */
    public function execute(array $orderIds, int $age): void;
}
