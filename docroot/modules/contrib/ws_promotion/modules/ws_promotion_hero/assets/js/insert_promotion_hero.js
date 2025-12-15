(function ($, Drupal, drupalSettings, once) {

  Drupal.behaviors.init_promotion_hero = {
    attach: function (context, settings) {

      for (const key in drupalSettings.ws_promotion_hero) {
        const heroConfig = drupalSettings.ws_promotion_hero[key];

        once('init_promotion_hero', $(`#inline-blocklb-hero${key}`)).forEach(function (hero) {
          const promotionHero = $(hero).find('.hero__banner')[0];

          $.ajax({
            url: heroConfig.url,
            method: "GET",
            headers: {
              "Content-Type": "application/hal+json"
            },
            success: function (data, status, xhr) {
              if (data.data) {
                promotionHero.outerHTML = data.data;
              }
            }
          });
        });
      }
    }
  };

})(jQuery, Drupal, drupalSettings, once);
