<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Shockwavemk\Mail\Base\Block\Adminhtml\Edit\Tab\Mail\Grid\Renderer;

/**
 * Adminhtml customers wishlist grid item renderer for item visibility
 */
class Customer extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * Render the description of given row.
     *
     * @param \Magento\Framework\DataObject $row
     * @return string
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        $customerId = (int)$row->getData($this->getColumn()->getIndex());
        if($customerId > 0) {
            return '<a href="' . $this->getUrl('customer/index/edit', array('id' => $customerId)) . '" target="_blank">' . $customerId . '</a>';
        }

        return nl2br(htmlspecialchars($row->getData($this->getColumn()->getIndex())));
    }
}
