/* global woocommerce_admin_product_editor */
jQuery( function ( $ ) {
	$( function () {
		var editorWrapper = $( '#postdivrich' );

		if ( editorWrapper.length ) {
			editorWrapper.addClass( 'postbox woocommerce-product-description' );
			editorWrapper.prepend(
				'<h2 class="postbox-header"><label>' +
					woocommerce_admin_product_editor.i18n_description +
					'</label></h2>'
			);
		}
	} );
} );
