<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Shockwavemk\Mail\Base\Block\Adminhtml;

/**
 * Ratings grid
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class MailOverview extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml';
        $this->_blockGroup = 'Shockwavemk_Mail_Base';
        $this->_headerText = __('Review Transactional Mails');

        parent::_construct();
    }

    /**
     * Create "New" button
     *
     * @return void
     */
    protected function _addNewButton()
    {
        // do nothing
    }
}
