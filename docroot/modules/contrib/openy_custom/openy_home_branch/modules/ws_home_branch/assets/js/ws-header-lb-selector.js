/**
 * @file
 * Location finder extension with Home Branch logic.
 */

(function ($, Drupal, drupalSettings) {

  "use strict";

  /**
   * Add plugin, that related to Location Finder.
   */
  Drupal.homeBranch.plugins.push({
    name: 'hb-selector--link',
    attach: function (settings, context) {
      // Set top position for select branch link on mobile screen.
      $('.hb-selector--link').hbPlugin(settings);
      const header = $('.ws-header', context);
      const resizeObserver = new ResizeObserver(entries => {
        const positionOffset = 9;
        const mobileHeaderHeight = $('.header--top').outerHeight() + $('.header--bottom').outerHeight() + positionOffset;
        $('.hb-selector').css('top', mobileHeaderHeight + 'px');
      });
      resizeObserver.observe(document.querySelector('.header--top'));
    },
    settings: {
      selector: '.hb-selector--link',
      selectorBranch: '.hb-selector--branch--link',
      event: 'click',
      element: null,
      targetSelector: drupalSettings.home_branch.hb_header_lb_selector.targetSelector,
      defaultTitle: drupalSettings.home_branch.hb_header_lb_selector.defaultTitle,
      init: function () {
        if (!this.element) {
          return;
        }
        let selected = Drupal.homeBranch.getValue('id');
        let locations = Drupal.homeBranch.getLocations();
        if (selected) {
          this.element.text(locations[selected]);
          this.element.parent().addClass('selected');
          this.element.attr('href', `${window.location.origin}${drupalSettings.path.baseUrl}/node/` + Drupal.homeBranch.getValue('id'));
          this.element.removeClass('hb-selector--link');
        }
        else {
          this.element.text(this.defaultTitle);
          this.element.parent().removeClass('selected');
          this.element.addClass('hb-selector--link');
          this.element.attr('href', '#');
        }
      },
      onChange: function (event, el) {
        // Show HB locations modal.
        Drupal.homeBranch.showModal(true);
      },
      addMarkup: function (context) {
        let block = $(this.targetSelector);
        block.append('<span class="hb-selector"><i class="fas fa-map-marker-alt"></i><a class="hb-selector--branch--link">' + this.defaultTitle + '</a><a href="#" class="hb-selector--link" ><svg width="9px" height="5px" viewBox="0 0 9 5" xmlns="http://www.w3.org/2000/svg"\n' +
          '             xmlns:xlink="http://www.w3.org/1999/xlink">\n' +
          '          <title>Chevron</title>\n' +
          '          <g id="UI-KIt" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">\n' +
          '            <g id="menu-chevron" transform="translate(-588.000000, -579.154639)"\n' +
          '               fill="var(--wsPartnerColor, black)">\n' +
          '              <g id="Group-4" transform="translate(0.000000, 528.000000)">\n' +
          '                <g id="Group" transform="translate(493.000000, 35.000000)">\n' +
          '                  <path\n' +
          '                    d="M103.765625,16.3629002 C103.617187,16.2240577 103.431641,16.1546392 103.208984,16.1546392 C102.986327,16.1546392 102.796876,16.2240577 102.640625,16.3629002 L99.5,19.4451637 L96.359375,16.3629002 C96.2031242,16.2240577 96.013673,16.1546392 95.7910157,16.1546392 C95.5683582,16.1546392 95.3828132,16.2240577 95.234375,16.3629002 C95.0781242,16.5017414 95,16.6683475 95,16.8627267 C95,17.0571046 95.0781242,17.2237108 95.234375,17.3625532 L98.890625,20.9550556 C98.968751,21.0244764 99.0624995,21.0765411 99.171875,21.1112514 C99.2812505,21.1459618 99.3906245,21.1598457 99.5,21.1529036 C99.6093755,21.1598457 99.7187495,21.1459618 99.828125,21.1112514 C99.9375005,21.0765411 100.031249,21.0244764 100.109375,20.9550556 L103.765625,17.3625532 C103.921876,17.2237108 104,17.0571046 104,16.8627267 C104,16.6683475 103.921876,16.5017414 103.765625,16.3629002 Z"\n' +
          '                    id="Page-1-Copy"></path>\n' +
          '                </g>\n' +
          '              </g>\n' +
          '            </g>\n' +
          '          </g>\n' +
          '        </svg></a></span>');
        // Save created element in plugin.
        this.element = $(this.selectorBranch, block);
      }
    }
  });
})(jQuery, Drupal, drupalSettings);
