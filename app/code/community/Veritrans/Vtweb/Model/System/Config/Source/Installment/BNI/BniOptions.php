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
 * @category    Veritrans
 * @package     Veritrans_Vtweb
 * @copyright   Copyright (c) 2014 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Installment Options Source Model for BNI
 */
class Veritrans_Vtweb_Model_System_Config_Source_Installment_BNI_BniOptions
{
    public function toOptionArray()
    {
        return array(
            array('value' => '3', 'label' => Mage::helper('vtweb')->__('3 months')),
            array('value' => '6', 'label' => Mage::helper('vtweb')->__('6 months')),
            array('value' => '12', 'label' => Mage::helper('vtweb')->__('12 months')),
            );
    }
}
