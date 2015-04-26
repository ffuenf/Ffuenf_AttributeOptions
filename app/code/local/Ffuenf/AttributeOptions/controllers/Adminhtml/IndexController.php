<?php
/**
 * Ffuenf_AttributeOptions extension
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 * 
 * @category   Ffuenf
 * @package    Ffuenf_AttributeOptions
 * @author     Achim Rosenhagen <a.rosenhagen@ffuenf.de>
 * @copyright  Copyright (c) 2015 ffuenf (http://www.ffuenf.de)
 * @license    http://opensource.org/licenses/mit-license.php MIT License
*/

class Ffuenf_AttributeOptions_Adminhtml_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        if (!Mage::helper('ffuenf_attributeoptions')->isExtensionActive()) {
            if (count($error)) Mage::getSingleton('adminhtml/session')->addError(Mage::helper('ffuenf_attributeoptions')->__('Extension Ffuenf_AttributeOptions is disabled! See configuration.'));
            $this->_redirectReferer();
        }
        $options = $this->getRequest()->getParam('options');
        $options = explode("\n", $options);
        $attr = Mage::helper('ffuenf_attributeoptions')->getAttributeInformation($this->getRequest()->getParam('attribute_id'));
        $existing = array_flip(Mage::helper('ffuenf_attributeoptions')->getAllAttributeValuesFromAttribute($attr['attribute_code']));
        $success = array();
        $error = array();
        foreach ($options as $key=>$option) {
            $option = trim($option);
            if (!isset($existing[$option])) { // skip existing values
                if (Mage::helper('ffuenf_attributeoptions')->addAttributeValue($attr['attribute_code'], $option)) {
                    $success[] = Mage::helper('ffuenf_attributeoptions')->__('Attribute option') . ' \'' . $option . '\' ' . Mage::helper('ffuenf_attributeoptions')->__('is added to') . ' \'' . Mage::helper('ffuenf_attributeoptions')->__($attr['frontend_label']) . '\'';
                }
            } else {
                $error[] = Mage::helper('ffuenf_attributeoptions')->__('Attribute option') . ' \'' . $option . '\' ' . Mage::helper('ffuenf_attributeoptions')->__('already existed in') . ' \'' . Mage::helper('ffuenf_attributeoptions')->__($attr['frontend_label']) . '\'';
            }
        }
        if (count($success)) Mage::getSingleton('core/session')->addSuccess(implode("<br />", $success));
        if (count($error)) Mage::getSingleton('core/session')->addError(implode("<br />", $error));
        $this->_redirectReferer();
    }

    public function mergeAction()
    {
        $this->write = Mage::getSingleton('core/resource')->getConnection('core_write');
        $this->read = Mage::getSingleton('core/resource')->getConnection('core_read');
        $error = array();
        $success = array();
        if (!Mage::helper('ffuenf_attributeoptions')->isExtensionActive()) {
            if (count($error)) Mage::getSingleton('adminhtml/session')->addError(Mage::helper('ffuenf_attributeoptions')->__('Extension Ffuenf_AttributeOptions is disabled! See configuration.'));
            $this->_redirectReferer();
        }
        $merge = $this->getRequest()->getParam('merge');
        if (!isset($merge) || count($merge)==0) {
            $error[] = Mage::helper('ffuenf_attributeoptions')->__('No options to merge selected!');
        }
        if (isset($merge) && count($merge)==1) {
            $error[] = Mage::helper('ffuenf_attributeoptions')->__('Only one option to merge is selected!');
        }
        $mergegoal = $this->getRequest()->getParam('mergegoal');
        if (empty($mergegoal)) {
            $error[] = Mage::helper('ffuenf_attributeoptions')->__("You haven't selected a merge goal.");
        }
        $attribute_id = $this->getRequest()->getParam('attribute_id');
        if (count($error) == 0) {
            foreach ($merge as $value) {
                if ($value == $mergegoal) continue;
                // do the hard work
                $options = $this->read->fetchAll('SELECT value_id,value FROM catalog_product_entity_int WHERE attribute_id = ? AND value = ?', array($attribute_id, $value));
                foreach ($options as $option) {
                    $this->write->query('UPDATE `' . Mage::getSingleton('core/resource')->getTableName('catalog_product_entity_int') . '` SET value = \'' . $mergegoal . '\' WHERE value_id = \'' . $option['value_id'] . '\'');
                }
                // delete option value
                $deleteOptions['delete'][$value] = true;
                $deleteOptions['value'][$value] = true;
            }
            // delete option value
            $setup = new Mage_Eav_Model_Entity_Setup('core_setup');
            $setup->addAttributeOption($deleteOptions);
        }
        if (count($success)) Mage::getSingleton('core/session')->addSuccess(implode("<br />", $success));
        if (count($error)) Mage::getSingleton('core/session')->addError(implode("<br />", $error));
        $this->_redirectReferer();
    }
}
