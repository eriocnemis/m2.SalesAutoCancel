<?php
/**
 * Copyright © Eriocnemis, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Eriocnemis\SalesAutoCancel\Model;

use Magento\Framework\MessageQueue\MergerInterface;
use Magento\Framework\MessageQueue\MergedMessageInterfaceFactory;
use Magento\AsynchronousOperations\Api\Data\OperationListInterfaceFactory;

/**
 * Merges messages from the operations queue
 */
class Merger implements MergerInterface
{
    /**
     * Operation list factory
     *
     * @var OperationListInterfaceFactory
     */
    private $operationListFactory;

    /**
     * Merged message factory
     *
     * @var MergedMessageInterfaceFactory
     */
    private $mergedMessageFactory;

    /**
     * Initialize merged
     *
     * @param OperationListInterfaceFactory $operationListFactory
     * @param MergedMessageInterfaceFactory $mergedMessageFactory
     */
    public function __construct(
        OperationListInterfaceFactory $operationListFactory,
        MergedMessageInterfaceFactory $mergedMessageFactory
    ) {
        $this->operationListFactory = $operationListFactory;
        $this->mergedMessageFactory = $mergedMessageFactory;
    }

    /**
     * Merge process
     *
     * @param mixed[] $messages
     * @return mixed[]
     */
    public function merge(array $messages): array
    {
        $result = [];
        foreach ($messages as $topicName => $topicMessages) {
            $operationList = $this->operationListFactory->create(['items' => $topicMessages]);
            $messagesIds = array_keys($topicMessages);
            $result[$topicName][] = $this->mergedMessageFactory->create(
                [
                    'mergedMessage' => $operationList,
                    'originalMessagesIds' => $messagesIds
                ]
            );
        }
        return $result;
    }
}
