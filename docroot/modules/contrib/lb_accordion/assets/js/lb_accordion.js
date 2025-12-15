// Import Bootstrap Collapse JS. Will get triggered automagically.
import Collapse from "bootstrap/js/dist/collapse";

(function ($, Drupal){
  // Layout Builder - Accordion behavior
  Drupal.behaviors.lbAccordion = {
    attach: function (context) {
      $(document)
          .on('show.bs.collapse', '.accordion-item', function(e) {
            $('.card-header', this).addClass('active');
          })
          .on('hide.bs.collapse', '.accordion-item', function(e) {
            $('.card-header', this).removeClass('active');
          });
    }
  };
})(jQuery, Drupal);
