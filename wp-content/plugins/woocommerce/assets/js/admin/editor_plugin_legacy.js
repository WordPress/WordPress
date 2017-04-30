/* global tinymce */
( function () {
	tinymce.create( 'tinymce.plugins.WooCommerceShortcodes', {
		init: function( d, e ) {},
		createControl: function( d, e ) {
			var ed = tinymce.activeEditor;

			if ( d === 'wc_shortcodes_button' ) {
				var a = this;

				d = e.createMenuButton( 'wc_shortcodes_button', {
					title: ed.getLang( 'woocommerce.insert' ),
					icons: false
				});

				d.onRenderMenu.add( function ( c, b ) {
					a.addImmediate(b, ed.getLang( 'woocommerce.order_tracking' ), '[' + ed.getLang( 'woocommerce.order_tracking_shortcode') + ']' );
					a.addImmediate(b, ed.getLang( 'woocommerce.price_button' ), '[add_to_cart id="" sku=""]' );
					a.addImmediate(b, ed.getLang( 'woocommerce.product_by_sku' ), '[product id="" sku=""]' );
					a.addImmediate(b, ed.getLang( 'woocommerce.products_by_sku' ), '[products ids="" skus=""]' );
					a.addImmediate(b, ed.getLang( 'woocommerce.product_categories' ), '[product_categories number=""]' );
					a.addImmediate(b, ed.getLang( 'woocommerce.products_by_cat_slug' ), '[product_category category="" per_page="12" columns="4" orderby="date" order="desc"]' );

					b.addSeparator();

					a.addImmediate(b, ed.getLang( 'woocommerce.recent_products' ), '[recent_products per_page="12" columns="4" orderby="date" order="desc"]');
					a.addImmediate(b, ed.getLang( 'woocommerce.featured_products' ), '[featured_products per_page="12" columns="4" orderby="date" order="desc"]' );

					b.addSeparator();

					a.addImmediate(b, ed.getLang( 'woocommerce.shop_messages' ), '[' + ed.getLang( 'woocommerce.shop_messages_shortcode' ) + ']' );
				});

				return d;

			} // End IF Statement

			return null;
		},
		addImmediate: function ( d, e, a ) {
			d.add({
				title: e,
				onclick: function () {
					tinymce.activeEditor.execCommand( 'mceInsertContent', false, a );
				}
			});
		}
	});

	tinymce.PluginManager.add( 'wc_shortcodes_button', tinymce.plugins.WooCommerceShortcodes);
})();
