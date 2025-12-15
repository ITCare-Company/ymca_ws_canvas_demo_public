(function ($, Drupal, drupalSettings, once) {

  Drupal.behaviors.init_promotion_ping_pong = {
    attach: function (context, settings) {
      for (const key in drupalSettings.ws_promotion_ping_pong) {
        const pingPongConfig = drupalSettings.ws_promotion_ping_pong[key];

        once('init_promotion_ping_pong', $(`#inline-blocklb-ping-pong${key}`)).forEach(function (ping_pong) {
          const pingPongPromo = $(ping_pong).find('.ping-pong-item')[0];
          $.ajax({
            url: pingPongConfig.url,
            method: "GET",
            headers: {
              "Content-Type": "application/hal+json"
            },
            success: function (data, status, xhr) {
              if (data.data) {
                pingPongPromo.innerHTML = data.data;
              }
            }
          });
        });
      }
    }
  };

})(jQuery, Drupal, drupalSettings, once);
