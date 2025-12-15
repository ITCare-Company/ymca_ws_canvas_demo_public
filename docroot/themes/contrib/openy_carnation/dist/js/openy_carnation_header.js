/**
 * @file
 * Open Y Carnation JS.
 */
(function ($, once) {
  "use strict";

  /**
   * Show/hide desktop search block.
   */
  Drupal.behaviors.openySearchToggle = {
    attach: function (context, settings) {
      var searchBtn = $('.site-search button');
      var searchInput = $('header input.search-input');
      var mainMenuLinks = $('.page-head__main-menu .nav-level-1 li:not(:eq(0))').find('a, button');
      var searchClose = $('.page-head__search-close');

      $(once('openy-search-toggle-hide', searchBtn))
        .on('click', function () {
          mainMenuLinks.removeClass('show').addClass('fade');
          setTimeout(function () {
            searchInput.focus();
          }, 500);
        });

      $(once('openy-search-toggle-show', searchClose))
        .on('click', function () {
          mainMenuLinks.addClass('show');
        });
    }
  };

})(jQuery, once);
