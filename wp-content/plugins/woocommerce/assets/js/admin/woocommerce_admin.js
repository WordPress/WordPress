/**
 * WooCommerce Admin JS
 */
jQuery(function(){

	// Price input validation
	jQuery('body').on( 'blur', '.wc_input_decimal[type=text], .wc_input_price[type=text]', function() {
			jQuery('.wc_error_tip').fadeOut('100', function(){ jQuery(this).remove(); } );
			return this;
		});

	jQuery('body').on('keyup change', '.wc_input_price[type=text]', function(){
		var value		= jQuery(this).val();
		var regex		= new RegExp( "[^\-0-9\%.\\" + woocommerce_admin.mon_decimal_point + "]+", "gi" );
		var newvalue = value.replace( regex, '' );

		if ( value !== newvalue ) {
			jQuery(this).val( newvalue );
			if ( jQuery(this).parent().find('.wc_error_tip').size() == 0 ) {
				var offset = jQuery(this).position();
				jQuery(this).after( '<div class="wc_error_tip">' + woocommerce_admin.i18n_mon_decimal_error + '</div>' );
				jQuery('.wc_error_tip')
					.css('left', offset.left + jQuery(this).width() - ( jQuery(this).width() / 2 ) - ( jQuery('.wc_error_tip').width() / 2 ) )
					.css('top', offset.top + jQuery(this).height() )
					.fadeIn('100');
			}
		}
		return this;
	});

	jQuery('body').on('keyup change', '.wc_input_decimal[type=text]', function(){
		var value		= jQuery(this).val();
		var regex		= new RegExp( "[^\-0-9\%.\\" + woocommerce_admin.decimal_point + "]+", "gi" );
		var newvalue = value.replace( regex, '' );

		if ( value !== newvalue ) {
			jQuery(this).val( newvalue );
			if ( jQuery(this).parent().find('.wc_error_tip').size() == 0 ) {
				var offset = jQuery(this).position();
				jQuery(this).after( '<div class="wc_error_tip">' + woocommerce_admin.i18n_decimal_error + '</div>' );
				jQuery('.wc_error_tip')
					.css('left', offset.left + jQuery(this).width() - ( jQuery(this).width() / 2 ) - ( jQuery('.wc_error_tip').width() / 2 ) )
					.css('top', offset.top + jQuery(this).height() )
					.fadeIn('100');
			}
		}
		return this;
	});

	jQuery("body").click(function(){
		jQuery('.wc_error_tip').fadeOut('100', function(){ jQuery(this).remove(); } );
	});

	// Tooltips
	jQuery(".tips, .help_tip").tipTip({
		'attribute' : 'data-tip',
		'fadeIn' : 50,
		'fadeOut' : 50,
		'delay' : 200
	});

	// wc_input_table tables
	jQuery('.wc_input_table.sortable tbody').sortable({
		items:'tr',
		cursor:'move',
		axis:'y',
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
		}
	});

	jQuery('.wc_input_table .remove_rows').click(function() {
		var $tbody = jQuery(this).closest('.wc_input_table').find('tbody');
		if ( $tbody.find('tr.current').size() > 0 ) {
			$current = $tbody.find('tr.current');

			$current.each(function(){
				jQuery(this).remove();
			});
		}
		return false;
	});

	var controlled = false;
	var shifted = false;
	var hasFocus = false;

	jQuery(document).bind('keyup keydown', function(e){ shifted = e.shiftKey; controlled = e.ctrlKey || e.metaKey } );

	jQuery('.wc_input_table').on( 'focus click', 'input', function( e ) {

		$this_table = jQuery(this).closest('table');
		$this_row   = jQuery(this).closest('tr');

		if ( ( e.type == 'focus' && hasFocus != $this_row.index() ) || ( e.type == 'click' && jQuery(this).is(':focus') ) ) {

			hasFocus = $this_row.index();

			if ( ! shifted && ! controlled ) {
				jQuery('tr', $this_table).removeClass('current').removeClass('last_selected');
				$this_row.addClass('current').addClass('last_selected');
			} else if ( shifted ) {
				jQuery('tr', $this_table).removeClass('current');
				$this_row.addClass('selected_now').addClass('current');

				if ( jQuery('tr.last_selected', $this_table).size() > 0 ) {
					if ( $this_row.index() > jQuery('tr.last_selected, $this_table').index() ) {
						jQuery('tr', $this_table).slice( jQuery('tr.last_selected', $this_table).index(), $this_row.index() ).addClass('current');
					} else {
						jQuery('tr', $this_table).slice( $this_row.index(), jQuery('tr.last_selected', $this_table).index() + 1 ).addClass('current');
					}
				}

				jQuery('tr', $this_table).removeClass('last_selected');
				$this_row.addClass('last_selected');
			} else {
				jQuery('tr', $this_table).removeClass('last_selected');
				if ( controlled && jQuery(this).closest('tr').is('.current') ) {
					$this_row.removeClass('current');
				} else {
					$this_row.addClass('current').addClass('last_selected');
				}
			}

			jQuery('tr', $this_table).removeClass('selected_now');

		}
	}).on( 'blur', 'input', function( e ) {
		hasFocus = false;
	});

	// Additional cost tables
	jQuery( '.woocommerce_page_wc-settings .shippingrows tbody tr:even' ).addClass( 'alternate' );

	// Availability inputs
	jQuery('select.availability').change(function(){
		if ( jQuery(this).val() == "all" ) {
			jQuery(this).closest('tr').next('tr').hide();
		} else {
			jQuery(this).closest('tr').next('tr').show();
		}
	}).change();

	// Show order items on orders page
	jQuery('body').on( 'click', '.show_order_items', function() {
		jQuery(this).closest('td').find('table').toggle();
		return false;
	});

	// Hidden options
	jQuery('.hide_options_if_checked').each(function(){

		jQuery(this).find('input:eq(0)').change(function() {

			if (jQuery(this).is(':checked')) {
				jQuery(this).closest('fieldset, tr').nextUntil( '.hide_options_if_checked, .show_options_if_checked', '.hidden_option').hide();
			} else {
				jQuery(this).closest('fieldset, tr').nextUntil( '.hide_options_if_checked, .show_options_if_checked', '.hidden_option').show();
			}

		}).change();

	});

	jQuery('.show_options_if_checked').each(function(){

		jQuery(this).find('input:eq(0)').change(function() {

			if (jQuery(this).is(':checked')) {
				jQuery(this).closest('fieldset, tr').nextUntil( '.hide_options_if_checked, .show_options_if_checked', '.hidden_option').show();
			} else {
				jQuery(this).closest('fieldset, tr').nextUntil( '.hide_options_if_checked, .show_options_if_checked', '.hidden_option').hide();
			}

		}).change();

	});

	jQuery('input#woocommerce_demo_store').change(function() {
		if (jQuery(this).is(':checked')) {
			jQuery('#woocommerce_demo_store_notice').closest('tr').show();
		} else {
			jQuery('#woocommerce_demo_store_notice').closest('tr').hide();
		}
	}).change();

	// Attribute term table
	jQuery( 'table.attributes-table tbody tr:nth-child(odd)' ).addClass( 'alternate' );

});