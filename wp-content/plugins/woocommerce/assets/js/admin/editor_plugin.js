/* global tinymce */
( function () {
	tinymce.PluginManager.add( 'wc_shortcodes_button', function( editor, url ) {
		var ed = tinymce.activeEditor;
		editor.addButton( 'wc_shortcodes_button', {
			text: false,
			icon: false,
			type: 'menubutton',
			menu: [
				{
					text: ed.getLang( 'woocommerce.order_tracking' ),
					onclick: function() {
						editor.insertContent( '[' + ed.getLang('woocommerce.order_tracking_shortcode') + ']' );
					}
				},
				{
					text: ed.getLang( 'woocommerce.price_button' ),
					onclick: function() {
						editor.insertContent( '[add_to_cart id="" sku=""]' );
					}
				},
				{
					text: ed.getLang( 'woocommerce.product_by_sku' ),
					onclick: function() {
						editor.insertContent( '[product id="" sku=""]' );
					}
				},
				{
					text: ed.getLang( 'woocommerce.products_by_sku' ),
					onclick: function() {
						editor.insertContent( '[products ids="" skus=""]' );
					}
				},
				{
					text: ed.getLang( 'woocommerce.product_categories' ),
					onclick: function() {
						editor.insertContent( '[product_categories number=""]' );
					}
				},
				{
					text: ed.getLang( 'woocommerce.products_by_cat_slug' ),
					onclick: function() {
						editor.insertContent( '[product_category category="" per_page="12" columns="4" orderby="date" order="desc"]' );
					}
				},
				{
					text: ed.getLang( 'woocommerce.recent_products' ),
					onclick: function() {
						editor.insertContent( '[recent_products per_page="12" columns="4" orderby="date" order="desc"]' );
					}
				},
				{
					text: ed.getLang( 'woocommerce.featured_products' ),
					onclick: function() {
						editor.insertContent( '[featured_products per_page="12" columns="4" orderby="date" order="desc"]' );
					}
				},
				{
					text: ed.getLang( 'woocommerce.shop_messages' ),
					onclick: function() {
						editor.insertContent( '[' + ed.getLang('woocommerce.shop_messages_shortcode') + ']' );
					}
				}
			]
		});
	});
})();
