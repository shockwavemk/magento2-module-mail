<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/**
 * Manage Newsletter Template Controller
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Shockwavemk\Mail\Base\Controller\Adminhtml;

abstract class Mail extends \Magento\Backend\App\Action
{
    /**
     * Check is allowed access
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return true; // $this->_authorization->isAllowed('Magento_Newsletter::template');
    }
}
