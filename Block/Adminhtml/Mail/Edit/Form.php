<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Shockwavemk\Mail\Base\Block\Adminhtml\Mail\Edit;

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
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\ObjectManagerInterface $manager,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }
}
