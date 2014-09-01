<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_GoogleShopping
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

/**
 * Drop foreign keys
 */
$installer->getConnection()->dropForeignKey(
    $installer->getTable('googleshopping/types'),
    'GOOGLESHOPPING_TYPES_ATTRIBUTE_SET_ID'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('googleshopping/items'),
    'GOOGLESHOPPING_ITEMS_PRODUCT_ID'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('googleshopping/items'),
    'GOOGLESHOPPING_ITEMS_STORE_ID'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('googleshopping/attributes'),
    'GOOGLESHOPPING_ATTRIBUTES_ATTRIBUTE_ID'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('googleshopping/attributes'),
    'GOOGLESHOPPING_ATTRIBUTES_TYPE_ID'
);

/**
 * Drop indexes
 */
$installer->getConnection()->dropIndex(
    $installer->getTable('googleshopping/types'),
    'GOOGLESHOPPING_TYPES_ATTRIBUTE_SET_COUNTRY'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('googleshopping/items'),
    'GOOGLESHOPPING_ITEMS_PRODUCT_STORE_ID'
);

/**
 * Change columns
 */
$tables = array(
    $installer->getTable('googleshopping/types') => array(
        'columns' => array(
            'type_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Type ID',
            ),
            'attribute_set_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Attribute Set Id',
            ),
            'target_country' => array(
                'type'       => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'     => 2,
                'nullable'   => false,
                'default'    => 'US',
                'comment'    => 'Target country',
            ),
        ),
        'comment' => 'Google Content Item Types link Attribute Sets'
    ),
    $installer->getTable('googleshopping/items') => array(
        'columns' => array(
            'item_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'nullable'  => false,
                'unsigned'  => true,
                'primary'   => true,
                'comment'   => 'Item Id',
            ),
            'type_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'nullable'  => false,
                'unsigned'  => true,
                'default'   => 0,
                'comment'   => 'Type Id',
            ),
            'product_id'    => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'nullable'  => false,
                'unsigned'  => true,
                'comment'   => 'Product Id',
            ),
            'gcontent_item_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'nullable'  => false,
                'comment'   => 'Google Content Item Id',
            ),
            'store_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'nullable'  => false,
                'unsigned'  => true,
                'comment'   => 'Store Id',
            ),
            'published' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DATETIME,
                'nullable'  => true,
                'comment'   => 'Published date',
            ),
            'expires' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DATETIME,
                'nullable'  => true,
                'comment'   => 'Expires date',
            ),
        ),
        'comment' => 'Google Content Items Products'
    ),
    $installer->getTable('googleshopping/attributes') => array(
        'columns' => array(
            'id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'length'    => 10,
                'identity'  => true,
                'nullable'  => false,
                'unsigned'  => true,
                'primary'   => true,
                'comment'   => 'Id',
            ),
            'attribute_id'  => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'nullable'  => false,
                'unsigned'  => true,
                'comment'   => 'Attribute Id',
            ),
            'gcontent_attribute' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'nullable'  => false,
                'comment'   => 'Google Content Attribute',
            ),
            'type_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'nullable'  => false,
                'unsigned'  => true,
                'comment'   => 'Type Id',
            ),
        ),
        'comment' => 'Google Content Attributes link Product Attributes'
    ),
);

$installer->getConnection()->modifyTables($tables);

/**
 * Add indexes
 */
$installer->getConnection()->addIndex(
    $this->getTable('googleshopping/types'),
    $installer->getIdxName(
        'googleshopping/types',
        array('attribute_set_id', 'target_country'),
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ),
    array('attribute_set_id', 'target_country'),
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);

$installer->getConnection()->addIndex(
    $this->getTable('googleshopping/items'),
    $installer->getIdxName('googleshopping/items', array('product_id', 'store_id')),
    array('product_id', 'store_id')
);

$installer->getConnection()->addIndex(
    $this->getTable('googleshopping/items'),
    $installer->getIdxName('googleshopping/items', array('product_id', 'store_id')),
    array('product_id', 'store_id')
);

/**
 * Add foreign keys
 */
$installer->getConnection()->addForeignKey(
    $installer->getFkName('googleshopping/types', 'attribute_set_id', 'eav/attribute_set', 'attribute_set_id'),
    $installer->getTable('googleshopping/types'),
    'attribute_set_id',
    $this->getTable('eav/attribute_set'),
    'attribute_set_id',
    Varien_Db_Ddl_Table::ACTION_CASCADE,
    Varien_Db_Ddl_Table::ACTION_NO_ACTION
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('googleshopping/items', 'product_id', 'catalog/product', 'entity_id'),
    $installer->getTable('googleshopping/items'),
    'product_id',
    $this->getTable('catalog/product'),
    'entity_id',
    Varien_Db_Ddl_Table::ACTION_CASCADE,
    Varien_Db_Ddl_Table::ACTION_NO_ACTION
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('googleshopping/items', 'store_id', 'core/store', 'store_id'),
    $installer->getTable('googleshopping/items'),
    'store_id',
    $this->getTable('core/store'),
    'store_id',
    Varien_Db_Ddl_Table::ACTION_CASCADE,
    Varien_Db_Ddl_Table::ACTION_NO_ACTION
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('googleshopping/attributes', 'attribute_id', 'eav/attribute', 'attribute_id'),
    $installer->getTable('googleshopping/attributes'),
    'attribute_id',
    $this->getTable('eav/attribute'),
    'attribute_id',
    Varien_Db_Ddl_Table::ACTION_CASCADE,
    Varien_Db_Ddl_Table::ACTION_NO_ACTION
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('googleshopping/attributes', 'type_id', 'googleshopping/types', 'type_id'),
    $installer->getTable('googleshopping/attributes'),
    'type_id',
    $this->getTable('googleshopping/types'),
    'type_id',
    Varien_Db_Ddl_Table::ACTION_CASCADE,
    Varien_Db_Ddl_Table::ACTION_NO_ACTION
);
