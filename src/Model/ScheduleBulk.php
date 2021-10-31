<?php
/**
 * Copyright © Eriocnemis, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Eriocnemis\SalesAutoCancel\Model;

use Magento\Framework\Bulk\BulkManagementInterface;
use Magento\Framework\Bulk\OperationInterface;
use Magento\Framework\DataObject\IdentityGeneratorInterface;
use Magento\Framework\Exception\LocalizedException;
use Eriocnemis\SalesAutoCancel\Api\ScheduleBulkInterface;

/**
 * Schedule bulk cancel of orders
 */
class ScheduleBulk implements ScheduleBulkInterface
{
    /**
     * @var BulkManagementInterface
     */
    private $bulkManagement;

    /**
     * @var IdentityGeneratorInterface
     */
    private $identityService;

    /**
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
     * @param int[] $orderIds
     * @param int $age
     * @throws LocalizedException
     * @return void
     */
    public function execute(array $orderIds, int $age): void
    {
        if (0 == count($orderIds)) {
            return;
        }

        $bulkUuid = $this->identityService->generateId();
        $operations = $this->getBulkOperations($orderIds, $age, $bulkUuid);
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
     * @param int[] $orderIds
     * @param int $age
     * @param string $bulkUuid
     * @return OperationInterface[]
     */
    private function getBulkOperations(array $orderIds, int $age, $bulkUuid)
    {
        $operations = [];
        foreach ($orderIds as $orderId) {
            $operations[] = $this->operationBuilder->build(
                $bulkUuid,
                'eriocnemis.salesautocancel.order',
                ['order_id' => $orderId, 'age' => $age]
            );
        }
        return $operations;
    }
}
