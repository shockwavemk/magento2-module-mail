<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Shockwavemk\Mail\Base\Controller\Adminhtml\Mail;

class Overview extends \Shockwavemk\Mail\Base\Controller\Adminhtml\Mail
{
    protected $_mail;
    /**
     * Preview Newsletter template
     *
     * @return void|$this
     */
    public function execute()
    {
        $this->_view->loadLayout();
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Transactional mails'));
        $this->_view->renderLayout();
    }
}
