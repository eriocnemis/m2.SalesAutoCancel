<?php
/**
 * Copyright © Eriocnemis, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Eriocnemis\SalesAutoCancel\Api;

use Magento\Framework\Exception\LocalizedException;

/**
 * Retrieve match order ids
 */
interface GetMatchOrderIdsInterface
{
    /**
     * Retrieve match order ids
     *
     * @param int $storeId
     * @param string $method
     * @param int $age
     * @return int[]
     * @throws LocalizedException
     */
    public function execute(int $storeId, string $method, int $age): array;
}
