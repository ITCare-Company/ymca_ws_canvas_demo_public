(function ($, Drupal, Cookies) {

  Drupal.behaviors.lbModal = {
    attach: function (context, settings) {
      if ($('#layout-builder').length > 0) {
        return;
      }

      $('.lb-modal-block .modal').each(function (){
        const dismissible = $(this).data('modal-dismissible');
        const id = $(this).attr('id');
        if (dismissible) {
          $(this).on('hide.bs.modal', function (e){
            Cookies.set('lb-modal-' + id + '-dismissed', true);
          });

          if (Cookies.get('lb-modal-' + id + '-dismissed')) {
            return;
          }
        }
        $(this).modal({});
      });
    }
  };
})(jQuery, Drupal, window.Cookies)
