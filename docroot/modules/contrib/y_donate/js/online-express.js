(function ($, Drupal) {
  Drupal.behaviors.onlineExpress = {
    attach: function (context, settings) {
      // Attach to #bbox-root because it exists. Anything deeper doesn't.
      $('#bbox-root', context).once('setQueryAmount').each(function () {

        // Helper function to get the query parameter.
        function getParameterByName(name, url = window.location.href) {
          name = name.replace(/[\[\]]/g, '\\$&');
          var regex = new RegExp('[?&]' + name + '(=([^&#]*)|&|#|$)'),
            results = regex.exec(url);
          if (!results) return null;
          if (!results[2]) return '';
          return decodeURIComponent(results[2].replace(/\+/g, ' '));
        }

        // Wait for the levels to arrive in the DOM.
        $("#bbox-root").arrive(".BBFormFieldContainerGivingLevels", function() {
          // Get the amount parameter and set it.
          const amt = getParameterByName('amount');
          if (amt) {
            $('input[value="' + amt + '"]').click();
          }
        });
      });
    }
  };
})(jQuery, Drupal);
