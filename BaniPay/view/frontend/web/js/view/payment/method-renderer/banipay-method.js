define(["Magento_Checkout/js/view/payment/default"], function (Component) {
  "use strict";

  return Component.extend({
    defaults: {
      template: "BaniPayPaymentGateway3_BaniPay/payment/banipay",
    },

    /** Returns send check to info */
    getMailingAddress: function () {
      return window.checkoutConfig.payment.checkmo.mailingAddress;
    },
  });
});
