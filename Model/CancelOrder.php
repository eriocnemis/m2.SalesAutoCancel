<?php
/**
 * Copyright © Eriocnemis, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Eriocnemis\SalesAutoCancel\Model;

use Magento\Sales\Model\Order;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Eriocnemis\SalesAutoCancel\Api\CancelOrderInterface;

/**
 * Cancel order by order id
 *
 * @api
 */
class CancelOrder implements CancelOrderInterface
{
    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * Initialize provider
     *
     * @param OrderRepositoryInterface $orderRepository
     */
    public function __construct(
        OrderRepositoryInterface $orderRepository
    ) {
        $this->orderRepository = $orderRepository;
    }

    /**
     * Cancel order
     *
     * @param int $orderId
     * @param int $age
     * @return void
     */
    public function execute(int $orderId, int $age): void
    {
        $order = $this->orderRepository->get($orderId);
        if ($order->canCancel()) {
            $order->setState(Order::STATE_CANCELED);
            $order->addCommentToStatusHistory(
                __(
                    'The order is canceled automatically due to the lack of information about payment for the order within %1 days.',
                    (string)$age
                ),
                Order::STATE_CANCELED,
                false // visible on front
            )->setIsCustomerNotified(false);

            $this->orderRepository->save($order);
        }
    }
}
