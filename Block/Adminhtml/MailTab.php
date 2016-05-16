<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Shockwavemk\Mail\Base\Block\Adminhtml;

use Magento\Framework\Registry;
use Magento\Backend\Block\Template\Context;
use Magento\Ui\Component\Layout\Tabs\TabWrapper;
use Magento\Ui\Component\Layout\Tabs\TabInterface;
use Magento\Customer\Controller\RegistryConstants;

/**
 * Class WishlistTab
 */
class MailTab extends TabWrapper implements TabInterface
{
    /**
     * Core registry
     *
     * @var Registry
     */
    protected $coreRegistry = null;

    /**
     * @var bool
     */
    protected $isAjaxLoaded = true;

    /**
     * Constructor
     *
     * @param Context $context
     * @param Registry $registry
     * @param array $data
     */
    public function __construct(Context $context, Registry $registry, array $data = [])
    {
        $this->coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * @inheritdoc
     */
    public function canShowTab()
    {
        return $this->coreRegistry->registry(RegistryConstants::CURRENT_CUSTOMER_ID);
    }

    /**
     * Return Tab label
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Mail');
    }

    /**
     * Return URL link to Tab content
     *
     * @return string
     */
    public function getTabUrl()
    {
        return $this->getUrl('customer/*/mail', ['_current' => true]);
    }
}
