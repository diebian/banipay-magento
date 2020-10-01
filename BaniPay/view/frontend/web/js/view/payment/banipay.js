define([
  "uiComponent",
  "Magento_Checkout/js/model/payment/renderer-list",
], function (Component, rendererList) {
  "use strict";
  rendererList.push({
    type: "banipay",
    component:
      "BaniPayPaymentGateway3_BaniPay/js/view/payment/method-renderer/banipay-method",
  });
  /** Add view logic here if needed */
  return Component.extend({});
});
