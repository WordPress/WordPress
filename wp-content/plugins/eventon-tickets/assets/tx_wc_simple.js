/*---ajax add to cart and popup single product page*/
jQuery(document).ready(function($){ 
    
    // Ajax add to cart
    $(document).on( 'click', '#cart_btn', function() {  

        

        // AJAX add to cart request
        var $thisbutton = $(this); 
        var qty =      $thisbutton.parent().find('input[name=quantity]').val();

        var data = {
            action:         'woocommerce_add_to_cart',
            product_id:     $thisbutton.attr('data-product_id'),
            //quantity:       $thisbutton.attr('data-quantity'),
            quantity:       parseInt(qty),
            security:       woocommerce_params.add_to_cart_nonce
        };

        // Trigger event
        $('body').trigger( 'adding_to_cart', [ $thisbutton, data ] );

        // Ajax action
        $.post( woocommerce_params.ajax_url, data, function( response ) {

            if ( ! response )
                return;

            var this_page = window.location.toString();

            if ( response.error && response.product_url ) {
                window.location = response.product_url;
                return;
            }else{

               $thisbutton.closest('.evo_metarow_tix').find('.tx_wc_notic').show();
            }

            // Redirect to cart option
            if ( woocommerce_params.cart_redirect_after_add == 'yes' ) {

                window.location = woocommerce_params.cart_url;
                return;

            } else {

                $thisbutton.removeClass('loading');

                fragments = response.fragments;
                cart_hash = response.cart_hash;

                // Block fragments class
                if ( fragments ) {
                    $.each(fragments, function(key, value) {
                        $(key).addClass('updating');
                    });
                }

                // Block widgets and fragments
                $('.shop_table.cart, .updating, .cart_totals').fadeTo('400', '0.6').block({message: null, overlayCSS: {background: 'transparent url(' + woocommerce_params.ajax_loader_url + ') no-repeat center', backgroundSize: '16px 16px', opacity: 0.6 } } );

                // Replace fragments
                if ( fragments ) {
                    $.each(fragments, function(key, value) {
                        $(key).replaceWith(value);
                    });
                }

                // Unblock
                $('.widget_shopping_cart, .updating').stop(true).css('opacity', '1').unblock();

                // Cart page elements
                $('.shop_table.cart').load( this_page + ' .shop_table.cart:eq(0) > *', function() {

                    $("div.quantity:not(.buttons_added), td.quantity:not(.buttons_added)").addClass('buttons_added').append('<input type="button" value="+" id="add1" class="plus" />').prepend('<input type="button" value="-" id="minus1" class="minus" />');

                    $('.shop_table.cart').stop(true).css('opacity', '1').unblock();

                    $('body').trigger('cart_page_refreshed');

                });

                // update the popup cart notice
                $("#popup_woocommerce_cart_notice_minimum_amount").html($("#woocommerce_cart_notice_minimum_amount").html());

                $('.cart_totals').load( this_page + ' .cart_totals:eq(0) > *', function() {
                    $('.cart_totals').stop(true).css('opacity', '1').unblock();
                });

                // Trigger event so themes can refresh other areas
                $('body').trigger( 'added_to_cart', [ fragments, cart_hash ] );
                

                // Trigger event so themes can refresh other areas
                $('body').trigger( 'added_to_cart', [ fragments, cart_hash ] );
            }
        });

        return false;

        

    });
});