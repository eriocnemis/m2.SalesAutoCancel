<?php
/**
 * Copyright © Eriocnemis, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Eriocnemis\SalesAutoCancel\Api;

/**
 * Cancel order by order id
 *
 * @api
 */
interface CancelOrderInterface
{
    /**
     * Cancel order
     *
     * @param int $orderId
     * @param int $age
     * @return void
     */
    public function execute(int $orderId, int $age): void;
}
