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

class Ffuenf_AttributeOptions_Block_Catalog_Product_Attribute_Edit_Tab_Options extends Mage_Eav_Block_Adminhtml_Attribute_Edit_Options_Abstract
{
    public function __construct() {
        parent::__construct();
        $this->setTemplate('ffuenf/attributeoptions/options.phtml');
    }
}
