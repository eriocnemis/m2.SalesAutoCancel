<?php
/**
 * Copyright © Eriocnemis, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Eriocnemis\SalesAutoCancel\Model\Order;

use Magento\Framework\Bulk\BulkManagementInterface;
use Magento\Framework\Bulk\OperationInterface;
use Magento\Framework\DataObject\IdentityGeneratorInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Api\Data\OrderInterface;

/**
 * Schedule bulk cancel of orders
 */
class ScheduleBulk
{
    /**
     * Bulk management
     *
     * @var BulkManagementInterface
     */
    private $bulkManagement;

    /**
     * Identity generator
     *
     * @var IdentityGeneratorInterface
     */
    private $identityService;

    /**
     * Operation builder
     *
     * @var OperationBuilder
     */
    private $operationBuilder;

    /**
     * Initialize schedule bulk
     *
     * @param BulkManagementInterface $bulkManagement
     * @param IdentityGeneratorInterface $identityService
     * @param OperationBuilder $operationBuilder
     */
    public function __construct(
        BulkManagementInterface $bulkManagement,
        IdentityGeneratorInterface $identityService,
        OperationBuilder $operationBuilder
    ) {
        $this->bulkManagement = $bulkManagement;
        $this->identityService = $identityService;
        $this->operationBuilder = $operationBuilder;
    }

    /**
     * Schedule new bulk
     *
     * @param OrderInterface[] $orders
     * @throws LocalizedException
     * @return void
     */
    public function execute(array $orders)
    {
        if (0 == count($orders)) {
            return;
        }

        $bulkUuid = $this->identityService->generateId();
        $operations = $this->getBulkOperations($orders, $bulkUuid);
        $description = __('Automatic cancellation of orders with an expired payment period.');

        $result = $this->bulkManagement->scheduleBulk($bulkUuid, $operations, $description);
        if (!$result) {
            throw new LocalizedException(
                __('Something went wrong while processing the request.')
            );
        }
    }

    /**
     * Retrieve bulk operations
     *
     * @param OrderInterface[] $orders
     * @param string $bulkUuid
     * @return OperationInterface[]
     */
    private function getBulkOperations(array $orders, $bulkUuid)
    {
        $operations = [];
        foreach ($orders as $order) {
            $operations[] = $this->operationBuilder->build(
                $bulkUuid,
                'eriocnemis.salesautocancel.order',
                ['order_id' => $order->getEntityId()]
            );
        }
        return $operations;
    }
}
