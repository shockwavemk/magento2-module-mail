<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

// @codingStandardsIgnoreFile

/**
 * Adminhtml reviews grid
 *
 * @method int getProductId() getProductId()
 * @method \Magento\Review\Block\Adminhtml\Grid setProductId() setProductId(int $productId)
 * @method int getCustomerId() getCustomerId()
 * @method \Magento\Review\Block\Adminhtml\Grid setCustomerId() setCustomerId(int $customerId)
 * @method \Magento\Review\Block\Adminhtml\Grid setMassactionIdFieldOnlyIndexValue() setMassactionIdFieldOnlyIndexValue(bool $onlyIndex)
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Shockwavemk\Mail\Base\Block\Adminhtml;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * Review action pager
     *
     * @var \Magento\Review\Helper\Action\Pager
     */
    protected $_reviewActionPager = null;

    /**
     * Review data
     *
     * @var \Magento\Review\Helper\Data
     */
    protected $_reviewData = null;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * Review collection model factory
     *
     * @var \Magento\Review\Model\ResourceModel\Review\Product\CollectionFactory
     */
    protected $_productsFactory;

    /**
     * Review model factory
     *
     * @var \Magento\Review\Model\ReviewFactory
     */
    protected $_reviewFactory;
    /**
     * @var \Shockwavemk\Mail\Base\Model\ResourceModel\Mail\Collection
     */
    private $collection;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Shockwavemk\Mail\Base\Model\ResourceModel\Mail\Collection $collection
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Framework\Registry $coreRegistry,
        \Shockwavemk\Mail\Base\Model\ResourceModel\Mail\Collection $collection,
        array $data = []
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_collection = $collection;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * Initialize grid
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('mailGrid');
        $this->setDefaultSort('sent_at');
    }
}
