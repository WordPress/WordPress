jQuery(function(){
    jQuery('#the-list').on('click', '.editinline', function(){

		inlineEditPost.revert();

		var post_id = jQuery(this).closest('tr').attr('id');

		post_id = post_id.replace("post-", "");

		var $wc_inline_data = jQuery('#woocommerce_inline_' + post_id );

		var sku 				= $wc_inline_data.find('.sku').text();
		var regular_price 		= $wc_inline_data.find('.regular_price').text();
		var sale_price 			= $wc_inline_data.find('.sale_price').text();
		var weight 				= $wc_inline_data.find('.weight').text();
		var length 				= $wc_inline_data.find('.length').text();
		var width 				= $wc_inline_data.find('.width').text();
		var height	 			= $wc_inline_data.find('.height').text();
		var visibility	 		= $wc_inline_data.find('.visibility').text();
		var stock_status	 	= $wc_inline_data.find('.stock_status').text();
		var stock	 			= $wc_inline_data.find('.stock').text();
		var featured	 		= $wc_inline_data.find('.featured').text();
		var manage_stock		= $wc_inline_data.find('.manage_stock').text();
		var menu_order			= $wc_inline_data.find('.menu_order').text();
		var tax_status			= $wc_inline_data.find('.tax_status').text();
		var tax_class			= $wc_inline_data.find('.tax_class').text();
		var backorders			= $wc_inline_data.find('.backorders').text();

		jQuery('input[name="_sku"]', '.inline-edit-row').val(sku);
		jQuery('input[name="_regular_price"]', '.inline-edit-row').val(regular_price);
		jQuery('input[name="_sale_price"]', '.inline-edit-row').val(sale_price);
		jQuery('input[name="_weight"]', '.inline-edit-row').val(weight);
		jQuery('input[name="_length"]', '.inline-edit-row').val(length);
		jQuery('input[name="_width"]', '.inline-edit-row').val(width);
		jQuery('input[name="_height"]', '.inline-edit-row').val(height);
		jQuery('input[name="_stock"]', '.inline-edit-row').val(stock);
		jQuery('input[name="menu_order"]', '.inline-edit-row').val(menu_order);

		jQuery('select[name="_tax_status"] option[value="' + tax_status + '"]', '.inline-edit-row').attr('selected', 'selected');
		jQuery('select[name="_tax_class"] option[value="' + tax_class + '"]', '.inline-edit-row').attr('selected', 'selected');

		jQuery('select[name="_visibility"] option, select[name="_stock_status"] option, select[name="_backorders"] option').removeAttr('selected');

		jQuery('select[name="_visibility"] option[value="' + visibility + '"]', '.inline-edit-row').attr('selected', 'selected');
		jQuery('select[name="_stock_status"] option[value="' + stock_status + '"]', '.inline-edit-row').attr('selected', 'selected');
		jQuery('select[name="_backorders"] option[value="' + backorders + '"]', '.inline-edit-row').attr('selected', 'selected');

		if (featured=='yes') {
			jQuery('input[name="_featured"]', '.inline-edit-row').attr('checked', 'checked');
		} else {
			jQuery('input[name="_featured"]', '.inline-edit-row').removeAttr('checked');
		}

		if (manage_stock=='yes') {
			jQuery('.stock_qty_field', '.inline-edit-row').show().removeAttr('style');
			jQuery('input[name="_manage_stock"]', '.inline-edit-row').attr('checked', 'checked');
		} else {
			jQuery('.stock_qty_field', '.inline-edit-row').hide();
			jQuery('input[name="_manage_stock"]', '.inline-edit-row').removeAttr('checked');
		}

		// Conditional display
		var product_type		= $wc_inline_data.find('.product_type').text();
		var product_is_virtual	= $wc_inline_data.find('.product_is_virtual').text();

		if (product_type=='simple' || product_type=='external') {
			jQuery('.price_fields', '.inline-edit-row').show().removeAttr('style');
		} else {
			jQuery('.price_fields', '.inline-edit-row').hide();
		}

		if (product_is_virtual=='yes') {
			jQuery('.dimension_fields', '.inline-edit-row').hide();
		} else {
			jQuery('.dimension_fields', '.inline-edit-row').show().removeAttr('style');
		}

		if (product_type=='grouped') {
			jQuery('.stock_fields', '.inline-edit-row').hide();
		} else {
			jQuery('.stock_fields', '.inline-edit-row').show().removeAttr('style');
		}
    });

    jQuery('#the-list').on('change', '.inline-edit-row input[name="_manage_stock"]', function(){

    	if (jQuery(this).is(':checked')) {
    		jQuery('.stock_qty_field', '.inline-edit-row').show().removeAttr('style');
    	} else {
    		jQuery('.stock_qty_field', '.inline-edit-row').hide();
    	}

    });

    jQuery('#wpbody').on('click', '#doaction, #doaction2', function(){
		jQuery('input.text', '.inline-edit-row').val('');
		jQuery('#woocommerce-fields select').prop('selectedIndex',0);
		jQuery('#woocommerce-fields-bulk .inline-edit-group .alignright').hide();
	});

	 jQuery('#wpbody').on('change', '#woocommerce-fields-bulk .inline-edit-group .change_to', function(){

    	if (jQuery(this).val() > 0) {
    		jQuery(this).closest('div').find('.alignright').show();
    	} else {
    		jQuery(this).closest('div').find('.alignright').hide();
    	}

    });

    jQuery('.product_shipping_class-checklist input').change(function(){

    	jQuery(this).closest('li').siblings().find('input:checked').removeAttr('checked');

    });
});
