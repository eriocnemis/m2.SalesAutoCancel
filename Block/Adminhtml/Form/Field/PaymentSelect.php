<?php
/**
 * Copyright © Eriocnemis, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Eriocnemis\SalesAutoCancel\Block\Adminhtml\Form\Field;

use Magento\Framework\View\Element\Context;
use Magento\Framework\View\Element\Html\Select;
use Magento\Payment\Helper\Data as PaymentHelper;

/**
 * Payment select element
 */
class PaymentSelect extends Select
{
    /**
     * @var PaymentHelper
     */
    private $paymentHelper;

    /**
     * Initialize element
     *
     * @param Context $context
     * @param PaymentHelper $paymentHelper
     * @param mixed[] $data
     */
    public function __construct(
        Context $context,
        PaymentHelper $paymentHelper,
        array $data = []
    ) {
        $this->paymentHelper = $paymentHelper;

        parent::__construct(
            $context,
            $data
        );
    }

    /**
     * Set element name
     *
     * @param string $value
     * @return $this
     */
    public function setInputName($value)
    {
        return $this->setName($value);
    }

    /**
     * Set element id
     *
     * @param string $value
     * @return $this
     */
    public function setInputId($value)
    {
        return $this->setId($value);
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    public function _toHtml()
    {
        if (!$this->getOptions()) {
            $options = $this->paymentHelper->getPaymentMethodList(true, true);
            $this->setOptions($options);
        }
        return parent::_toHtml();
    }
}
