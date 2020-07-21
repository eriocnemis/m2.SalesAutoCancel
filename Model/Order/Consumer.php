<?php
/**
 * Copyright © Eriocnemis, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Eriocnemis\SalesAutoCancel\Model\Order;

use Magento\Framework\EntityManager\EntityManager;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\AsynchronousOperations\Api\Data\OperationListInterface;
use Magento\AsynchronousOperations\Api\Data\OperationInterface;
use Eriocnemis\SalesAutoCancel\Model\Order\ManagerInterface;

/**
 * Consumer for auto cancel message
 */
class Consumer
{
    /**
     * Order manager
     *
     * @var ManagerInterface
     */
    private $manager;

    /**
     * Entity manager
     *
     * @var EntityManager
     */
    private $entityManager;

    /**
     * Serializer
     *
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * Initialize consumer
     *
     * @param ManagerInterface $manager
     * @param SerializerInterface $serializer
     * @param EntityManager $entityManager
     */
    public function __construct(
        ManagerInterface $manager,
        SerializerInterface $serializer,
        EntityManager $entityManager
    ) {
        $this->manager = $manager;
        $this->serializer = $serializer;
        $this->entityManager = $entityManager;
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
            $operation->setResultMessage(null);
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
        $operationStatus = OperationInterface::STATUS_TYPE_COMPLETE;
        $serializedData = $operation->getSerializedData();
        $data = $this->serializer->unserialize($serializedData);

        try {
            $this->manager->cancel($data['order_id']);
        } catch (\Exception $e) {
            $operationStatus = OperationInterface::STATUS_TYPE_RETRIABLY_FAILED;
        }

        return $operationStatus;
    }
}
