/**
 * @file
 * Accessibility fixes for AddToCalendar JS.
 */

(function ($) {
  "use strict";

  Drupal.behaviors.atcFix = {
    attach: function (context, settings) {
      once('addtocalendar', $('.addtocalendar')).forEach(function (el) {
        $(el).find('.atcb-link').attr('tabindex','0');
        $(el).on('keydown', function(e) {
          // Trigger click behavior on the RETURN key, like regular links.
          if (e.which == 13) {
            el.click();
          }
        });
        $(el).click(function () {
          if ($(this).hasClass('activated')) {
            $(this).removeClass('activated');
            $(this).find('.atcb-list').css({
              'display': 'none',
              'visibility': 'hidden'
            });
          }
          else {
            $(this).addClass('activated');
            $(this).find('.atcb-list').css({
              'display': 'block',
              'visibility': 'visible'
            });
          }
        });
      });
    }
  };

})(jQuery);
