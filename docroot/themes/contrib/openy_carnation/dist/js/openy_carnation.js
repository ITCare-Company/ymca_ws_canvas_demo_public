/**
 * @file
 * Open Y Carnation JS.
 */
(function ($) {
  "use strict";
  Drupal.openyCarnation = Drupal.openyCarnation || {};

  /**
   * Alert Modals
   */
  Drupal.behaviors.openyAlertModals = {
    attach: function (context, settings) {

      function triggerModals(context) {
        var alertModals = $('.alert-modal', context);

        if (alertModals.length) {
          alertModals.on('hidden.bs.modal', function (e) {
            $(this).remove();
          });
          alertModals.modal('show');
        }
      }

      // Trigger modals on page load.
      triggerModals();

      // Check again on ajaxStop to handle form alerts.
      $(context).ajaxStop(function() {
        triggerModals(context);
      });
    }
  };
})(jQuery);
