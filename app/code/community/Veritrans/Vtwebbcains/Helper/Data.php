<?php
/**
 * Veritrans VT Web Helper Data
 *
 * @category   Mage
 * @package    Mage_Veritrans_VtWeb_PaymentController
 * @author     Kisman Hong, plihplih.com
 * this class is used for declaring variable of Veritrans's constant.
 */
class Veritrans_Vtwebbcains_Helper_Data extends Mage_Core_Helper_Abstract
{

	// Veritrans payment method title 
	function _getTitle(){
		return Mage::getStoreConfig('payment/vtwebbcains/title');
	}

	// Installment bank
	function _getInstallmentBank(){
		return Mage::getStoreConfig('payment/vtwebbcains/installment_bank');
	}

	// Installment terms, separate by comma (,) ex. 3,6,12
	function _getInstallmentTerms(){
		return Mage::getStoreConfig('payment/vtwebbcains/installment_terms');
	}
	
	// progress side bar, if true then show logo image, vice versa
	function _getInfoTypeIsImage(){
		return Mage::getStoreConfig('payment/vtwebbcains/info_type');
	}
	
	// Message to be shown when Veritrans payment method is chosen
	function _getFormMessage(){
		return Mage::getStoreConfig('payment/vtwebbcains/form_message');
	}
}