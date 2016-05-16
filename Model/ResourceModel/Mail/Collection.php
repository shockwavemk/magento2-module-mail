<?php
/**
 * Copyright 2016 Shockwave-Design - J. & M. Kramer, all rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Shockwavemk\Mail\Base\Model\ResourceModel\Mail;

use Magento\Framework\App\RequestInterface;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_selectedCustomerId;

    protected $_objectManager;

    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
    }

    /**
     * Initialize resource
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Shockwavemk\Mail\Base\Model\Mail', 'Shockwavemk\Mail\Base\Model\ResourceModel\Mail');
    }

    /**
     * Init collection select
     *
     * @return $this
     */
    protected function _initSelect()
    {
        $request = $this->_objectManager->get('Magento\Framework\App\RequestInterface');
        $this->getSelect()->from(['main_table' => $this->getMainTable()]);

        if(!empty($customerId = $request->getParam('id'))) {
            $this->getSelect()->where('customer_id = ?', $customerId);
        }

        return $this;
    }
}
