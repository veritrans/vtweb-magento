<?php
/**
 * Veritrans VT Web Payment Controller
 *
 * @category   Mage
 * @package    Mage_Veritrans_VtWeb_PaymentController
 * @author     Kisman Hong, plihplih.com
 * This class is used for handle redirection after placing order.
 * function redirectAction -> redirecting to Veritrans VT Web
 * function responseAction -> when payment at Veritrans VT Web is completed or failed, the page will be redirected to this function, 
 * you must set this url in your Veritrans MAP merchant account. http://yoursite.com/vtweb/payment/response
 */
require_once 'veritrans.php';

class Veritrans_Vtweb_PaymentController extends Mage_Core_Controller_Front_Action {

	/**
	     * @return Mage_Checkout_Model_Session
	     */
	protected function _getCheckout() {
		return Mage::getSingleton('checkout/session');
	}
	// The redirect action is triggered when someone places an order, redirecting to Veritrans payment page.
	public function redirectAction() {
		$orderIncrementId = $this->_getCheckout()->getLastRealOrderId();
		$order = Mage::getModel('sales/order')->loadByIncrementId($orderIncrementId);
		$sessionId = Mage::getSingleton("core/session");
		
		/* send an order email when redirecting to payment page although payment has not been completed. */
		$order->setState(Mage_Sales_Model_Order::STATE_PAYMENT_REVIEW, true, 'New Order, waiting for payment.');
		$order->sendNewOrderEmail();
		$order->setEmailSent(true); 
		
		$veritrans = new Veritrans;
		$veritrans->merchant_id = Mage::helper('vtweb/data')->_getMerchantID();
		// Supposed to be merchant_hash_key, not merchant_hash
		$veritrans->merchant_hash_key = Mage::helper('vtweb/data')->_getMerchantHashKey();
		//var_dump(Mage::helper('vtweb/data')->_getMerchantHashKey());
		$veritrans->settlement_type = '01';
		$veritrans->order_id = $orderIncrementId;
		$veritrans->session_id = $sessionId->getSessionId();
		// Gross amount must be total of commodities price
		$veritrans->gross_amount = (int)$order->getBaseGrandTotal();
		$veritrans->billing_address_different_with_shipping_address = 1;
		$veritrans->required_shipping_address = 0;		
		$veritrans->first_name = $order->getShippingAddress()->getFirstname();
		$veritrans->last_name = $order->getShippingAddress()->getLastname();
		$veritrans->address1 = $order->getShippingAddress()->getStreet(1);
		$veritrans->address2 = $order->getShippingAddress()->getStreet(2);
		$veritrans->city = $order->getShippingAddress()->getCity();
		//$veritrans->country_code = $order->getShippingAddress()->getCountryId();
		$veritrans->country_code = 'IDN'; // this is hard coded because magento and veritrans country code is not the same.
		$veritrans->postal_code = $order->getShippingAddress()->getPostcode();
		$veritrans->email = $order->getShippingAddress()->getEmail();
		$veritrans->phone = $order->getShippingAddress()->getTelephone();
	
		$items = $order->getAllItems();		
		$shipping_amount = (int)$order->getShippingAmount();
		$shipping_tax_amount = (int) (int)$order->getShippingTaxAmount();
		$commodities =  array ();		
		foreach ($items as $itemId => $item){
			array_push($commodities, array("COMMODITY_ID" => $item->getProductId(), "COMMODITY_PRICE" => (int)$item->getPrice(), 
				"COMMODITY_QTY" => $item->getQtyToInvoice(), 
				"COMMODITY_NAME1" => $item->getName(), "COMMODITY_NAME2" => $item->getName()));
                }
		
		if($shipping_amount > 0){
			array_push($commodities, array("COMMODITY_ID" => '1234', "COMMODITY_PRICE" => $shipping_amount, 
				"COMMODITY_QTY" => 1, 
				"COMMODITY_NAME1" => 'Shipping '. $order->getShippingDescription(), 
				"COMMODITY_NAME2" => 'Shipping '. $order->getShippingDescription()));
		}
		
		if($shipping_tax_amount > 0){
			array_push($commodities, array("COMMODITY_ID" => '4321', "COMMODITY_PRICE" => $shipping_tax_amount, 
				"COMMODITY_QTY" => 1, 
				"COMMODITY_NAME1" => 'Shipping Tax Amount ', 
				"COMMODITY_NAME2" => 'Shipping Tax Amount '));
		}
		$veritrans->commodity = $commodities;
		$keys = $veritrans->get_keys();
		
		$payment = $order->getPayment();
		// for security comparation when getting notification, i use "additional_data" field in magento to save "token_merchant"
		$payment->setTokenBrowser($keys['token_merchant'])->save(); 
		
		$this->loadLayout();
		$block = $this->getLayout()->createBlock('Mage_Core_Block_Template','vtweb',array('template' => 'vtweb/redirect.phtml'));
		$block->setData('token_browser', $keys['token_browser']);
		$block->setData('merchant_id', $veritrans->merchant_id);
		$block->setData('redirect_url', Mage::helper('vtweb/data')->_getRedirectURL());
		$block->setData('redirect_message', Mage::helper('vtweb/data')->_getRedirectMessage());
		$this->getLayout()->getBlock('content')->append($block);
		$this->getResponse()->setBody($block->toHtml());
		//$this->renderLayout(); 		
	}
	
