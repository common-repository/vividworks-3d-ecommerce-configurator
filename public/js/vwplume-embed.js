import vw3d from 'https://app.vividworks.com/embed/vividworks-frontend-embed.es.js';

(function(){
  function initvw() {
    let locale = '';

    if (typeof VWPlume.params !== 'undefined' ) {
      if (!!VWPlume.params.locale) {
        locale = VWPlume.params.locale.replace('_','-');
      }
    }

    let settings = {
      culture: locale,
    };

    // Only set up cart / prices integration if woocommerce is installed and active
    if (typeof woocommerce_params !== "undefined") {

      // Cart. 
      // Enable if we do NOT have the params (for a safe fallback), 
      // or if the "enable cart" setting is true
      if (typeof VWPlume.params == 'undefined' || !!VWPlume.params.cart_enabled) {
        settings.addToBasketCallback = VWPlume.Cart.handlebom;
      }

      // Prices
      settings.getPricesCallback = VWPlume.Products.getPrices;
    }
    
    vw3d(settings).then(resp => window.vw3d = resp);
  }
  
  var timer = window.setInterval(function () {
    if (typeof VWPlume !== 'undefined') {
      initvw();
      window.clearInterval(timer);
    }
  }, 10);
})();
