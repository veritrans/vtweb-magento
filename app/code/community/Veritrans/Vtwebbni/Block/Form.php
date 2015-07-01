<?php
/**
 * Veritrans VT Web form block
 *
 * @category   Mage
 * @package    Mage_Veritrans_VtWeb_Block_Form
 * @author     Kisman Hong, plihplih.com
 * when Veritrans payment method is chosen, vtweb/form.phtml template will be rendered through this class.
 */
class Veritrans_Vtwebbni_Block_Form extends Mage_Payment_Block_Form
{
    
    protected function _construct()
    {
        parent::_construct();

		$this->setFormMessage(Mage::helper('vtwebbni/data')->_getFormMessage());
        $this->setTemplate('vtwebbni/form.phtml');
    }
}
?>