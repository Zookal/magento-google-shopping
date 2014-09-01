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

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$typesTable = new Varien_Db_Ddl_Table();
$typesTable->setName($this->getTable('googleshopping/types'))
    ->addColumn('type_id',          Varien_Db_Ddl_Table::TYPE_INTEGER,  10, array('nullable' => false, 'unsigned' => true, 'primary' => true))
    ->addColumn('attribute_set_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, 5,  array('unsigned' => true, 'nullable' => false))
    ->addColumn('target_country',   Varien_Db_Ddl_Table::TYPE_CHAR,     2,  array('nullable' => false, 'default' => 'US'))
    ->addForeignKey(
        'GOOGLESHOPPING_TYPES_ATTRIBUTE_SET_ID',
        'attribute_set_id',
        $this->getTable('eav/attribute_set'),
        'attribute_set_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE
     )
     ->setOption('ENGINE', 'InnoDB')
     ->setOption('DEFAULT CHARSET', 'utf8')
     ->setOption('COMMENT', 'Google Content Item Types link Attribute Sets');

$itemsTable = new Varien_Db_Ddl_Table();
$itemsTable->setName($this->getTable('googleshopping/items'))
    ->addColumn('item_id',          Varien_Db_Ddl_Table::TYPE_INTEGER,  10,     array('nullable' => false, 'unsigned' => true, 'primary' => true))
    ->addColumn('type_id',          Varien_Db_Ddl_Table::TYPE_INTEGER,  10,     array('nullable' => false, 'unsigned' => true, 'default' => 0))
    ->addColumn('product_id',       Varien_Db_Ddl_Table::TYPE_INTEGER,  10,     array('nullable' => false, 'unsigned' => true))
    ->addColumn('gcontent_item_id', Varien_Db_Ddl_Table::TYPE_VARCHAR,  255,    array('nullable' => false))
    ->addColumn('store_id',         Varien_Db_Ddl_Table::TYPE_SMALLINT, 5,      array('nullable' => false, 'unsigned' => true))
    ->addForeignKey(
        'GOOGLESHOPPING_ITEMS_PRODUCT_ID',
        'product_id',
        $this->getTable('catalog/product'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE
     )
    ->addForeignKey(
        'GOOGLESHOPPING_ITEMS_STORE_ID',
        'store_id',
        $this->getTable('core/store'),
        'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE
     )
     ->setOption('ENGINE', 'InnoDB')
     ->setOption('DEFAULT CHARSET', 'utf8')
     ->setOption('COMMENT', 'Google Content Items Products');

$attributesTable = new Varien_Db_Ddl_Table();
$attributesTable->setName($this->getTable('googleshopping/attributes'))
    ->addColumn('id',                   Varien_Db_Ddl_Table::TYPE_INTEGER,  10,     array('nullable' => false, 'unsigned' => true, 'primary' => true))
    ->addColumn('attribute_id',         Varien_Db_Ddl_Table::TYPE_SMALLINT, 5,      array('nullable' => false, 'unsigned' => true))
    ->addColumn('gcontent_attribute',   Varien_Db_Ddl_Table::TYPE_VARCHAR,  255,    array('nullable' => false))
    ->addColumn('type_id',              Varien_Db_Ddl_Table::TYPE_INTEGER,  10,     array('nullable' => false, 'unsigned' => true))
    ->addForeignKey(
        'GOOGLESHOPPING_ATTRIBUTES_ATTRIBUTE_ID',
        'attribute_id',
        $this->getTable('eav/attribute'),
        'attribute_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE
     )
    ->addForeignKey(
        'GOOGLESHOPPING_ATTRIBUTES_TYPE_ID',
        'type_id',
        $this->getTable('googleshopping/types'),
        'type_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE
     )
     ->setOption('ENGINE', 'InnoDB')
     ->setOption('DEFAULT CHARSET', 'utf8')
     ->setOption('COMMENT', 'Google Content Attributes link Product Attributes');

$installer->startSetup();

$connection = $installer->getConnection();
$connection->createTable($typesTable);
$connection->modifyColumn($typesTable->getName(), 'type_id', 'int(10) unsigned NOT NULL auto_increment');
$connection->createTable($itemsTable);
$connection->modifyColumn($itemsTable->getName(), 'item_id', 'int(10) unsigned NOT NULL auto_increment');
$connection->addColumn($this->getTable('googleshopping/items'), 'published', 'DATETIME NOT NULL DEFAULT "0000-00-00 00:00:00"');
$connection->addColumn($this->getTable('googleshopping/items'), 'expires', 'DATETIME NOT NULL DEFAULT "0000-00-00 00:00:00"');
$connection->createTable($attributesTable);
$connection->modifyColumn($attributesTable->getName(), 'id', 'int(10) unsigned NOT NULL auto_increment');

$installer->endSetup();
