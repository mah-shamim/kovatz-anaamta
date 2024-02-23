(function($) {
  const install_button = $('.product-addons-button-install');
  const switch_cc_block_button = $( '#wcv-switch-to-classic-cart-checkout' );
  const script_params = window.wcv_admin_script_params;
  install_button.on('click', function(e) {
    e.preventDefault();
    let button = $(this);
    let button_text = button.children('.product-addons-button-text');
    let spinner = button.children('.wcv-loading-spinner');
    let install_staus = button.next('.product-addons-install-status');

    button.attr('disabled', 'disabled');
    button_text.text(script_params.installing_text);
    spinner.addClass('active');

    $.ajax({
      url: ajaxurl,
      type: 'POST',
      data: {
        action: 'wcv_install_activate_plugin',
        plugin_slug: button.data('plugin_slug'),
        nonce: script_params.install_nonce
      }
    })
      .done(function(response) {
        if (response.success && !response.success) {
          button.removeAttr('disabled');
          spinner.removeClass('active');
          button_text.text(script_params.try_again_text);
          install_staus.text(response.data).addClass('active');
        } else {
          button
            .addClass('installed')
            .removeClass('product-addons-button-install');
          button_text.text(script_params.installed_text);
          spinner.removeClass('active');
          install_staus
            .text(script_params.installed_message)
            .addClass('active');
        }
      })
      .fail(function(error) {
        button.removeAttr('disabled');
        button_text.text(script_params.try_again_text);
        spinner.removeClass('active');
        install_staus.addClass('active').text(error.responseJSON.data);
      });
  });

  switch_cc_block_button.on( 'click', function(e) {
    const nonce  = wcv_admin_script_params.switch_cc_blocks_nonce;
    const notice_container = $('#wcv-switch-to-classic-cart-checkout-notice');
    $.post( ajaxurl, {
      action: 'wcvendors_switch_to_classic_cart_checkout',
      nonce: nonce
    }, function( response ) {
      if ( response.success ) {
          notice_container.addClass('notice-success').removeClass('notice-error').html(`<p>${response.data.message}</p>`).show();
      }
    });
  })

})(jQuery);