	// The response action is triggered when your gateway sends back a response after processing the customer's payment, we will not update to success because success is valid when notification (security reason)
	public function responseAction() {
		//var_dump($_POST); use for debugging value.
		if($this->getRequest()->isPost()) {
			$orderId = $_POST['orderId']; // Generally sent by gateway
			$status = $_POST['mStatus'];
			if($status == 'success' && !is_null($orderId) && $orderId != '') {
				// Redirected by Veritrans, if ok
				Mage::getSingleton('checkout/session')->unsQuoteId();				
				Mage_Core_Controller_Varien_Action::_redirect('checkout/onepage/success', array('_secure'=>true));
			}
			else {
				// There is a problem in the response we got
				$this->cancelAction();
				Mage_Core_Controller_Varien_Action::_redirect('checkout/onepage/failure', array('_secure'=>true));
			}
		}
		else{
			Mage_Core_Controller_Varien_Action::_redirect('');
		}
	}
	
	// Veritrans will send notification of the payment status, this is only way we make sure that the payment is successed, if success send the item(s) to customer :p 
	public function notificationAction() {
		if($this->getRequest()->isPost()) { // Veritrans use POST method for notification
			
			$orderId = $_POST['orderId']; // Sent by Veritrans gateway
			$order = Mage::getModel('sales/order');
			$order->loadByIncrementId($orderId);
			$payment = $order->getPayment();
			$tokenBrowser = $payment->getTokenBrowser();
		
			//security check is here, if success then compare the token_merchant that generated at "redirectAction" function
			if( $_POST['mStatus'] == 'success' && $tokenBrowser == $_POST['TOKEN_MERCHANT']) { 
				// Payment was successful, so update the order's state, send order email and move to the success page
				$order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true, 'Gateway has successed the payment.');				
				$order->sendOrderUpdateEmail(true, '<b>Payment Received Successfully!</b>');
				$paymentDueDate = date("Y-m-d H:i:s");
				$payment->setPaymentDueDate($paymentDueDate);
				$payment->setNotificationBody(json_encode($_POST));
				$order->setPaymentDueDate($paymentDueDate);
				$order->setMethod($payment->getMethodInstance()->getTitle());
				$order->save();
			
				Mage::getSingleton('checkout/session')->unsQuoteId();				
			}
			else {
				//when payment is failed, cancel the order. 
				$order->cancel()->setState(Mage_Sales_Model_Order::STATE_CANCELED, true, 'Gateway has problem for the payment.');
				$order->sendOrderUpdateEmail(true, '<b>Your Order Payment cannot be processed!</b>');
				$order->cancel()->save();
			
				Mage::getSingleton('checkout/session')->unsQuoteId();				
			} 
		}
	}
	
	// The cancel action is triggered when an order is to be cancelled
	public function cancelAction() {
		if (Mage::getSingleton('checkout/session')->getLastRealOrderId()) {
		    $order = Mage::getModel('sales/order')->loadByIncrementId(Mage::getSingleton('checkout/session')->getLastRealOrderId());
		    if($order->getId()) {
			// Flag the order as 'cancelled' and save it
		        $order->cancel()->setState(Mage_Sales_Model_Order::STATE_CANCELED, true, 'Gateway has declined the payment.')->save();
		    }
		}
	}
}

?>
