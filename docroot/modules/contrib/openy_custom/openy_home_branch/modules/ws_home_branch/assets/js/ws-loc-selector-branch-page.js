/**
 * @file
 * Location finder extension with Home Branch logic.
 */

(function ($, Drupal, drupalSettings) {

  "use strict";

  /**
   * Add plugin, that related to Branch Page.
   */
  Drupal.homeBranch.plugins.push({
    name: 'hb-loc-selector-branch-page',
    attach: function (settings) {
      $('.node--type-branch--use-lb').hbPlugin(settings);
    },
    settings: {
      selector: '.hb-location-checkbox',
      event: 'change',
      element: null,
      wrapper: null,
      labelSelector: '.hb-location-checkbox-wrapper label',
      selectedText: drupalSettings.home_branch.hb_loc_selector_branch_lb_page.selectedText,
      notSelectedText: drupalSettings.home_branch.hb_loc_selector_branch_lb_page.notSelectedText,
      placeholderSelector: drupalSettings.home_branch.hb_loc_selector_branch_lb_page.placeholderSelector,
      selectedClass: 'hb-branch-selector--selected',
      init: function () {
        if (!this.element) {
          return;
        }
        let isSelected = $(this.element).val() === Drupal.homeBranch.getValue('id');
        this.element.prop('checked', isSelected);
        $(this.labelSelector).text(isSelected ? this.selectedText : this.notSelectedText);

        this.wrapper.removeClass(this.selectedClass);
        if (isSelected) {
          this.wrapper.addClass(this.selectedClass);
        }
      },
      handleChangeLink: function () {
        $('.hb-branch-selector-change').on('click', function () {
          Drupal.homeBranch.showModal(true);
          return false;
        });
      },
      onChange: function (event, el) {
        if ($(el).attr('id') === this.element.attr('id')) {
          // Save selected value in сookies storage.
          let id = ($(el).is(':checked')) ? $(el).val() : null;
          Drupal.homeBranch.setId(id);
        }
      },
      addMarkup: function (context) {
        let id = context.data('hb-id');
        let branchSelector = $(this.placeholderSelector, context);

        // Replace branch selector implementation by home branch alternative.
        branchSelector.each(function () {
          $(this).append('<div class="hb-branch-selector">' +
            '<div class="hb-location-checkbox-wrapper">' +
              '<span class="hb-checkbox-wrapper">' +
                '<input type="checkbox" value="' + id + '" id="hb-location-checkbox-' + id + '" class="hb-location-checkbox hb-location-checkbox-' + id + '">' +
                '<label for="hb-location-checkbox-' + id + '">' + this.selectedText + '</label>' +
              '</span>' +
            '</div>' +
            '<div class="hb-branch-selector-change-wrapper"><a class="hb-branch-selector-change" href="#">Change</a></div>' +
            '</div>');
        });
        // Save created element in plugin.
        this.element = $('.hb-location-checkbox-' + id);
        this.wrapper = $('.hb-branch-selector');
        this.handleChangeLink();
      }
    }
  });

})(jQuery, Drupal, drupalSettings);
