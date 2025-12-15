(function ($, Drupal, drupalSettings, once) {

  Drupal.behaviors.init_promotion_modal = {
    attach: function (context, settings) {
      for (const key in drupalSettings.ws_promotion_modal) {
        const modalConfig = drupalSettings.ws_promotion_modal[key];

        once('init_promotion_modal', $(`#modal-${key}`)).forEach(function (modal) {
          const promotionModal = $(modal).find('.modal-content')[0];

          $.ajax({
            url: modalConfig.url,
            method: "GET",
            headers: {
              "Content-Type": "application/hal+json"
            },
            success: function (data, status, xhr) {
              if (data.data) {
                const parentWrapper = $(promotionModal).parents('.modal-lg')[0];
                if (parentWrapper) {
                  $(parentWrapper).removeClass('modal-lg').addClass('modal-xl');
                }
                promotionModal.innerHTML = data.data;
              }
            }
          });
        });
      }
    }
  };

})(jQuery, Drupal, drupalSettings, once);
