jQuery( function($){
	// run tip tip
	function runTipTip() {
		// remove any lingering tooltips
		$( '#tiptip_holder' ).removeAttr( 'style' );
		$( '#tiptip_arrow' ).removeAttr( 'style' );
		
		// init tiptip
		$( '.tips' ).tipTip({
			'attribute': 'data-tip',
			'fadeIn': 50,
			'fadeOut': 50,
			'delay': 200
		});
	}

	runTipTip();

	// Allow tabbing
	$('#titlediv #title').keyup(function( event ) {
		var code = event.keyCode || event.which;

    	if ( code == '9' && $('#woocommerce-coupon-description').size() > 0 ) {
    		event.stopPropagation();
    		$('#woocommerce-coupon-description').focus();
    		return false;
    	}
	});

	// Coupon type options
	$('select#discount_type').change(function(){

		// Get value
		var select_val = $(this).val();

		if ( select_val == 'fixed_product' || select_val == 'percent_product' ) {
			$('.limit_usage_to_x_items_field').show();
		} else {
			$('.limit_usage_to_x_items_field').hide();
		}

	}).change();

	// Scroll to first checked category - https://github.com/scribu/wp-category-checklist-tree/blob/d1c3c1f449e1144542efa17dde84a9f52ade1739/category-checklist-tree.php
	$(function(){
		$('[id$="-all"] > ul.categorychecklist').each(function() {
			var $list = $(this);
			var $firstChecked = $list.find(':checked').first();

			if ( !$firstChecked.length )
				return;

			var pos_first = $list.find('input').position().top;
			var pos_checked = $firstChecked.position().top;

			$list.closest('.tabs-panel').scrollTop(pos_checked - pos_first + 5);
		});
	});

	// Prevent enter submitting post form
	$("#upsell_product_data").bind("keypress", function(e) {
		if (e.keyCode == 13) return false;
	});

	// Type box
	$('.type_box').appendTo( '#woocommerce-product-data h3.hndle span' );

	$(function(){
		// Prevent inputs in meta box headings opening/closing contents
		$('#woocommerce-product-data h3.hndle').unbind('click.postboxes');

		jQuery('#woocommerce-product-data').on('click', 'h3.hndle', function(event){

			// If the user clicks on some form input inside the h3 the box should not be toggled
			if ( $(event.target).filter('input, option, label, select').length )
				return;

			$('#woocommerce-product-data').toggleClass('closed');
		});
	});

	$(function(){
		jQuery('.wc-metabox > h3').click( function(event){
			$( this ).parent( '.wc-metabox' ).toggleClass( 'closed' ).toggleClass( 'open' );
		});
	});

	// Order emails
	$('#order-emails a.show-order-emails').click(function () {
		if ($('#order-emails-select').is(":hidden")) {
			$('#order-emails-select').slideDown('fast');
			$(this).hide();
		}
		return false;
	});

	$('#order-emails a.hide-order-emails').click(function () {
		$('input[name="order_email[]"]').each( function() { $(this).attr('checked', false) } );

		if ($('#order-emails-select').is(":visible")) {
			$('#order-emails-select').slideUp('fast');
			$('#order-emails a.show-order-emails').show();
		}
		return false;
	});

	// Catalog Visibility
	$('#catalog-visibility .edit-catalog-visibility').click(function () {
		if ($('#catalog-visibility-select').is(":hidden")) {
			$('#catalog-visibility-select').slideDown('fast');
			$(this).hide();
		}
		return false;
	});
	$('#catalog-visibility .save-post-visibility').click(function () {
		$('#catalog-visibility-select').slideUp('fast');
		$('#catalog-visibility .edit-catalog-visibility').show();

		var value = $('input[name=_visibility]:checked').val();
		var label = $('input[name=_visibility]:checked').attr('data-label');

		if ( $('input[name=_featured]').is(':checked') ) {
			label = label + ', ' + woocommerce_admin_meta_boxes.featured_label
			$('input[name=_featured]').attr('checked', 'checked');
		}

		$('#catalog-visibility-display').text( label );
		return false;
	});
	$('#catalog-visibility .cancel-post-visibility').click(function () {
		$('#catalog-visibility-select').slideUp('fast');
		$('#catalog-visibility .edit-catalog-visibility').show();

		var current_visibility = $('#current_visibility').val();
		var current_featured = $('#current_featured').val();

		$('input[name=_visibility]').removeAttr('checked');
		$('input[name=_visibility][value=' + current_visibility + ']').attr('checked', 'checked');

		var label = $('input[name=_visibility]:checked').attr('data-label');

		if ( current_featured == 'yes' ) {
			label = label + ', ' + woocommerce_admin_meta_boxes.featured_label
			$('input[name=_featured]').attr('checked', 'checked');
		} else {
			$('input[name=_featured]').removeAttr('checked');
		}

		$('#catalog-visibility-display').text( label );
		return false;
	});

	// TABS
	$('ul.wc-tabs').show();
	$('div.panel-wrap').each(function(){
		$(this).find('div.panel:not(:first)').hide();
	});
	$('ul.wc-tabs a').click(function(){
		var panel_wrap =  $(this).closest('div.panel-wrap');
		$('ul.wc-tabs li', panel_wrap).removeClass('active');
		$(this).parent().addClass('active');
		$('div.panel', panel_wrap).hide();
		$( $(this).attr('href') ).show();
		return false;
	});

	// Chosen selects
	jQuery("select.chosen_select").chosen();

	jQuery("select.chosen_select_nostd").chosen({
		allow_single_deselect: 'true'
	});

	// Ajax Chosen Product Selectors
	jQuery("select.ajax_chosen_select_products").ajaxChosen({
	    method: 	'GET',
	    url: 		woocommerce_admin_meta_boxes.ajax_url,
	    dataType: 	'json',
	    afterTypeDelay: 100,
	    data:		{
	    	action: 		'woocommerce_json_search_products',
			security: 		woocommerce_admin_meta_boxes.search_products_nonce
	    }
	}, function (data) {

		var terms = {};

	    $.each(data, function (i, val) {
	        terms[i] = val;
	    });

	    return terms;
	});

	jQuery("select.ajax_chosen_select_products_and_variations").ajaxChosen({
	    method: 	'GET',
	    url: 		woocommerce_admin_meta_boxes.ajax_url,
	    dataType: 	'json',
	    afterTypeDelay: 100,
	    data:		{
	    	action: 		'woocommerce_json_search_products_and_variations',
			security: 		woocommerce_admin_meta_boxes.search_products_nonce
	    }
	}, function (data) {

		var terms = {};

	    $.each(data, function (i, val) {
	        terms[i] = val;
	    });

	    return terms;
	});

	jQuery("select.ajax_chosen_select_downloadable_products_and_variations").ajaxChosen({
	    method: 	'GET',
	    url: 		woocommerce_admin_meta_boxes.ajax_url,
	    dataType: 	'json',
	    afterTypeDelay: 100,
	    data:		{
	    	action: 		'woocommerce_json_search_downloadable_products_and_variations',
			security: 		woocommerce_admin_meta_boxes.search_products_nonce
	    }
	}, function (data) {

		var terms = {};

	    $.each(data, function (i, val) {
	        terms[i] = val;
	    });

	    return terms;
	});

	// ORDERS
	jQuery('#woocommerce-order-actions input, #woocommerce-order-actions a').click(function(){
		window.onbeforeunload = '';
	});

	$('a.edit_address').click(function(event){
		$(this).hide();
		$(this).closest('.order_data_column').find('div.address').hide();
		$(this).closest('.order_data_column').find('div.edit_address').show();
		event.preventDefault();
	});

	$('#order_items_list').on( 'init_row', 'tr.item', function() {
		var $row = $(this);
		var $qty = $row.find('input.quantity');
		var qty = $qty.val();

		var line_subtotal 	= accounting.unformat( $row.find('input.line_subtotal').val(), woocommerce_admin.mon_decimal_point );
		var line_total 		= accounting.unformat( $row.find('input.line_total').val(), woocommerce_admin.mon_decimal_point );
		var line_tax 		= accounting.unformat( $row.find('input.line_tax').val(), woocommerce_admin.mon_decimal_point );
		var line_subtotal_tax = accounting.unformat( $row.find('input.line_subtotal_tax').val(), woocommerce_admin.mon_decimal_point );

		if ( qty ) {
			unit_subtotal 		= parseFloat( accounting.toFixed( ( line_subtotal / qty ), woocommerce_admin_meta_boxes.rounding_precision ) );
			unit_subtotal_tax 	= parseFloat( accounting.toFixed( ( line_subtotal_tax / qty ), woocommerce_admin_meta_boxes.rounding_precision ) );
			unit_total			= parseFloat( accounting.toFixed( ( line_total / qty ), woocommerce_admin_meta_boxes.rounding_precision ) );
			unit_total_tax		= parseFloat( accounting.toFixed( ( line_tax / qty ), woocommerce_admin_meta_boxes.rounding_precision ) );
		} else {
			unit_subtotal = unit_subtotal_tax = unit_total = unit_total_tax = 0;
		}

		$qty.attr( 'data-o_qty', qty );
		$row.attr( 'data-unit_subtotal', unit_subtotal );
		$row.attr( 'data-unit_subtotal_tax', unit_subtotal_tax );
		$row.attr( 'data-unit_total', unit_total );
		$row.attr( 'data-unit_total_tax', unit_total_tax );
	});

	// When the page is loaded, store the unit costs
	$('#order_items_list tr.item').each( function() {
		$(this).trigger('init_row');
		$(this).find('.edit').hide();
	} );

	$('#order_items_list').on( 'click', 'a.edit_order_item', function() {
		$(this).closest('tr').find('.view').hide();
		$(this).closest('tr').find('.edit').show();
		$(this).hide();
		return false;
	} );

	// When the qty is changed, increase or decrease costs
	$('#order_items_list').on( 'change', 'input.quantity', function() {
		var $row = $(this).closest('tr.item');
		var qty = $(this).val();

		var unit_subtotal 		= $row.attr('data-unit_subtotal');
		var unit_subtotal_tax 	= $row.attr('data-unit_subtotal_tax');
		var unit_total 			= $row.attr('data-unit_total');
		var unit_total_tax = $row.attr('data-unit_total_tax');
		var o_qty 				= $(this).attr('data-o_qty');

		var subtotal  = parseFloat( accounting.formatNumber( unit_subtotal * qty, woocommerce_admin_meta_boxes.rounding_precision, '' ) );
		var tax       = parseFloat( accounting.formatNumber( unit_subtotal_tax * qty, woocommerce_admin_meta_boxes.rounding_precision, '' ) );
		var total     = parseFloat( accounting.formatNumber( unit_total * qty, woocommerce_admin_meta_boxes.rounding_precision, '' ) );
		var total_tax = parseFloat( accounting.formatNumber( unit_total_tax * qty, woocommerce_admin_meta_boxes.rounding_precision, '' ) );

		subtotal  = subtotal.toString().replace( '.', woocommerce_admin.mon_decimal_point );
		tax       = tax.toString().replace( '.', woocommerce_admin.mon_decimal_point );
		total     = total.toString().replace( '.', woocommerce_admin.mon_decimal_point );
		total_tax = total_tax.toString().replace( '.', woocommerce_admin.mon_decimal_point );

		$row.find('input.line_subtotal').val( subtotal );
		$row.find('input.line_total').val( total );
		$row.find('input.line_subtotal_tax').val( tax );
		$row.find('input.line_tax').val( total_tax );

		$(this).trigger('quantity_changed');
	});

	// When subtotal is changed, update the unit costs
	$('#order_items_list').on( 'change', 'input.line_subtotal', function() {
		var $row = $(this).closest('tr.item');
		var $qty = $row.find('input.quantity');
		var qty = $qty.val();
		var value = ( qty ) ? accounting.toFixed( ( $(this).val() / qty ), woocommerce_admin_meta_boxes.rounding_precision ) : 0;

		$row.attr( 'data-unit_subtotal', value );
	});

	// When total is changed, update the unit costs + discount amount
	$('#order_items_list').on( 'change', 'input.line_total', function() {
		var $row = $(this).closest('tr.item');
		var $qty = $row.find('input.quantity');
		var qty = $qty.val();
		var value = ( qty ) ? accounting.toFixed( ( $(this).val() / qty ), woocommerce_admin_meta_boxes.rounding_precision ) : 0;

		$row.attr( 'data-unit_total', value );
	});

	// When total is changed, update the unit costs + discount amount
	$('#order_items_list').on( 'change', 'input.line_subtotal_tax', function() {
		var $row = $(this).closest('tr.item');
		var $qty = $row.find('input.quantity');
		var qty = $qty.val();
		var value = ( qty ) ? accounting.toFixed( ( $(this).val() / qty ), woocommerce_admin_meta_boxes.rounding_precision ) : 0;

		$row.attr( 'data-unit_subtotal_tax', value );
	});

	// When total is changed, update the unit costs + discount amount
	$('#order_items_list').on( 'change', 'input.line_tax', function() {
		var $row = $(this).closest('tr.item');
		var $qty = $row.find('input.quantity');
		var qty = $qty.val();
		var value = ( qty ) ? accounting.toFixed( ( $(this).val() / qty ), woocommerce_admin_meta_boxes.rounding_precision ) : 0;

		$row.attr( 'data-unit_total_tax', value );
	});

	// Display a total for taxes
	$('#woocommerce-order-totals').on( 'change input', '.order_taxes_amount, .order_taxes_shipping_amount, .shipping_cost, #_order_discount', function() {

		var $this  =  $(this);
		var fields = $this.closest('.totals_group').find('input[type=number], .wc_input_price');
		var total  = 0;

		fields.each(function(){
			if ( $(this).val() )
				total = total + accounting.unformat( $(this).val(), woocommerce_admin.mon_decimal_point );
		});

		if ( $this.is('.order_taxes_amount') || $this.is('.order_taxes_shipping_amount') ) {
			total = round( total, woocommerce_admin_meta_boxes.currency_format_num_decimals, woocommerce_admin_meta_boxes.tax_rounding_mode );
		}

		var formatted_total = accounting.formatMoney( total, {
			symbol 		: woocommerce_admin_meta_boxes.currency_format_symbol,
			decimal 	: woocommerce_admin_meta_boxes.currency_format_decimal_sep,
			thousand	: woocommerce_admin_meta_boxes.currency_format_thousand_sep,
			precision 	: woocommerce_admin_meta_boxes.currency_format_num_decimals,
			format		: woocommerce_admin_meta_boxes.currency_format
		} );

		$this.closest('.totals_group').find('span.inline_total').text( formatted_total );
	} );

	$('span.inline_total').closest('.totals_group').find('input').change();

	// Calculate totals
	$('button.calc_line_taxes').click(function(){
		// Block write panel
		$('.woocommerce_order_items_wrapper').block({ message: null, overlayCSS: { background: '#fff url(' + woocommerce_admin_meta_boxes.plugin_url + '/assets/images/ajax-loader.gif) no-repeat center', opacity: 0.6 } });

		var answer = confirm(woocommerce_admin_meta_boxes.calc_line_taxes);

		if (answer) {

			var $items = $('#order_items_list').find('tr.item, tr.fee');

			var shipping_country = $('#_shipping_country').val();
			var billing_country = $('#_billing_country').val();

			if (shipping_country) {
				var country = shipping_country;
				var state = $('#_shipping_state').val();
				var postcode = $('#_shipping_postcode').val();
				var city = $('#_shipping_city').val();
			} else if(billing_country) {
				var country = billing_country;
				var state = $('#_billing_state').val();
				var postcode = $('#_billing_postcode').val();
				var city = $('#_billing_city').val();
			} else {
				var country = woocommerce_admin_meta_boxes.base_country;
				var state = '';
				var postcode = '';
				var city = '';
			}

			// Get items and values
			var calculate_items = {};

			$items.each( function() {

				var $row = $(this);

				var item_id 		= $row.find('input.order_item_id').val();
				var line_subtotal	= $row.find('input.line_subtotal').val();
				var line_total		= $row.find('input.line_total').val();
				var tax_class		= $row.find('select.tax_class').val();

				calculate_items[ item_id ] = {};
				calculate_items[ item_id ].line_subtotal = line_subtotal;
				calculate_items[ item_id ].line_total = line_total;
				calculate_items[ item_id ].tax_class = tax_class;
			} );

			order_shipping = 0;

			$('#shipping_rows').find('input[type=number], .wc_input_price').each(function(){
				cost = $(this).val() || '0';
				cost = accounting.unformat( cost, woocommerce_admin.mon_decimal_point );
				order_shipping = order_shipping + parseFloat( cost );
			});

			var data = {
				action: 		'woocommerce_calc_line_taxes',
				order_id: 		woocommerce_admin_meta_boxes.post_id,
				items:			calculate_items,
				shipping:		order_shipping,
				country:		country,
				state:			state,
				postcode:		postcode,
				city:			city,
				security: 		woocommerce_admin_meta_boxes.calc_totals_nonce
			};

			$.post( woocommerce_admin_meta_boxes.ajax_url, data, function( response ) {

				if ( response ) {
					$items.each( function() {
						var $row = $(this);
						var item_id = $row.find('input.order_item_id').val();
						$row.find('.edit_order_item').click();

						if ( response['item_taxes'][ item_id ] ) {
							$row.find('input.line_tax').val( response['item_taxes'][ item_id ]['line_tax'] ).change();
							$row.find('input.line_subtotal_tax').val( response['item_taxes'][ item_id ]['line_subtotal_tax'] ).change();
						}

						if ( response['tax_row_html'] )
							$('#tax_rows').empty().append( response['tax_row_html'] );
					} );

					$('#tax_rows').find('input').change();
				}

				$('.woocommerce_order_items_wrapper').unblock();
			});

		} else {
			$('.woocommerce_order_items_wrapper').unblock();
		}
		return false;
	});


	$('button.calc_totals').click( function(){
		// Block write panel
		$('#woocommerce-order-totals').block({ message: null, overlayCSS: { background: '#fff url(' + woocommerce_admin_meta_boxes.plugin_url + '/assets/images/ajax-loader.gif) no-repeat center', opacity: 0.6 } });

		var answer = confirm(woocommerce_admin_meta_boxes.calc_totals);

		if (answer) {

			// Get row totals
			var line_totals 		= 0;
			var tax      			= 0;
			var shipping      		= 0;
			var order_discount		= $('#_order_discount').val() || '0';

			order_discount = accounting.unformat( order_discount.replace(',', '.') );

			$('#shipping_rows').find('input[type=number], .wc_input_price').each(function(){
				cost = $(this).val() || '0';
				cost = accounting.unformat( cost, woocommerce_admin.mon_decimal_point );
				shipping = shipping + parseFloat( cost );
			});

			$('#tax_rows').find('input[type=number], .wc_input_price').each(function(){
				cost = $(this).val() || '0';
				cost = accounting.unformat( cost, woocommerce_admin.mon_decimal_point );
				tax = tax + parseFloat( cost );
			});

			$('#order_items_list tr.item, #order_items_list tr.fee').each(function(){
				line_total 	= $(this).find('input.line_total').val() || '0';
				line_totals = line_totals + accounting.unformat( line_total.replace(',', '.') );
			});

			// Tax
			if ( woocommerce_admin_meta_boxes.round_at_subtotal == 'yes' )
				tax = parseFloat( accounting.toFixed( tax, woocommerce_admin_meta_boxes.rounding_precision ) );

			// Set Total
			$('#_order_total').val( accounting.formatNumber( line_totals + tax + shipping - order_discount, woocommerce_admin_meta_boxes.currency_format_num_decimals, '', woocommerce_admin.mon_decimal_point ) ).change();
		}

		$('#woocommerce-order-totals').unblock();

		return false;
	});

	// Add a line item
	$('#woocommerce-order-items button.add_order_item').click(function(){

		var add_item_ids = $('select#add_item_id').val();

		if ( add_item_ids ) {

			count = add_item_ids.length;

			$('table.woocommerce_order_items').block({ message: null, overlayCSS: { background: '#fff url(' + woocommerce_admin_meta_boxes.plugin_url + '/assets/images/ajax-loader.gif) no-repeat center', opacity: 0.6 } });

			$.each( add_item_ids, function( index, value ) {

				var data = {
					action: 		'woocommerce_add_order_item',
					item_to_add: 	value,
					order_id:		woocommerce_admin_meta_boxes.post_id,
					security: 		woocommerce_admin_meta_boxes.order_item_nonce
				};

				$.post( woocommerce_admin_meta_boxes.ajax_url, data, function( response ) {

					$('table.woocommerce_order_items tbody#order_items_list').append( response );

					if (!--count) {
						$('select#add_item_id, #add_item_id_chosen .chosen-choices').css('border-color', '').val('');
					   	
					   	runTipTip();

					    $('select#add_item_id').trigger("chosen:updated");
					    $('table.woocommerce_order_items').unblock();
					}

					$('#order_items_list tr.new_row').trigger('init_row').removeClass('new_row');
				});

			});

		} else {
			$('select#add_item_id, #add_item_id_chosen .chosen-choices').css('border-color', 'red');
		}
		return false;
	});

	// Add a fee
	$('#woocommerce-order-items button.add_order_fee').click(function(){

		$('table.woocommerce_order_items').block({ message: null, overlayCSS: { background: '#fff url(' + woocommerce_admin_meta_boxes.plugin_url + '/assets/images/ajax-loader.gif) no-repeat center', opacity: 0.6 } });

		var data = {
			action: 		'woocommerce_add_order_fee',
			order_id:		woocommerce_admin_meta_boxes.post_id,
			security: 		woocommerce_admin_meta_boxes.order_item_nonce
		};

		$.post( woocommerce_admin_meta_boxes.ajax_url, data, function( response ) {
			$('table.woocommerce_order_items tbody#order_items_list').append( response );
			$('table.woocommerce_order_items').unblock();
		});
		return false;
	});

	// Add some meta to a line item
	$('#order_items_list').on('click', 'button.add_order_item_meta', function(){

		var $button = $(this);
		var $item = $button.closest('tr.item');

		var data = {
			order_item_id: 	$item.attr( 'data-order_item_id' ),
			action: 	'woocommerce_add_order_item_meta',
			security: 	woocommerce_admin_meta_boxes.order_item_nonce
		};

		$('table.woocommerce_order_items').block({ message: null, overlayCSS: { background: '#fff url(' + woocommerce_admin_meta_boxes.plugin_url + '/assets/images/ajax-loader.gif) no-repeat center', opacity: 0.6 } });

		$.ajax( {
			url: woocommerce_admin_meta_boxes.ajax_url,
			data: data,
			type: 'POST',
			success: function( response ) {
				$item.find('tbody.meta_items').append( response );
				$('table.woocommerce_order_items').unblock();
			}
		} );

		return false;
	});

	$('#order_items_list').on('click', 'button.remove_order_item_meta', function(){
		var answer = confirm( woocommerce_admin_meta_boxes.remove_item_meta )
		if ( answer ) {
			var $row = $(this).closest('tr');

			var data = {
				meta_id: 			$row.attr( 'data-meta_id' ),
				action: 			'woocommerce_remove_order_item_meta',
				security: 			woocommerce_admin_meta_boxes.order_item_nonce
			};

			$('table.woocommerce_order_items').block({ message: null, overlayCSS: { background: '#fff url(' + woocommerce_admin_meta_boxes.plugin_url + '/assets/images/ajax-loader.gif) no-repeat center', opacity: 0.6 } });

			$.ajax( {
				url: woocommerce_admin_meta_boxes.ajax_url,
				data: data,
				type: 'POST',
				success: function( response ) {
					$row.hide();
					$('table.woocommerce_order_items').unblock();
				}
			} );
		}
		return false;
	});

	// Bulk actions for line items
	$('#woocommerce-order-items').on( 'click', 'input.check-column', function() {
		if ( $(this).is(':checked') )
			$('#woocommerce-order-items').find('.check-column input').attr('checked', 'checked');
		else
			$('#woocommerce-order-items').find('.check-column input').removeAttr('checked');
	} );

	$('#woocommerce-order-items').on( 'click', '.do_bulk_action', function() {

		var action = $(this).closest('.bulk_actions').find('select').val();
		var selected_rows = $('#woocommerce-order-items').find('.check-column input:checked');
		var item_ids = [];

		$(selected_rows).each( function() {

			var $item = $(this).closest('tr.item, tr.fee');

			item_ids.push( $item.attr( 'data-order_item_id' ) );

		} );

		if ( item_ids.length == 0 ) {
			alert( woocommerce_admin_meta_boxes.i18n_select_items );
			return;
		}

		if ( action == 'delete' ) {

			var answer = confirm( woocommerce_admin_meta_boxes.remove_item_notice );

			if ( answer ) {

				$('table.woocommerce_order_items').block({ message: null, overlayCSS: { background: '#fff url(' + woocommerce_admin_meta_boxes.plugin_url + '/assets/images/ajax-loader.gif) no-repeat center', opacity: 0.6 } });

				var data = {
					order_item_ids: 	item_ids,
					action: 			'woocommerce_remove_order_item',
					security: 			woocommerce_admin_meta_boxes.order_item_nonce
				};

				$.ajax( {
					url: woocommerce_admin_meta_boxes.ajax_url,
					data: data,
					type: 'POST',
					success: function( response ) {
						$(selected_rows).each( function() {
							$(this).closest('tr.item, tr.fee').remove();
						} );
						$('table.woocommerce_order_items').unblock();
					}
				} );

			}

		} else if ( action == 'reduce_stock' ) {

			$('table.woocommerce_order_items').block({ message: null, overlayCSS: { background: '#fff url(' + woocommerce_admin_meta_boxes.plugin_url + '/assets/images/ajax-loader.gif) no-repeat center', opacity: 0.6 } });

			var quantities = {};

			$(selected_rows).each( function() {

				var $item = $(this).closest('tr.item, tr.fee');
				var $qty  = $item.find('input.quantity');

				quantities[ $item.attr( 'data-order_item_id' ) ] = $qty.val();
			} );

			var data = {
				order_id:			woocommerce_admin_meta_boxes.post_id,
				order_item_ids: 	item_ids,
				order_item_qty: 	quantities,
				action: 			'woocommerce_reduce_order_item_stock',
				security: 			woocommerce_admin_meta_boxes.order_item_nonce
			};

			$.ajax( {
				url: woocommerce_admin_meta_boxes.ajax_url,
				data: data,
				type: 'POST',
				success: function( response ) {
					alert( response );
					$('table.woocommerce_order_items').unblock();
				}
			} );

		} else if ( action == 'increase_stock' ) {

			$('table.woocommerce_order_items').block({ message: null, overlayCSS: { background: '#fff url(' + woocommerce_admin_meta_boxes.plugin_url + '/assets/images/ajax-loader.gif) no-repeat center', opacity: 0.6 } });

			var quantities = {};

			$(selected_rows).each( function() {

				var $item = $(this).closest('tr.item, tr.fee');
				var $qty  = $item.find('input.quantity');

				quantities[ $item.attr( 'data-order_item_id' ) ] = $qty.val();
			} );

			var data = {
				order_id:			woocommerce_admin_meta_boxes.post_id,
				order_item_ids: 	item_ids,
				order_item_qty: 	quantities,
				action: 			'woocommerce_increase_order_item_stock',
				security: 			woocommerce_admin_meta_boxes.order_item_nonce
			};

			$.ajax( {
				url: woocommerce_admin_meta_boxes.ajax_url,
				data: data,
				type: 'POST',
				success: function( response ) {
					alert( response );
					$('table.woocommerce_order_items').unblock();
				}
			} );
		}

		return false;
	} );

	// Download permissions
	$('.order_download_permissions').on('click', 'button.grant_access', function(){
		var products = $('select#grant_access_id').val();
			if (!products) return;

		$('.order_download_permissions').block({ message: null, overlayCSS: { background: '#fff url(' + woocommerce_admin_meta_boxes.plugin_url + '/assets/images/ajax-loader.gif) no-repeat center', opacity: 0.6 } });

		var data = {
			action: 		'woocommerce_grant_access_to_download',
			product_ids: 	products,
			loop:			$('.order_download_permissions .wc-metabox').size(),
			order_id: 		woocommerce_admin_meta_boxes.post_id,
			security: 		woocommerce_admin_meta_boxes.grant_access_nonce,
		};

		$.post( woocommerce_admin_meta_boxes.ajax_url, data, function( response ) {

			if ( response ) {
				$('.order_download_permissions .wc-metaboxes').append( response );
			} else {
				alert( woocommerce_admin_meta_boxes.i18n_download_permission_fail );
			}

			$( ".date-picker" ).datepicker({
				dateFormat: "yy-mm-dd",
				numberOfMonths: 1,
				showButtonPanel: true,
				showOn: "button",
				buttonImage: woocommerce_admin_meta_boxes.calendar_image,
				buttonImageOnly: true
			});
			$('#grant_access_id').val('').trigger('chosen:updated');
			$('.order_download_permissions').unblock();

		});

		return false;
	});

	$('.order_download_permissions').on('click', 'button.revoke_access', function(e){
		e.preventDefault();
		var answer = confirm( woocommerce_admin_meta_boxes.i18n_permission_revoke );
		if ( answer ) {
			var el = $(this).parent().parent();
			var product = $(this).attr('rel').split(",")[0];
			var file = $(this).attr('rel').split(",")[1];

			if ( product > 0 ) {
				$(el).block({ message: null, overlayCSS: { background: '#fff url(' + woocommerce_admin_meta_boxes.plugin_url + '/assets/images/ajax-loader.gif) no-repeat center', opacity: 0.6 } });

				var data = {
					action: 		'woocommerce_revoke_access_to_download',
					product_id: 	product,
					download_id:	file,
					order_id: 		woocommerce_admin_meta_boxes.post_id,
					security: 		woocommerce_admin_meta_boxes.revoke_access_nonce,
				};

				$.post( woocommerce_admin_meta_boxes.ajax_url, data, function(response) {
					// Success
					$(el).fadeOut('300', function(){
						$(el).remove();
					});
				});

			} else {
				$(el).fadeOut('300', function(){
					$(el).remove();
				});
			}
		}
		return false;
	});


	$('button.load_customer_billing').click(function(){

		var answer = confirm(woocommerce_admin_meta_boxes.load_billing);
		if (answer){

			// Get user ID to load data for
			var user_id = $('#customer_user').val();

			if (!user_id) {
				alert(woocommerce_admin_meta_boxes.no_customer_selected);
				return false;
			}

			var data = {
				user_id: 			user_id,
				type_to_load: 		'billing',
				action: 			'woocommerce_get_customer_details',
				security: 			woocommerce_admin_meta_boxes.get_customer_details_nonce
			};

			$(this).closest('.edit_address').block({ message: null, overlayCSS: { background: '#fff url(' + woocommerce_admin_meta_boxes.plugin_url + '/assets/images/ajax-loader.gif) no-repeat center', opacity: 0.6 } });

			$.ajax({
				url: woocommerce_admin_meta_boxes.ajax_url,
				data: data,
				type: 'POST',
				success: function( response ) {
					var info = response;

					if (info) {
						$('input#_billing_first_name').val( info.billing_first_name );
						$('input#_billing_last_name').val( info.billing_last_name );
						$('input#_billing_company').val( info.billing_company );
						$('input#_billing_address_1').val( info.billing_address_1 );
						$('input#_billing_address_2').val( info.billing_address_2 );
						$('input#_billing_city').val( info.billing_city );
						$('input#_billing_postcode').val( info.billing_postcode );
						$('#_billing_country').val( info.billing_country );
						$('input#_billing_state').val( info.billing_state );
						$('input#_billing_email').val( info.billing_email );
						$('input#_billing_phone').val( info.billing_phone );
					}

					$('.edit_address').unblock();
				}
			});
		}
		return false;
	});

	$('button.load_customer_shipping').click(function(){

		var answer = confirm(woocommerce_admin_meta_boxes.load_shipping);
		if (answer){

			// Get user ID to load data for
			var user_id = $('#customer_user').val();

			if (!user_id) {
				alert(woocommerce_admin_meta_boxes.no_customer_selected);
				return false;
			}

			var data = {
				user_id: 			user_id,
				type_to_load: 		'shipping',
				action: 			'woocommerce_get_customer_details',
				security: 			woocommerce_admin_meta_boxes.get_customer_details_nonce
			};

			$(this).closest('.edit_address').block({ message: null, overlayCSS: { background: '#fff url(' + woocommerce_admin_meta_boxes.plugin_url + '/assets/images/ajax-loader.gif) no-repeat center', opacity: 0.6 } });

			$.ajax({
				url: woocommerce_admin_meta_boxes.ajax_url,
				data: data,
				type: 'POST',
				success: function( response ) {
					var info = response;

					if (info) {
						$('input#_shipping_first_name').val( info.shipping_first_name );
						$('input#_shipping_last_name').val( info.shipping_last_name );
						$('input#_shipping_company').val( info.shipping_company );
						$('input#_shipping_address_1').val( info.shipping_address_1 );
						$('input#_shipping_address_2').val( info.shipping_address_2 );
						$('input#_shipping_city').val( info.shipping_city );
						$('input#_shipping_postcode').val( info.shipping_postcode );
						$('#_shipping_country').val( info.shipping_country );
						$('input#_shipping_state').val( info.shipping_state );
					}

					$('.edit_address').unblock();
				}
			});
		}
		return false;
	});

	$('button.billing-same-as-shipping').click(function(){
		var answer = confirm(woocommerce_admin_meta_boxes.copy_billing);
		if (answer){
			$('input#_shipping_first_name').val( $('input#_billing_first_name').val() );
			$('input#_shipping_last_name').val( $('input#_billing_last_name').val() );
			$('input#_shipping_company').val( $('input#_billing_company').val() );
			$('input#_shipping_address_1').val( $('input#_billing_address_1').val() );
			$('input#_shipping_address_2').val( $('input#_billing_address_2').val() );
			$('input#_shipping_city').val( $('input#_billing_city').val() );
			$('input#_shipping_postcode').val( $('input#_billing_postcode').val() );
			$('#_shipping_country').val( $('#_billing_country').val() );
			$('input#_shipping_state').val( $('input#_billing_state').val() );
		}
		return false;
	});

	$('.totals_group').on('click','a.add_total_row',function(){
		$(this).closest('.totals_group').find('.total_rows').append( $(this).data( 'row' ) );
		return false;
	});

	$('.total_rows').on('click','a.delete_total_row',function(){
		$row = $(this).closest('.total_row');

		var row_id = $row.attr( 'data-order_item_id' );

		if ( row_id ) {
			$row.append('<input type="hidden" name="delete_order_item_id[]" value="' + row_id + '" />').hide();
		} else {
			$row.remove();
		}

		return false;
	});

	// Order notes
	$('#woocommerce-order-notes').on( 'click', 'a.add_note', function() {
		if ( ! $('textarea#add_order_note').val() ) return;

		$('#woocommerce-order-notes').block({ message: null, overlayCSS: { background: '#fff url(' + woocommerce_admin_meta_boxes.plugin_url + '/assets/images/ajax-loader.gif) no-repeat center', opacity: 0.6 } });
		var data = {
			action: 		'woocommerce_add_order_note',
			post_id:		woocommerce_admin_meta_boxes.post_id,
			note: 			$('textarea#add_order_note').val(),
			note_type:		$('select#order_note_type').val(),
			security: 		woocommerce_admin_meta_boxes.add_order_note_nonce,
		};

		$.post( woocommerce_admin_meta_boxes.ajax_url, data, function(response) {
			$('ul.order_notes').prepend( response );
			$('#woocommerce-order-notes').unblock();
			$('#add_order_note').val('');
		});

		return false;

	});

	$('#woocommerce-order-notes').on( 'click', 'a.delete_note', function() {
		var note = $(this).closest('li.note');
		$(note).block({ message: null, overlayCSS: { background: '#fff url(' + woocommerce_admin_meta_boxes.plugin_url + '/assets/images/ajax-loader.gif) no-repeat center', opacity: 0.6 } });

		var data = {
			action: 		'woocommerce_delete_order_note',
			note_id:		$(note).attr('rel'),
			security: 		woocommerce_admin_meta_boxes.delete_order_note_nonce,
		};

		$.post( woocommerce_admin_meta_boxes.ajax_url, data, function(response) {
			$(note).remove();
		});

		return false;
	});

	// PRODUCT TYPE SPECIFIC OPTIONS
	$('select#product-type').change(function(){

		// Get value
		var select_val = $(this).val();

		if (select_val=='variable') {
			$('input#_manage_stock').change();
			$('input#_downloadable').prop('checked', false);
			$('input#_virtual').removeAttr('checked');
		}

		else if (select_val=='grouped') {
			$('input#_downloadable').prop('checked', false);
			$('input#_virtual').removeAttr('checked');
		}

		else if (select_val=='external') {
			$('input#_downloadable').prop('checked', false);
			$('input#_virtual').removeAttr('checked');
		}

		show_and_hide_panels();

		$('ul.wc-tabs li:visible').eq(0).find('a').click();

		$('body').trigger('woocommerce-product-type-change', select_val, $(this) );

	}).change();

	$('ul.wc-tabs li:visible').eq(0).find('a').click();

	$('input#_downloadable, input#_virtual').change(function(){
		show_and_hide_panels();
	});

	function show_and_hide_panels() {
		var product_type    = $('select#product-type').val();
		var is_virtual      = $('input#_virtual:checked').size();
		var is_downloadable = $('input#_downloadable:checked').size();

		// Hide/Show all with rules
		var hide_classes = '.hide_if_downloadable, .hide_if_virtual';
		var show_classes = '.show_if_downloadable, .show_if_virtual, .show_if_external';

		$.each( woocommerce_admin_meta_boxes.product_types, function( index, value ) {
			hide_classes = hide_classes + ', .hide_if_' + value;
			show_classes = show_classes + ', .show_if_' + value;
		} );

		$( hide_classes ).show();
		$( show_classes ).hide();

		// Shows rules
		if ( is_downloadable ) {
			$('.show_if_downloadable').show();
		}
		if ( is_virtual ) {
			$('.show_if_virtual').show();
		}

        $('.show_if_' + product_type).show();

		// Hide rules
		if ( is_downloadable ) {
			$('.hide_if_downloadable').hide();
		}
		if ( is_virtual ) {
			$('.hide_if_virtual').hide();
		}

		$('.hide_if_' + product_type).hide();

		$('input#_manage_stock').change();
	}


	// Sale price schedule
	$('.sale_price_dates_fields').each(function() {

		var $these_sale_dates = $(this);
		var sale_schedule_set = false;
		var $wrap = $these_sale_dates.closest( 'div, table' );

		$these_sale_dates.find('input').each(function(){
			if ( $(this).val() != '' )
				sale_schedule_set = true;
		});

		if ( sale_schedule_set ) {

			$wrap.find('.sale_schedule').hide();
			$wrap.find('.sale_price_dates_fields').show();

		} else {

			$wrap.find('.sale_schedule').show();
			$wrap.find('.sale_price_dates_fields').hide();

		}

	});

	$('#woocommerce-product-data').on( 'click', '.sale_schedule', function() {
		var $wrap = $(this).closest( 'div, table' );

		$(this).hide();
		$wrap.find('.cancel_sale_schedule').show();
		$wrap.find('.sale_price_dates_fields').show();

		return false;
	});
	$('#woocommerce-product-data').on( 'click', '.cancel_sale_schedule', function() {
		var $wrap = $(this).closest( 'div, table' );

		$(this).hide();
		$wrap.find('.sale_schedule').show();
		$wrap.find('.sale_price_dates_fields').hide();
		$wrap.find('.sale_price_dates_fields').find('input').val('');

		return false;
	});

	// File inputs
	$('#woocommerce-product-data').on('click','.downloadable_files a.insert',function(){
		$(this).closest('.downloadable_files').find('tbody').append( $(this).data( 'row' ) );
		return false;
	});
	$('#woocommerce-product-data').on('click','.downloadable_files a.delete',function(){
		$(this).closest('tr').remove();
		return false;
	});


	// STOCK OPTIONS
	$('input#_manage_stock').change(function(){
		if ($(this).is(':checked')) $('div.stock_fields').show();
		else $('div.stock_fields').hide();
	}).change();


	// DATE PICKER FIELDS
	var dates = $( ".sale_price_dates_fields input" ).datepicker({
		defaultDate: "",
		dateFormat: "yy-mm-dd",
		numberOfMonths: 1,
		showButtonPanel: true,
		showOn: "button",
		buttonImage: woocommerce_admin_meta_boxes.calendar_image,
		buttonImageOnly: true,
		onSelect: function( selectedDate ) {
			var option = $(this).is('#_sale_price_dates_from, .sale_price_dates_from') ? "minDate" : "maxDate";

			var instance = $( this ).data( "datepicker" ),
				date = $.datepicker.parseDate(
					instance.settings.dateFormat ||
					$.datepicker._defaults.dateFormat,
					selectedDate, instance.settings );
			dates.not( this ).datepicker( "option", option, date );
		}
	});

	$( ".date-picker" ).datepicker({
		dateFormat: "yy-mm-dd",
		numberOfMonths: 1,
		showButtonPanel: true,
		showOn: "button",
		buttonImage: woocommerce_admin_meta_boxes.calendar_image,
		buttonImageOnly: true
	});

	$( ".date-picker-field" ).datepicker({
		dateFormat: "yy-mm-dd",
		numberOfMonths: 1,
		showButtonPanel: true,
	});

	// META BOXES

		// Open/close
		jQuery('.wc-metaboxes-wrapper').on('click', '.wc-metabox h3', function(event){
			// If the user clicks on some form input inside the h3, like a select list (for variations), the box should not be toggled
			if ($(event.target).filter(':input, option').length) return;

			jQuery(this).next('.wc-metabox-content').toggle();
		})
		.on('click', '.expand_all', function(event){
			jQuery(this).closest('.wc-metaboxes-wrapper').find('.wc-metabox > table').show();
			return false;
		})
		.on('click', '.close_all', function(event){
			jQuery(this).closest('.wc-metaboxes-wrapper').find('.wc-metabox > table').hide();
			return false;
		});

		jQuery('.wc-metabox.closed').each(function(){
			jQuery(this).find('.wc-metabox-content').hide();
		});

	// ATTRIBUTE TABLES

		// Multiselect attributes
		$(".product_attributes select.multiselect").chosen();

		// Initial order
		var woocommerce_attribute_items = $('.product_attributes').find('.woocommerce_attribute').get();

		woocommerce_attribute_items.sort(function(a, b) {
		   var compA = parseInt($(a).attr('rel'));
		   var compB = parseInt($(b).attr('rel'));
		   return (compA < compB) ? -1 : (compA > compB) ? 1 : 0;
		})
		$(woocommerce_attribute_items).each( function(idx, itm) { $('.product_attributes').append(itm); } );

		function attribute_row_indexes() {
			$('.product_attributes .woocommerce_attribute').each(function(index, el){
				$('.attribute_position', el).val( parseInt( $(el).index('.product_attributes .woocommerce_attribute') ) );
			});
		};

		// Add rows
		$('button.add_attribute').on('click', function(){

			var size = $('.product_attributes .woocommerce_attribute').size();

			var attribute_type = $('select.attribute_taxonomy').val();

			if (!attribute_type) {

				var product_type = $('select#product-type').val();
				if (product_type!='variable') enable_variation = 'style="display:none;"'; else enable_variation = '';

				// Add custom attribute row
				$('.product_attributes').append('<div class="woocommerce_attribute wc-metabox">\
						<h3>\
							<button type="button" class="remove_row button">' + woocommerce_admin_meta_boxes.remove_label + '</button>\
							<div class="handlediv" title="' + woocommerce_admin_meta_boxes.click_to_toggle + '"></div>\
							<strong class="attribute_name"></strong>\
						</h3>\
						<table cellpadding="0" cellspacing="0" class="woocommerce_attribute_data">\
							<tbody>\
								<tr>\
									<td class="attribute_name">\
										<label>' + woocommerce_admin_meta_boxes.name_label + ':</label>\
										<input type="text" class="attribute_name" name="attribute_names[' + size + ']" />\
										<input type="hidden" name="attribute_is_taxonomy[' + size + ']" value="0" />\
										<input type="hidden" name="attribute_position[' + size + ']" class="attribute_position" value="' + size + '" />\
									</td>\
									<td rowspan="3">\
										<label>' + woocommerce_admin_meta_boxes.values_label + ':</label>\
										<textarea name="attribute_values[' + size + ']" cols="5" rows="5" placeholder="' + woocommerce_admin_meta_boxes.text_attribute_tip + '"></textarea>\
									</td>\
								</tr>\
								<tr>\
									<td>\
										<label><input type="checkbox" class="checkbox" ' + ( woocommerce_admin_meta_boxes.default_attribute_visibility ? 'checked="checked"' : '' ) + ' name="attribute_visibility[' + size + ']" value="1" /> ' + woocommerce_admin_meta_boxes.visible_label + '</label>\
									</td>\
								</tr>\
								<tr>\
									<td>\
										<div class="enable_variation show_if_variable" ' + enable_variation + '>\
										<label><input type="checkbox" class="checkbox" ' + ( woocommerce_admin_meta_boxes.default_attribute_variation ? 'checked="checked"' : '' ) + ' name="attribute_variation[' + size + ']" value="1" /> ' + woocommerce_admin_meta_boxes.used_for_variations_label + '</label>\
										</div>\
									</td>\
								</tr>\
							</tbody>\
						</table>\
					</div>');

			} else {

				// Reveal taxonomy row
				var thisrow = $('.product_attributes .woocommerce_attribute.' + attribute_type);
				$('.product_attributes').append( $(thisrow) );
				$(thisrow).show().find('.woocommerce_attribute_data').show();
				attribute_row_indexes();

			}

			$('select.attribute_taxonomy').val('');
		});

		$('.product_attributes').on('blur', 'input.attribute_name', function(){
			$(this).closest('.woocommerce_attribute').find('strong.attribute_name').text( $(this).val() );
		});

		$('.product_attributes').on('click', 'button.select_all_attributes', function(){
			$(this).closest('td').find('select option').attr("selected","selected");
			$(this).closest('td').find('select').trigger("chosen:updated");
			return false;
		});

		$('.product_attributes').on('click', 'button.select_no_attributes', function(){
			$(this).closest('td').find('select option').removeAttr("selected");
			$(this).closest('td').find('select').trigger("chosen:updated");
			return false;
		});

		$('.product_attributes').on('click', 'button.remove_row', function() {
			var answer = confirm(woocommerce_admin_meta_boxes.remove_attribute);
			if (answer){
				var $parent = $(this).parent().parent();

				if ($parent.is('.taxonomy')) {
					$parent.find('select, input[type=text]').val('');
					$parent.hide();
				} else {
					$parent.find('select, input[type=text]').val('');
					$parent.hide();
					attribute_row_indexes();
				}
			}
			return false;
		});

		// Attribute ordering
		$('.product_attributes').sortable({
			items:'.woocommerce_attribute',
			cursor:'move',
			axis:'y',
			handle: 'h3',
			scrollSensitivity:40,
			forcePlaceholderSize: true,
			helper: 'clone',
			opacity: 0.65,
			placeholder: 'wc-metabox-sortable-placeholder',
			start:function(event,ui){
				ui.item.css('background-color','#f6f6f6');
			},
			stop:function(event,ui){
				ui.item.removeAttr('style');
				attribute_row_indexes();
			}
		});

		// Add a new attribute (via ajax)
		$('.product_attributes').on('click', 'button.add_new_attribute', function() {

			$('.product_attributes').block({ message: null, overlayCSS: { background: '#fff url(' + woocommerce_admin_meta_boxes.plugin_url + '/assets/images/ajax-loader.gif) no-repeat center', opacity: 0.6 } });

			var attribute = $(this).attr('data-attribute');
			var $wrapper = $(this).closest('.woocommerce_attribute_data');
			var new_attribute_name = prompt( woocommerce_admin_meta_boxes.new_attribute_prompt );

			if ( new_attribute_name ) {

				var data = {
					action: 		'woocommerce_add_new_attribute',
					taxonomy:		attribute,
					term:			new_attribute_name,
					security: 		woocommerce_admin_meta_boxes.add_attribute_nonce
				};

				$.post( woocommerce_admin_meta_boxes.ajax_url, data, function( response ) {

					if ( response.error ) {
						// Error
						alert( response.error );
					} else if ( response.slug ) {
						// Success
						$wrapper.find('select.attribute_values').append('<option value="' + response.slug + '" selected="selected">' + response.name + '</option>');
						$wrapper.find('select.attribute_values').trigger("chosen:updated");
					}

					$('.product_attributes').unblock();

				});

			} else {
				$('.product_attributes').unblock();
			}

			return false;

		});

		// Save attributes and update variations
		$('.save_attributes').on('click', function(){

			$('.product_attributes').block({ message: null, overlayCSS: { background: '#fff url(' + woocommerce_admin_meta_boxes.plugin_url + '/assets/images/ajax-loader.gif) no-repeat center', opacity: 0.6 } });

			var data = {
				post_id: 		woocommerce_admin_meta_boxes.post_id,
				data:			$('.product_attributes').find('input, select, textarea').serialize(),
				action: 		'woocommerce_save_attributes',
				security: 		woocommerce_admin_meta_boxes.save_attributes_nonce
			};

			$.post( woocommerce_admin_meta_boxes.ajax_url, data, function( response ) {

				var this_page = window.location.toString();

				this_page = this_page.replace( 'post-new.php?', 'post.php?post=' + woocommerce_admin_meta_boxes.post_id + '&action=edit&' );

				// Load variations panel
				$('#variable_product_options').block({ message: null, overlayCSS: { background: '#fff url(' + woocommerce_admin_meta_boxes.plugin_url + '/assets/images/ajax-loader.gif) no-repeat center', opacity: 0.6 } });
				$('#variable_product_options').load( this_page + ' #variable_product_options_inner', function() {
					$('#variable_product_options').unblock();
				} );

				$('.product_attributes').unblock();

			});

		});

	// Uploading files
	var downloadable_file_frame;
	var file_path_field;

	jQuery(document).on( 'click', '.upload_file_button', function( event ){

		var $el = $(this);

		file_path_field = $el.closest('tr').find('td.file_url input');

		event.preventDefault();

		// If the media frame already exists, reopen it.
		if ( downloadable_file_frame ) {
			downloadable_file_frame.open();
			return;
		}

		var downloadable_file_states = [
			// Main states.
			new wp.media.controller.Library({
				library:   wp.media.query(),
				multiple:  true,
				title:     $el.data('choose'),
				priority:  20,
				filterable: 'uploaded',
			})
		];

		// Create the media frame.
		downloadable_file_frame = wp.media.frames.downloadable_file = wp.media({
			// Set the title of the modal.
			title: $el.data('choose'),
			library: {
				type: ''
			},
			button: {
				text: $el.data('update'),
			},
			multiple: true,
			states: downloadable_file_states,
		});

		// When an image is selected, run a callback.
		downloadable_file_frame.on( 'select', function() {

			var file_path = '';
			var selection = downloadable_file_frame.state().get('selection');

			selection.map( function( attachment ) {

				attachment = attachment.toJSON();

				if ( attachment.url )
					file_path = attachment.url

			} );

			file_path_field.val( file_path );
		});

		// Set post to 0 and set our custom type
		downloadable_file_frame.on( 'ready', function() {
			downloadable_file_frame.uploader.options.uploader.params = {
				type: 'downloadable_product'
			};
		});

		// Finally, open the modal.
		downloadable_file_frame.open();
	});

	// Download ordering
	jQuery('.downloadable_files tbody').sortable({
		items:'tr',
		cursor:'move',
		axis:'y',
		handle: 'td.sort',
		scrollSensitivity:40,
		forcePlaceholderSize: true,
		helper: 'clone',
		opacity: 0.65,
	});

	// Product gallery file uploads
	var product_gallery_frame;
	var $image_gallery_ids = $('#product_image_gallery');
	var $product_images = $('#product_images_container ul.product_images');

	jQuery('.add_product_images').on( 'click', 'a', function( event ) {
		var $el = $(this);
		var attachment_ids = $image_gallery_ids.val();

		event.preventDefault();

		// If the media frame already exists, reopen it.
		if ( product_gallery_frame ) {
			product_gallery_frame.open();
			return;
		}

		// Create the media frame.
		product_gallery_frame = wp.media.frames.product_gallery = wp.media({
			// Set the title of the modal.
			title: $el.data('choose'),
			button: {
				text: $el.data('update'),
			},
			states : [
				new wp.media.controller.Library({
					title: $el.data('choose'),
					filterable :	'all',
					multiple: true,
				})
			]
		});

		// When an image is selected, run a callback.
		product_gallery_frame.on( 'select', function() {

			var selection = product_gallery_frame.state().get('selection');

			selection.map( function( attachment ) {

				attachment = attachment.toJSON();

				if ( attachment.id ) {
					attachment_ids = attachment_ids ? attachment_ids + "," + attachment.id : attachment.id;

					$product_images.append('\
						<li class="image" data-attachment_id="' + attachment.id + '">\
							<img src="' + attachment.url + '" />\
							<ul class="actions">\
								<li><a href="#" class="delete" title="' + $el.data('delete') + '">' + $el.data('text') + '</a></li>\
							</ul>\
						</li>');
					}

				});

				$image_gallery_ids.val( attachment_ids );
			});

			// Finally, open the modal.
			product_gallery_frame.open();
		});

		// Image ordering
		$product_images.sortable({
			items: 'li.image',
			cursor: 'move',
			scrollSensitivity:40,
			forcePlaceholderSize: true,
			forceHelperSize: false,
			helper: 'clone',
			opacity: 0.65,
			placeholder: 'wc-metabox-sortable-placeholder',
			start:function(event,ui){
				ui.item.css('background-color','#f6f6f6');
			},
			stop:function(event,ui){
				ui.item.removeAttr('style');
			},
			update: function(event, ui) {
				var attachment_ids = '';

				$('#product_images_container ul li.image').css('cursor','default').each(function() {
					var attachment_id = jQuery(this).attr( 'data-attachment_id' );
					attachment_ids = attachment_ids + attachment_id + ',';
				});

				$image_gallery_ids.val( attachment_ids );
			}
		});

		// Remove images
		$('#product_images_container').on( 'click', 'a.delete', function() {
			$(this).closest('li.image').remove();

			var attachment_ids = '';

			$('#product_images_container ul li.image').css('cursor','default').each(function() {
				var attachment_id = jQuery(this).attr( 'data-attachment_id' );
				attachment_ids = attachment_ids + attachment_id + ',';
			});

			$image_gallery_ids.val( attachment_ids );

			runTipTip();

			return false;
		});
});
