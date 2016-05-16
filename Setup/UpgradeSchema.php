<?php
namespace Shockwavemk\Mail\Base\Setup;

use Magento\Framework\Setup\SetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    public function upgrade(SchemaSetupInterface $setup,
                            ModuleContextInterface $context){
        $setup->startSetup();
        if (version_compare($context->getVersion(), '1.0.1') < 0) {
            $this->createMailMainTable($setup);
            $this->createMailLinkTable($setup);
            $this->createMailLinkTypeTable($setup);
        }

        $setup->endSetup();
    }

    /**
     * @param $installer
     */
    protected function createMailMainTable(SetupInterface $installer)
    {
        $table = $installer->getConnection()->newTable(
            $installer->getTable('shockwavemk_mail_entity')
        )->addColumn(
            'entity_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Mail ID'
        )->addColumn(
            'customer_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => '0'],
            'Customer ID'
        )->addColumn(
            'template_identifier',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Template Identifier'
        )->addColumn(
            'subject',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Subject'
        )->addColumn(
            'template_model',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Template Model'
        )->addColumn(
            'store_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => '0'],
            'Store ID'
        )->addColumn(
            'sent',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => '0'],
            'Successful sent (0 or 1)'
        )->addColumn(
            'sent_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            [],
            'Sent at date'
        )->addColumn(
            'recipients',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            \Magento\Framework\DB\Ddl\Table::MAX_TEXT_SIZE,
            ['nullable' => false],
            'Recipients'
        )->addColumn(
            'language_code',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Language Code'
        )->addColumn(
            'sender_mail',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Sender Mail'
        )->addColumn(
            'vars',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            \Magento\Framework\DB\Ddl\Table::MAX_TEXT_SIZE,
            ['nullable' => false],
            'Vars'
        )->addColumn(
            'options',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            \Magento\Framework\DB\Ddl\Table::MAX_TEXT_SIZE,
            ['nullable' => false],
            'Options'
        )->addColumn(
            'recipient_variables',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            \Magento\Framework\DB\Ddl\Table::MAX_TEXT_SIZE,
            ['nullable' => false],
            'Recipient variables'
        )->addColumn(
            'delivery_time',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            [],
            'Delivery time'
        )->addColumn(
            'tags',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            \Magento\Framework\DB\Ddl\Table::MAX_TEXT_SIZE,
            ['nullable' => false],
            'Tags'
        )->addColumn(
            'test_mode',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => '0'],
            'Test mode'
        )->addColumn(
            'tracking_enabled',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => '0'],
            'Tracking enabled'
        )->addColumn(
            'tracking_clicks_enabled',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => '0'],
            'Tracking Clicks Enabled'
        )->addColumn(
            'tracking_opens_enabled',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => '0'],
            'Tracking Opens Enabled'
        )->addColumn(
            'transport_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Transport Id'
        )->addIndex(
            $installer->getIdxName('mail', 'sent'),
            'sent'
        )->addIndex(
            $installer->getIdxName('mail', 'sent_at'),
            'sent_at'
        )->addIndex(
            $installer->getIdxName('mail', 'customer_id'),
            'customer_id'
        )->setComment(
            'Mail main table'
        );
        $installer->getConnection()->createTable($table);
    }

    /**
     * @param $installer
     */
    protected function createMailLinkTable(SetupInterface $installer)
    {
        $table = $installer->getConnection()->newTable(
            $installer->getTable('shockwavemk_mail_entity_link')
        )->addColumn(
            'entity_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Mail ID'
        )->addColumn(
            'type_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => '0'],
            'Linked object type'
        )->addColumn(
            'link_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => '0'],
            'Linked object ID'
        )->addIndex(
            $installer->getIdxName('mail', 'link_id'),
            'link_id'
        )->addIndex(
            $installer->getIdxName('mail', 'type_id'),
            'type_id'
        )->setComment(
            'Mail link table'
        );
        $installer->getConnection()->createTable($table);
    }

    /**
     * @param $installer
     */
    protected function createMailLinkTypeTable(SetupInterface $installer)
    {
        $table = $installer->getConnection()->newTable(
            $installer->getTable('shockwavemk_mail_link_type')
        )->addColumn(
            'entity_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Mail Type ID'
        )->addColumn(
            'type_class',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            \Magento\Framework\DB\Ddl\Table::MAX_TEXT_SIZE,
            ['nullable' => false],
            'Decoupled linked object class'
        )->setComment(
            'Mail link type table'
        );
        $installer->getConnection()->createTable($table);
    }
}