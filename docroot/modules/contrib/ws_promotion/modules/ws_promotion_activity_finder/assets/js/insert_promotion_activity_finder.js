(function ($, Drupal, drupalSettings, once) {

  Drupal.behaviors.insert_promotion_activity_finder = {
    attach: function (context, settings) {
      for (const key in drupalSettings.ws_promotion_activity_finder) {
        const activityConfig = drupalSettings.ws_promotion_activity_finder[key];

        once('init_promotion_activity_finder', $(`#inline-blocklb-activity-finder${key}`)).forEach(function (modal) {

          $.ajax({
            url: activityConfig.url,
            method: "GET",
            headers: {
              "Content-Type": "application/hal+json"
            },
            success: function (data, status, xhr) {
              if (data.data) {

                let i = setInterval(function() {
                  const promotionActivity =  $(modal).find('.placeholder__insert_adjacent_promo');
                  if (promotionActivity && promotionActivity[0]){
                    clearInterval(i);
                    promotionActivity[0].innerHTML = data.data;
                  }
                }, 1000);
              }
            }
          });
        });
      }
    }
  };

})(jQuery, Drupal, drupalSettings, once);
