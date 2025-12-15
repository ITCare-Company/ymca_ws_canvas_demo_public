(function ($, Drupal, drupalSettings) {

  'use strict';

  function getCookie(name) {
    const value = `; ${document.cookie}`;
    const parts = value.split(`; ${name}=`);
    if (parts.length === 2) return parts.pop().split(';').shift();
    return null;
  }

  Drupal.behaviors.openy_popups_autoload = {
    attach: function (context, settings) {
      var popup = this;
      popup._context = context;

      if ($('body.page-node-type-class', context).length) {
        var change_link = $('.edit-class-popup', context);
        if (typeof popup.get_query_param().location === 'undefined') {
          popup.open_popup();
        } else {
          change_link.removeClass('hidden');
        }

        change_link.on('click', function () {
          popup.open_popup();
        });
      }
      else {
        var preferred_branch = getCookie('openy_preferred_branch');
        if (typeof this.get_query_param().location === 'undefined' && preferred_branch === null) {
          popup.open_popup();
        }
      }
    },

    open_popup: function () {
      const ctx = this._context || document;
      once('location-popup', 'a.location-popup-link', ctx).forEach(function (el) {
        el.click();
      });

      $(document).on('click', 'body > .ui-widget-overlay', function () {
        return false;
      });
    },

    get_query_param: function () {
      var query_string = {};
      var query = window.location.search.substring(1);
      var pairs = query.split('&');
      for (var i = 0; i < pairs.length; i++) {
        var pair = pairs[i].split('=');
        if (typeof query_string[pair[0]] === 'undefined') {
          query_string[pair[0]] = decodeURIComponent(pair[1]);
        }
        else if (typeof query_string[pair[0]] === 'string') {
          query_string[pair[0]] = [query_string[pair[0]], decodeURIComponent(pair[1])];
        }
        else {
          query_string[pair[0]].push(decodeURIComponent(pair[1]));
        }
      }
      return query_string;
    }
  };

})(jQuery, Drupal, drupalSettings);
