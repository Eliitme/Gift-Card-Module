<?php


namespace Mageplaza\GiftCard\Setup;


use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Adapter\Pdo\Mysql;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements \Magento\Framework\Setup\InstallSchemaInterface
{

    /**
     * @inheritDoc
     * @throws \Zend_Db_Exception
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        // TODO: Implement install() method.
        $installer = $setup;

        $installer->startSetup();

        if(!$installer->tableExists('mageplaza_giftcard_code')){
            $table = $installer->getConnection()->newTable(
                $installer->getTable('mageplaza_giftcard_code')
            )
                ->addColumn(
                    'giftcard_id',
                    Table::TYPE_INTEGER,
                    null,
                    [
                        'identity' => true,
                        'nullable' => false,
                        'primary' => true,
                        'unsigned' => true
                    ],
                    'Giftcard ID'
                )
                ->addColumn(
                    'code',
                    Table::TYPE_TEXT,
                    255,
                    [
                        'nullable' => false
                    ],
                    'Code'
                )
                ->addColumn(
                    'balance',
                    Table::TYPE_DECIMAL,
                    '12,4',
                    [],
                    'Giftcard Value'
                )
                ->addColumn(
                    'amount_used',
                    Table::TYPE_DECIMAL,
                    '12,4',
                    [],
                    'Amount Used'
                )
                ->addColumn(
                    'created_from',
                    Table::TYPE_TEXT,
                    255,
                    [],
                    'Create From'
                )
                ->addColumn(
                    'created_at',
                    Table::TYPE_TIMESTAMP,
                    null,
                    [
                        'nullable' => false,
                        'default' => Table::TIMESTAMP_INIT
                    ],
                    'Created At'
                )
                ->setComment('Giftcard Table');
            $installer->getConnection()->createTable($table);

            $installer->getConnection()->addIndex(
                $installer->getTable('mageplaza_giftcard_code'),
                $setup->getIdxName(
                    $installer->getTable('mageplaza_giftcard_code'),
                    [
                        'code', 'created_from'
                    ],
                    AdapterInterface::INDEX_TYPE_FULLTEXT
                ),
                [
                    'code', 'created_from'
                ],
                AdapterInterface::INDEX_TYPE_FULLTEXT
            );
        }


        $installer->endSetup();
    }
}
