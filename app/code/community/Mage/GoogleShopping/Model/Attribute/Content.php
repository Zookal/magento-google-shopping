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

/**
 * Content attribute's model
 *
 * @category   Mage
 * @package    Mage_GoogleShopping
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_GoogleShopping_Model_Attribute_Content extends Mage_GoogleShopping_Model_Attribute_Default
{
    /**
     * Set current attribute to entry (for specified product)
     *
     * @param Mage_Catalog_Model_Product $product
     * @param Varien_Gdata_Gshopping_Entry $entry
     * @return Varien_Gdata_Gshopping_Entry
     */
    public function convertAttribute($product, $entry)
    {
        $mapValue = $this->getProductAttributeValue($product);
        $description = $this->getGroupAttributeDescription();
        if (!is_null($description)) {
            $mapValue = $description->getProductAttributeValue($product);
        }

        if (!is_null($mapValue)) {
            $descrText = $mapValue;
        } elseif ($product->getDescription()) {
            $descrText = $product->getDescription();
        } else {
            $descrText = 'no description';
        }
        $descrText = Mage::helper('googleshopping')->cleanAtomAttribute($descrText);
        $entry->setContent($entry->getService()->newContent()->setText($descrText));

        return $entry;
    }
}
