Veritrans VT Web Extension.

1 - Copy the app and skin folders into magento root folders.

2 - Run the following SQL commands on your shop magento database:

ALTER TABLE sales_flat_order_payment ADD payment_due_date datetime DEFAULT NULL; 

ALTER TABLE sales_flat_order_payment ADD token_merchant varchar(100) DEFAULT NULL; 

INSERT INTO eav_attribute(entity_type_id,attribute_code,attribute_model,backend_model,backend_type,backend_table,frontend_model,frontend_input,frontend_label,frontend_class,source_model,is_required,is_user_defined,default_value,is_unique,note ) values(5, 'payment_due_date', null, 'eav/entity_attribute_backend_datetime', 'datetime', '', '', 'date', '',null, '',1,0,'',0,'');

INSERT INTO eav_attribute( entity_type_id, attribute_code, attribute_model, backend_model, backend_type, backend_table, frontend_model, frontend_input, frontend_label, frontend_class, source_model, is_required, is_user_defined, default_value, is_unique, note ) VALUES ( 5, 'token_merchant', NULL , NULL , 'varchar', '', '', 'text', '', NULL , '', 1, 0, '', 0, '' );

3 - Change app/code/core/Mage/Sales/Model/Entity/Setup.php

```
		'order_payment' => array(
                'entity_model'      => 'sales/order_payment',
                'table'=>'sales/order_entity',
                'attributes' => array(
                    'parent_id' => array(
                        'type'=>'static',
                        'backend'=>'sales_entity/order_attribute_backend_child'
                    ),
                    'quote_payment_id'      => array('type'=>'int'),
                    'method'                => array(),
                    'additional_data'       => array('type'=>'text'),
                    'last_trans_id'         => array(),
                    'po_number'     => array(),

                    'cc_type'       => array(),
                    'cc_number_enc' => array(),
                    'cc_last4'      => array(),
                    'cc_owner'      => array(),
                    'cc_exp_month'  => array(),
                    'cc_exp_year'   => array(),

                    'cc_ss_issue' => array(),
                    'cc_ss_start_month' => array(),
                    'cc_ss_start_year' => array(),

                    'cc_status'             => array(),
                    'cc_status_description' => array(),
                    'cc_trans_id'           => array(),
                    'cc_approval'           => array(),
                    'cc_avs_status'         => array(),
                    'cc_cid_status'         => array(),

                    'cc_debug_request_body' => array(),
                    'cc_debug_response_body'=> array(),
                    'cc_debug_response_serialized' => array(),

                    'anet_trans_method'     => array(),
                    'echeck_routing_number' => array(),
                    'echeck_bank_name'      => array(),
                    'echeck_account_type'   => array(),
                    'echeck_account_name'   => array(),
                    'echeck_type'           => array(),

                    'amount_ordered'    => array('type'=>'decimal'),
                    'amount_authorized' => array('type'=>'decimal'),
                    'amount_paid'       => array('type'=>'decimal'),
                    'amount_canceled'   => array('type'=>'decimal'),
                    'amount_refunded'   => array('type'=>'decimal'),
                    'shipping_amount'   => array('type'=>'decimal'),
                    'shipping_captured' => array('type'=>'decimal'),
                    'shipping_refunded' => array('type'=>'decimal'),

                    'base_amount_ordered'    => array('type'=>'decimal'),
                    'base_amount_authorized' => array('type'=>'decimal'),
                    'base_amount_paid'       => array('type'=>'decimal'),
                    'base_amount_canceled'   => array('type'=>'decimal'),
                    'base_amount_refunded'   => array('type'=>'decimal'),
                    'base_shipping_amount'   => array('type'=>'decimal'),
                    'base_shipping_captured' => array('type'=>'decimal'),
                    'base_shipping_refunded' => array('type'=>'decimal'),
		    
					'payment_due_date' => array('type'=>'datetime'), // the time that Veritrans send us notification
					'token_merchant' => array('type'=>'varchar'), // save token_merchant to payment object
                ), 
```			
				
4 - Change the Payment Notification URL in MAP to http://[yoursite.com]/vtweb/payment/notification 			
