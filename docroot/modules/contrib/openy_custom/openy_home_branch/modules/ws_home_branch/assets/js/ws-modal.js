/**
 * @file
 * Modal home branch extension override.
 */

(function ($, Drupal, drupalSettings) {

  "use strict";

  /**
   * Override hb-location-finder.js plugin.
   *
   * @type {Drupal~behavior}
   */
  if (Drupal.homeBranch.plugins.length > 0) {
    for (let key in Drupal.homeBranch.plugins) {

      if (Drupal.homeBranch.plugins.hasOwnProperty(key) && Drupal.homeBranch.plugins[key]['name'] === 'hb-loc-modal') {
        Drupal.homeBranch.plugins[key]['settings']['addMarkup'] = function (context) {
          this.element = $('<div id="hb-loc-modal" class="hidden modal" tabindex="-1">' +
            '<div class="hb-loc-modal__modal modal-dialog"" role="dialog">' +
            '<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="visually-hidden">Close</span> </button>' +
            '<div class="modal-content">' +
            '<div class="hb-loc-modal__modal--header">' +
            '<spant class="title">' + this.modalTitle + '</spant>' +
            '</div>' +
            '<div class="hb-loc-modal__modal--body">' +
            '<div>' +
            this.modalDescription + '<br>' +
            '</div>' +
            '<div class="form">' +
          '<label for="hb-locations-list">YMCA locations</label>' +
          '<div class="select-wrapper">' +
          '<select id="hb-locations-list" required class="form-select form-control">' +
            '<option value="null" selected>Choose your preferred location</option>' +
            '</select>' +
            '</div>' +
            '</div>' +
            '</div>' +
            '<div class="hb-loc-modal__modal--footer">' +
            '<button class="btn btn-lg btn-success action-save">Set as preferred location</button>' +
            '<div class="dont-ask hb-checkbox-wrapper">' +
            '<label for="hb-dont-ask-checkbox">' +
            '<input type="checkbox" id="hb-dont-ask-checkbox">' +
            this.dontAskTitle + '</label>' +
            '</div>' +
            '</div>' +
            '</div>' +
            '</div>' +
            '</div>');
          $('body').append(this.element);
          this.appendOptions();
          this.handleDontAsk();
          this.bindButtons();

          // Let HomeBranch know how to call the modal window.
          let self = this;
          Drupal.homeBranch.showModal = function (force) {
            self.show(force);
          };
        };
      }
    }
  }
})(jQuery, Drupal, drupalSettings);
