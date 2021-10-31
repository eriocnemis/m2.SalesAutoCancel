<?php
/**
 * Copyright © Eriocnemis, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Eriocnemis\SalesAutoCancel\Block\Adminhtml\Form\Field;

use Magento\Framework\DataObject;
use Magento\Framework\View\Element\BlockInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;

/**
 * Payments block
 */
class Payments extends AbstractFieldArray
{
    /**
     * @var BlockInterface
     */
    private $paymentSelect;

    /**
     * Prepare to render
     *
     * @return void
     */
    protected function _prepareToRender()
    {
        $this->addColumn(
            'method',
            [
                'label' => __('Method'),
                'renderer' => $this->getPaymentSelectRenderer()
            ]
        );

        $this->addColumn(
            'age',
            [
                'label' => __('Order Age, days'),
                'class' => 'required-entry'
            ]
        );

        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add');
    }

    /**
     * Prepare existing row data object
     *
     * @param DataObject $row
     * @throws LocalizedException
     */
    protected function _prepareArrayRow(DataObject $row): void
    {
        $options = [];
        $method = $row->getMethod();
        if (null !== $method) {
            $optionId = $this->getPaymentSelectRenderer()->calcOptionHash($method);
            $options['option_' . $optionId] = 'selected="selected"';
        }
        $row->setData('option_extra_attrs', $options);
    }

    /**
     * Retrieve payment select element
     *
     * @return BlockInterface
     * @throws LocalizedException
     */
    private function getPaymentSelectRenderer()
    {
        if (null === $this->paymentSelect) {
            $this->paymentSelect = $this->getLayout()->createBlock(
                PaymentSelect::class,
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );
        }
        return $this->paymentSelect;
    }
}
