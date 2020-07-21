<?php
/**
 * Copyright © Eriocnemis, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Eriocnemis\SalesAutoCancel\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;
use Magento\Sales\Model\Order\Config;
use Magento\Sales\Model\Order;

/**
 * Order states source
 */
class States implements ArrayInterface
{
    /**
     * Order config
     *
     * @var Config
     */
    private $orderConfig;

    /**
     * Order atates
     *
     * @var string[]
     */
    private $states = [
        Order::STATE_NEW,
        Order::STATE_PENDING_PAYMENT
    ];

    /**
     * Initialize source
     *
     * @param Config $orderConfig
     */
    public function __construct(
        Config $orderConfig
    ) {
        $this->orderConfig = $orderConfig;
    }

    /**
     * Retrieve options as array
     *
     * @return mixed[]
     */
    public function toOptionArray()
    {
        $options = [];
        $states = $this->orderConfig->getStates();
        foreach ($this->states as $code) {
            $options[] = ['value' => $code, 'label' => $states[$code]];
        }
        return $options;
    }
}
