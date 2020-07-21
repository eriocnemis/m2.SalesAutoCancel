<?php
/**
 * Copyright © Eriocnemis, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Eriocnemis\SalesAutoCancel\Model\Order;

use Magento\AsynchronousOperations\Api\Data\OperationInterface;
use Magento\AsynchronousOperations\Api\Data\OperationInterfaceFactory;
use Magento\Framework\Serialize\SerializerInterface;

/**
 * Bulk operation builder
 */
class OperationBuilder
{
    /**
     * Serializer
     *
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * Operation factory
     *
     * @var OperationInterfaceFactory
     */
    private $operationFactory;

    /**
     * Initialize builder
     *
     * @param SerializerInterface $serializer
     * @param OperationInterfaceFactory $operationFactory
     */
    public function __construct(
        SerializerInterface $serializer,
        OperationInterfaceFactory $operationFactory
    ) {
        $this->serializer = $serializer;
        $this->operationFactory = $operationFactory;
    }

    /**
     * Build bulk operation
     *
     * @param string $bulkId
     * @param string $queueTopic
     * @param string|mixed $operationData
     * @return OperationInterface
     */
    public function build(string $bulkId, string $queueTopic, $operationData)
    {
        $serializedData = $this->serializer->serialize($operationData);
        $data = [
            'data' => [
                OperationInterface::BULK_ID => $bulkId,
                OperationInterface::TOPIC_NAME => $queueTopic,
                OperationInterface::SERIALIZED_DATA => $serializedData,
                OperationInterface::STATUS => OperationInterface::STATUS_TYPE_OPEN,
            ]
        ];
        return $this->operationFactory->create($data);
    }
}
