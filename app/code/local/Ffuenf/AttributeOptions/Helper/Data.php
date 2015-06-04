<?php

/**
 * Ffuenf_AttributeOptions extension.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category   Ffuenf
 *
 * @author     Achim Rosenhagen <a.rosenhagen@ffuenf.de>
 * @copyright  Copyright (c) 2015 ffuenf (http://www.ffuenf.de)
 * @license    http://opensource.org/licenses/mit-license.php MIT License
 */
class Ffuenf_AttributeOptions_Helper_Data extends Ffuenf_AttributeOptions_Helper_Core
{
    /**
     * Path for the config for extension active status.
     */
    const CONFIG_EXTENSION_ACTIVE = 'attributeoptions/general/enabled';

    /**
     * A variable for the extension active state setting.
     *
     * @var bool
     */
    protected $bExtensionActive;

    /**
     * A check for the extension state.
     *
     * @return bool
     */
    public function isExtensionActive()
    {
        return $this->getStoreFlag(self::CONFIG_EXTENSION_ACTIVE, 'bExtensionActive');
    }

    public function getAttributeInformation($attribute)
    {
        if (is_numeric($attribute)) {
            $attributeId = $attribute;
        } else {
            $attributeId = Mage::getResourceModel('eav/entity_attribute')->getIdByCode('catalog_product', $attribute);
        }
        $attribute = Mage::getModel('catalog/resource_eav_attribute')->load($attributeId);

        return $attribute->getData();
    }

    public function getAllAttributeValuesFromAttribute($attribute)
    {
        $attribute = Mage::getModel('eav/config')->getAttribute('catalog_product', $attribute);
        $attributeArray = array();
        foreach ($attribute->getSource()->getAllOptions(true, true) as $option) {
            $attributeArray[$option['value']] = $option['label'];
        }

        return $attributeArray;
    }

    public function addAttributeValue($argattribute, $argvalue)
    {
        $attributemodel = Mage::getModel('eav/entity_attribute');
        $attributeoptionsmodel = Mage::getModel('eav/entity_attribute_source_table');
        $attributecode = $attributemodel->getIdByCode('catalog_product', $argattribute);
        $attribute = $attributemodel->load($attributecode);
        $attributeoptionsmodel->setAttribute($attribute);
        $options = $attributeoptionsmodel->getAllOptions(false);
        $value = array();
        $value['option'] = array($argvalue);
        $result = array('value' => $value);
        $attribute->setData('option', $result);
        $attribute->save();
        $attributemodel = Mage::getModel('eav/entity_attribute');
        $attributeoptionsmodel = Mage::getModel('eav/entity_attribute_source_table');
        $attributecode = $attributemodel->getIdByCode('catalog_product', $argattribute);
        $attribute = $attributemodel->load($attributecode);
        $attributeoptionsmodel->setAttribute($attribute);
        $options = $attributeoptionsmodel->getAllOptions(false);
        foreach ($options as $option) {
            if ($option['label'] == $argvalue) {
                return $option['value'];
            }
        }

        return false;
    }
}
