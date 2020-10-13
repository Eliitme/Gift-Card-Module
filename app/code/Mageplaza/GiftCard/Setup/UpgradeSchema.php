<?php


namespace Mageplaza\GiftCard\Setup;


use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Adapter\Pdo\Mysql;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class UpgradeSchema implements \Magento\Framework\Setup\UpgradeSchemaInterface
{

    /**
     * @inheritDoc
     * @throws \Zend_Db_Exception
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        // TODO: Implement upgrade() method.
        $installer = $setup;
        $installer->startSetup();


        if(version_compare($context->getVersion(), '2.0.0', '<')){

            if(!$installer->tableExists('mageplaza_giftcard_history')){
                $table = $installer->getConnection()->newTable(
                    $installer->getTable('mageplaza_giftcard_history')
                )
                    ->addColumn(
                        'history_id',
                        Table::TYPE_INTEGER,
                        null,
                        [
                            'identity' => true,
                            'primary' => true,
                            'nullable' => false,
                            'unsigned' => true
                        ],
                        'Gift Card History ID'
                    )
                    ->addColumn(
                        'giftcard_id',
                        Table::TYPE_INTEGER,
                        10,
                        [
                            'unsigned' => true,
                            'nullable' => false
                        ],
                        'Gift Card ID'
                    )
                    ->addColumn(
                        'customer_id',
                        Table::TYPE_INTEGER,
                        10,
                        [
                            'nullable' => false,
                            'unsigned' => true
                        ],
                        'Customer ID'
                    )
                    ->addColumn(
                        'amount',
                        Table::TYPE_DECIMAL,
                        '12,4',
                        [
                            'nullable' => true,
                            'default' => 0
                        ],
                        'Amount'
                    )
                    ->addColumn(
                        'action',
                        Table::TYPE_TEXT,
                        255,
                        [
                            'nullable' => false,
                        ],
                        'Action'
                    )
                    ->addColumn(
                        'action_time',
                        Table::TYPE_TIMESTAMP,
                        null,
                        [
                            'nullable' => false,
                            'default' => Table::TIMESTAMP_INIT
                        ],
                        'Action Time'
                    )
                    ->addForeignKey(
                        $installer->getFkName(
                            'mageplaza_giftcard_history',
                            'giftcard_id',
                            'mageplaza_giftcard_code',
                            'giftcard_id'
                        ),
                        'giftcard_id',
                        $installer->getTable('mageplaza_giftcard_code'),
                        'giftcard_id',
                        Table::ACTION_CASCADE
                    )
                    ->addForeignKey(
                        $installer->getFkName(
                            'mageplaza_giftcard_history',
                            'customer_id',
                            'customer_entity',
                            'entity_id'
                        ),
                        'customer_id',
                        $installer->getTable('customer_entity'),
                        'entity_id',
                        Table::ACTION_CASCADE
                    )
                    ->setComment('Gift Card History Table');

                $installer->getConnection()->createTable($table);
            }

            if(!$installer->tableExists('giftcard_customer_balance')){
                $table = $installer->getConnection()->newTable(
                    $installer->getTable('giftcard_customer_balance')
                )
                    ->addColumn(
                        'customer_id',
                        Table::TYPE_INTEGER,
                        10,
                        [
                            'nullable' => false,
                            'unsigned' => true
                        ],
                        'Customer ID'
                    )
                    ->addColumn(
                        'balance',
                        Table::TYPE_DECIMAL,
                        '12,4',
                        [
                            'nullable' => true,
                            'default' => 0
                        ],
                        'Balance'
                    )
                    ->addForeignKey(
                        $installer->getFkName(
                            'giftcard_customer_balance',
                            'customer_id',
                            'customer_entity',
                            'entity_id'
                        ),
                        'customer_id',
                        $installer->getTable('customer_entity'),
                        'entity_id',
                        Table::ACTION_CASCADE
                    )
                    ->setComment('Gift Card Customer Balance');

                $installer->getConnection()->createTable($table);
            }
        }

        if(version_compare($context->getVersion(), '2.1.0', '<')) {
            if($installer->getConnection()->isTableExists('quote')) {
                $installer->getConnection()->addColumn(
                    'quote',
                    'giftcard_code',
                    [
                        'type' => Table::TYPE_TEXT,
                        'size' => 255,
                        'nullable' => true,
                        'comment' => 'Gift Card Code'
                    ]);
                $installer->getConnection()->addColumn(
                    'quote',
                    'giftcard_base_discount',
                    [
                        'type' => Table::TYPE_DECIMAL,
                        'size' => null,
                        'length' => '12,4',
                        'nullable' => true,
                        'comment' => 'Gift Card Base Discount'
                    ]);
                $installer->getConnection()->addColumn(
                    'quote',
                    'giftcard_discount',
                    [
                        'type' => Table::TYPE_DECIMAL,
                        'size' => null,
                        'length' => '12,4',
                        'nullable' => true,
                        'comment' => 'Gift Card Discount'
                    ]);
            }

        }

        $installer->endSetup();

    }
}
