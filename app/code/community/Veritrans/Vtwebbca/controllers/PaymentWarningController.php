<?php
  class Veritrans_Vtwebmandiri_PaymentWarningController extends Mage_Core_Controller_Front_Action {
    public function warningAction() {
      $data = Mage::getSingleton('checkout/session')->getMsg();
      Mage::getSingleton('checkout/session')->setMsg('#');
      
      $message = $this->getRequest()->getParam('message');

      if ($message == 1) {
        Mage::getSingleton('core/session')->addWarning('Sorry, we are unable to proceed your transaction with installment.<br>
          Transaction with installment is only allowed for one product type on your cart.<br><br>
          <a href="' . $data .'">Click here to continue with full payment</a>');
      }
      else {
        Mage::getSingleton('core/session')->addWarning('Sorry, we are unable to proceed your transaction with installment.<br>
          Transaction with installment is only allowed for transaction amount above Rp 500.000.<br><br>
          <a href="' . $data .'">Click here to continue with full payment</a>');
      }

      $this->loadLayout();
      $this->_initLayoutMessages('core/session');
      $this->renderLayout();
    }
  }