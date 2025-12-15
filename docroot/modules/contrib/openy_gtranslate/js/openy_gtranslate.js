/**
 * @file
 * Attaches behavior for the Google Translate widget.
 * Version 2.1.0 - Uses event delegation to work with popups and dynamic content.
 */
(function (Drupal, drupalSettings, once) {
  'use strict';

  // State and callback function remain the same as in v2.0.0.
  Drupal.googleTranslateState = {
    isApiReady: false,
    isScriptInjected: false,
    initQueue: [],
  };

  Drupal.googleTranslateElementInit = function () {
    Drupal.googleTranslateState.isApiReady = true;
    Drupal.googleTranslateState.initQueue.forEach(elementId => {
      new google.translate.TranslateElement({
        pageLanguage: drupalSettings.path.currentLanguage,
        layout: google.translate.TranslateElement.InlineLayout.VERTICAL,
      }, elementId);
    });
    Drupal.googleTranslateState.initQueue = [];
  };

  Drupal.behaviors.googleTranslateSwap = {
    counter: 0,
    attach: function (context) {
      // [CHANGE] We attach ONE handler to document.body, and do this ONLY ONCE.
      once('google-translate-listener', document.body).forEach(body => {
        body.addEventListener('click', (event) => {
          // Find the placeholder, even if clicked on a child element inside it.
          const placeholder = event.target.closest('.openy-gtranslate-placeholder');

          // If the click was not on our placeholder, ignore it.
          if (!placeholder) {
            return;
          }

          // If we're here, the click was on the target element.
          event.preventDefault();

          const parentBlock = placeholder.closest('.block-openy-gtranslate-block');
          if (!parentBlock) { return; }

          const widgetContainer = parentBlock.querySelector('.openy-google-translate');
          if (!widgetContainer) { return; }

          if (!widgetContainer.id) {
            // Use counter from `behaviors` through closure.
            widgetContainer.id = 'openy-gtranslate-widget-' + Drupal.behaviors.googleTranslateSwap.counter++;
          }
          const widgetId = widgetContainer.id;

          // All further initialization logic remains the same as before.
          parentBlock.classList.remove('_none');
          placeholder.classList.add('d-none');
          widgetContainer.classList.remove('d-none');

          if (Drupal.googleTranslateState.isApiReady) {
            new google.translate.TranslateElement({
              pageLanguage: drupalSettings.path.currentLanguage,
              layout: google.translate.TranslateElement.InlineLayout.VERTICAL,
            }, widgetId);
          } else {
            if (!Drupal.googleTranslateState.initQueue.includes(widgetId)) {
              Drupal.googleTranslateState.initQueue.push(widgetId);
            }
            if (!Drupal.googleTranslateState.isScriptInjected) {
              Drupal.googleTranslateState.isScriptInjected = true;
              const script = document.createElement('script');
              script.type = 'text/javascript';
              script.src = '//translate.google.com/translate_a/element.js?cb=Drupal.googleTranslateElementInit';
              document.head.appendChild(script);
            }
          }
        });
      });
    }
  };

}(Drupal, drupalSettings, once));
