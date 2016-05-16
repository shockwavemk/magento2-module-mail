<?php
/**
 * Copyright 2016 Shockwave-Design - J. & M. Kramer, all rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Shockwavemk\Mail\Base\Block\Adminhtml\Widget\Grid\Column\Filter;

class Text extends \Magento\Backend\Block\Widget\Grid\Column\Filter\Text
{
    /**
     * Override abstract method
     *
     * @return array
     */
    public function getCondition()
    {
        return ['like' => $this->getValue()];
    }
}
