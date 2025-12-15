(function ($, Drupal, drupalSettings, once) {

  Drupal.behaviors.init_promotion_promo = {
    attach: function (context, settings) {
      for (const key in drupalSettings.ws_promotion_promo) {
        const promoConfig = drupalSettings.ws_promotion_promo[key];

        once('init_promotion_promo', $(`#inline-blocklb-promo${key}`)).forEach(function (promo) {
          const promoCard = $(promo).find('.block-content')[0];
          $.ajax({
            url: promoConfig.url,
            method: "GET",
            headers: {
              "Content-Type": "application/hal+json"
            },
            success: function (data, status, xhr) {
              if (data.data) {
                promoCard.outerHTML = data.data;
              }
            }
          });
        });
      }
    }
  };

})(jQuery, Drupal, drupalSettings, once);
