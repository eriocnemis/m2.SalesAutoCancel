<?php
/**
 * Copyright © Eriocnemis, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Eriocnemis\SalesAutoCancel\Model\Order;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Stdlib\DateTime;
use Magento\Framework\Stdlib\DateTime\DateTime as Time;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;
use Eriocnemis\SalesAutoCancel\Helper\Data as Helper;

/**
 * Order manager
 */
class Manager implements ManagerInterface
{
    /**
     * Search criteria builder
     *
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * Order repository
     *
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * DateTime
     *
     * @var DateTime
     */
    private $dateTime;

    /**
     * Date
     *
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    private $date;

    /**
     * Helper
     *
     * @var Helper
     */
    private $helper;

    /**
     * Initialize manager
     *
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param OrderRepositoryInterface $orderRepository
     * @param DateTime $dateTime
     * @param Time $time
     * @param Helper $helper
     */
    public function __construct(
        SearchCriteriaBuilder $searchCriteriaBuilder,
        OrderRepositoryInterface $orderRepository,
        DateTime $dateTime,
        Time $time,
        Helper $helper
    ) {
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->orderRepository = $orderRepository;
        $this->dateTime = $dateTime;
        $this->date = $time;
        $this->helper = $helper;
    }

    /**
     * Retrieve and populate search criteria
     *
     * @param int $storeId
     * @return SearchCriteriaInterface
     */
    private function getSearchCriteria($storeId)
    {
        $searchCriteria = $this->searchCriteriaBuilder->addFilter(
            OrderInterface::STATE,
            $this->helper->getStatuses($storeId),
            'in'
        )->addFilter(
            OrderInterface::STORE_ID,
            $storeId
        )->addFilter(
            OrderInterface::CREATED_AT,
            $this->dateTime->formatDate(
                $this->date->gmtTimestamp() - 3600 * 24 * (int)$this->helper->getAge($storeId)
            ),
            'lteq'
        )->create();

        return $searchCriteria;
    }

    /**
     * Retrieve expired orders from specific store
     *
     * @param int $storeId
     * @return OrderInterface[]
     */
    public function getOrderList($storeId)
    {
        return $this->orderRepository->getList(
            $this->getSearchCriteria($storeId)
        )->getItems();
    }

    /**
     * Order cancel
     *
     * @param int $orderId
     * @return void
     */
    public function cancel($orderId)
    {
        $order = $this->orderRepository->get($orderId);

        $order->setState(Order::STATE_CANCELED);
        $order->addCommentToStatusHistory(
            __(
                'The order is canceled automatically due to the lack of information about payment for the order within %1 days.',
                $this->helper->getAge($order->getStoreId())
            ),
            Order::STATE_CANCELED,
            false // visible on front
        )->setIsCustomerNotified(false);

        $this->orderRepository->save($order);
    }
}
