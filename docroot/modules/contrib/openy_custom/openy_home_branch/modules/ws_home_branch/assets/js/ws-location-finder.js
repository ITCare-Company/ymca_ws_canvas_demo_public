/**
 * @file
 * Location finder home branch extension override.
 */

(function ($, Drupal, drupalSettings) {

  "use strict";

  /**
   * Override hb-location-finder.js plugin.
   *
   * @type {Drupal~behavior}
   */
  if (Drupal.homeBranch.plugins.length > 0) {
    for (var key in Drupal.homeBranch.plugins) {
      if (Drupal.homeBranch.plugins.hasOwnProperty(key) && Drupal.homeBranch.plugins[key]['name'] === 'hb-location-finder') {
        Drupal.homeBranch.plugins[key]['settings']['addMarkup'] = function (context) {
          let id = context.data('hb-id');
          let $markup = $('<div class="card-footer"><div class="hb-location-checkbox-wrapper hb-checkbox-wrapper">' +
            '<input type="checkbox" value="' + id + '" id="hb-location-checkbox-' + id + '" class="hb-location-checkbox">' +
            '<label for="hb-location-checkbox-' + id + '">' + this.selectedText + '</label>' +
            '</div></div>');
          $markup.appendTo(context);
          this.element = $markup.find('input');
        };
      }
    }
  }
})(jQuery, Drupal, drupalSettings);
