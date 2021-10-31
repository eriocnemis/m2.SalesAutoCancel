<?php
/**
 * Copyright © Eriocnemis, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Eriocnemis\SalesAutoCancel\Model;

use Psr\Log\LoggerInterface;
use Magento\Framework\EntityManager\EntityManager;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\AsynchronousOperations\Api\Data\OperationListInterface;
use Magento\AsynchronousOperations\Api\Data\OperationInterface;
use Eriocnemis\SalesAutoCancel\Api\CancelOrderInterface;

/**
 * Consumer for auto cancel message
 */
class Consumer
{
    /**
     * @var CancelOrderInterface
     */
    private $cancelOrder;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Initialize consumer
     *
     * @param CancelOrderInterface $cancelOrder
     * @param SerializerInterface $serializer
     * @param EntityManager $entityManager
     * @param LoggerInterface $logger
     */
    public function __construct(
        CancelOrderInterface $cancelOrder,
        SerializerInterface $serializer,
        EntityManager $entityManager,
        LoggerInterface $logger
    ) {
        $this->cancelOrder = $cancelOrder;
        $this->serializer = $serializer;
        $this->entityManager = $entityManager;
        $this->logger = $logger;
    }

    /**
     * Consumer process
     *
     * @param OperationListInterface $operationList
     * @return void
     */
    public function process(OperationListInterface $operationList)
    {
        foreach ($operationList->getItems() as $operation) {
            $status = $this->processOperation($operation);
            $operation->setStatus($status);
        }
        $this->entityManager->save($operationList);
    }

    /**
     * Process bulk operation
     *
     * @param OperationInterface $operation
     * @return int
     */
    private function processOperation(OperationInterface $operation)
    {
        $operationStatus = OperationInterface::STATUS_TYPE_RETRIABLY_FAILED;
        $serializedData = $operation->getSerializedData();
        $data = $this->serializer->unserialize($serializedData);

        if (is_array($data) && isset($data['order_id']) && isset($data['age'])) {
            try {
                $this->cancelOrder->execute($data['order_id'], $data['age']);
                $operationStatus = OperationInterface::STATUS_TYPE_COMPLETE;
            } catch (\Exception $e) {
                $this->logger->critical($e->getMessage());
            }
        }

        return $operationStatus;
    }
}
