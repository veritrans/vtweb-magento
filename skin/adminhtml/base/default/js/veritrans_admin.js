document.observe('dom:loaded', function() {
  
  function getSensitiveOptions(str)
  {
    switch(str) {
      case 'v2_settings':
        return [
          // 'payment_vtweb_client_key_v2',
          // 'payment_vtweb_server_key_v2'
        ];
      case 'v2_vtweb_settings':
        return [];
      case 'v2_vtdirect_settings':
        return [];
      case 'v1_settings':
        return [];
      case 'v1_vtweb_settings':
        return [
          // 'payment_vtweb_merchant_id',
          // 'payment_vtweb_merchant_hash'
        ];
      case 'v1_vtdirect_settings':
        return [];
      case 'vtweb_settings':
        return [];
      case 'vtdirect_settings':
        return [];
      case 'sensitive':
        return [
          // 'payment_vtweb_client_key_v2',
          // 'payment_vtweb_server_key_v2',
          // 'payment_vtweb_merchant_id',
          // 'payment_vtweb_merchant_hash'
        ];
    }
  }

  function sensitiveOptions() {
    var api_version = $('payment_vtweb_api_version').value;
    var payment_type = $('payment_vtweb_payment_types').value;
    var api_string = 'v' + api_version + '_settings';
    var payment_type_string = payment_type + '_settings';
    var api_payment_type_string = 'v' + api_version + '_' + payment_type + '_settings';

    getSensitiveOptions('sensitive').forEach(function(element) {
      if ($(element))
        $('row_' + element).hide();
    });

    getSensitiveOptions(api_string).forEach(function(element) {
      if ($(element))
        $('row_' + element).show();
    });

    getSensitiveOptions(payment_type_string).forEach(function(element) {
      if ($(element))
        $('row_' + element).show();
    });

    getSensitiveOptions(api_payment_type_string).forEach(function(element) {
      if ($(element))
        $('row_' + element).show();
    });

  }

  if ($("payment_vtweb_api_version"))
  {
    $("payment_vtweb_api_version").observe('change', function(e, data) {
      // sensitiveOptions();
    });  
  }
  
  if ($("payment_vtweb_payment_types"))
  {
    $("payment_vtweb_payment_types").observe('change', function(e, data) {
      // sensitiveOptions();
    });  
  }  

  // sensitiveOptions();
});