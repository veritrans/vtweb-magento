<?php
class Veritrans_Vtwebbin_Helper_Data extends Mage_Core_Helper_Abstract
{

	// Veritrans payment method title 
	function _getTitle(){
		return Mage::getStoreConfig('payment/vtweb/title');
	}
	
	// Merchant ID given by Veritrans when registering via veritrans.co.id, you can get it from MAP (https://payments.veritrans.co.id/map/users/sign_in) 
	function _getMerchantID(){
		return Mage::getStoreConfig('payment/vtweb/merchant_id');
	}

	// Installment bank
	function _getInstallmentBank(){
		return Mage::getStoreConfig('payment/vtweb/installment_bank');
	}

	// Installment terms, separate by comma (,) ex. 3,6,12
	function _getInstallmentTerms(){
		return Mage::getStoreConfig('payment/vtweb/installment_terms');
	}
	
	// progress side bar, if true then show logo image, vice versa
	function _getInfoTypeIsImage(){
		return Mage::getStoreConfig('payment/vtweb/info_type');
	}
	
	// Message to be shown when Veritrans payment method is chosen
	function _getFormMessage(){
		return Mage::getStoreConfig('payment/vtweb/form_message');
	}
}