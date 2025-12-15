(function ($, Drupal, drupalSettings, once) {

  Drupal.behaviors.init_promotion_card = {
    attach: function (context, settings) {

      for (const key in drupalSettings.ws_promotion_cards) {
        const cardConfig = drupalSettings.ws_promotion_cards[key];

        once('init_promotion_card', $(`#cards-${key}`)).forEach(function (cards) {
          const promotionCard = $(cards).children()[cardConfig.item_number];

          $.ajax({
            url: cardConfig.url,
            method: "GET",
            headers: {
              "Content-Type": "application/hal+json"
            },
            success: function (data, status, xhr) {
              if (data.data) {
                promotionCard.outerHTML = data.data;
                setTimeout(() => {
                  window.dispatchEvent(new Event('resize'));
                }, 250);
              }
            }
          });
        });
      }
    }
  };

})(jQuery, Drupal, drupalSettings, once);
