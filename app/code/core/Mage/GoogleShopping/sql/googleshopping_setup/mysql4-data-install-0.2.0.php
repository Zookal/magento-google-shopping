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
/** @var $installer Mage_Core_Model_Resource_Setup */

if (Mage::helper('googleshopping')->isModuleEnabled('Mage_GoogleBase')) {
    $typesInsert = $installer->getConnection()
        ->select()
        ->from(
            $installer->getTable('googlebase/types'),
            array(
                'type_id',
                'attribute_set_id',
                'target_country'
            )
        )
        ->insertFromSelect($installer->getTable('googleshopping/types'));

    $itemsInsert = $installer->getConnection()
        ->select()
        ->from(
            $installer->getTable('googlebase/items'),
            array(
                'item_id',
                'type_id',
                'product_id',
                'gbase_item_id',
                'store_id',
                'published',
                'expires'
            )
        )
        ->insertFromSelect($installer->getTable('googleshopping/items'));

    $attributes = '';
    foreach (Mage::getModel('googleshopping/config')->getAttributes() as $destAttribtues) {
        foreach ($destAttribtues as $code => $info) {
            $attributes .= "'$code',";
        }
    }
    $attributes = rtrim($attributes, ',');
    $attributesInsert = $installer->getConnection()
        ->select()
        ->from(
            $installer->getTable('googlebase/attributes'),
            array(
                'id',
                'attribute_id',
                'gbase_attribute' => new Zend_Db_Expr("IF(gbase_attribute IN ($attributes), gbase_attribute, '')"),
                'type_id',
            )
        )
        ->insertFromSelect($installer->getTable('googleshopping/attributes'));

    $installer->run($typesInsert);
    $installer->run($attributesInsert);
    $installer->run($itemsInsert);
}
