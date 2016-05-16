<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Shockwavemk\Mail\Base\Block\Adminhtml\Edit\Tab\Mail\Grid\Renderer;

/**
 * Adminhtml customers wishlist grid item renderer for item visibility
 */
class Recipients extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * Render the description of given row.
     *
     * @param \Magento\Framework\DataObject $row
     * @return string
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        $recipients = json_decode($row->getData($this->getColumn()->getIndex()), true);
        if(!empty($recipients))
        {
            $recipientsString = htmlspecialchars(implode(', ', $recipients));
            if(strlen($recipientsString) < 15)
            {
                return $recipientsString;
            }

            return substr($recipientsString, 0, 12) .  '...';
        }

        return '';
    }
}
