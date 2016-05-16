<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Shockwavemk\Mail\Base\Block\Adminhtml\Mail\Send;

use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Controller\RegistryConstants;
use Magento\Customer\Model\Address\Mapper;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

/**
 * Adminhtml customer view personal information sales block.
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Form extends \Magento\Backend\Block\Widget\Form
{
    /** @var \Shockwavemk\Mail\Base\Model\Mail _mail */
    protected $_mail;

    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\ObjectManagerInterface $manager,
        array $data = []
    ) {
        $this->_request = $context->getRequest();
        $mailId = $this->_request->getParam('id');
        $this->_mail = $manager->get('\Shockwavemk\Mail\Base\Model\Mail');
        $this->_mail->load($mailId);

        parent::__construct($context, $data);
    }

    public function getMail()
    {
        return $this->_mail;
    }
}
