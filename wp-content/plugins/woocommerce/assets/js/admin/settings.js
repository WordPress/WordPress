jQuery(window).load(function(){

	// Countries
	jQuery('select#woocommerce_allowed_countries, select#woocommerce_ship_to_countries').change(function(){
		if (jQuery(this).val()=="specific") {
			jQuery(this).parent().parent().next('tr').show();
		} else {
			jQuery(this).parent().parent().next('tr').hide();
		}
	}).change();

	// Color picker
	jQuery('.colorpick').iris( {
		change: function(event, ui){
			jQuery(this).css( { backgroundColor: ui.color.toString() } );
		},
		hide: true,
		border: true
	} ).each( function() {
		jQuery(this).css( { backgroundColor: jQuery(this).val() } );
	})
	.click(function(){
		jQuery('.iris-picker').hide();
		jQuery(this).closest('.color_box, td').find('.iris-picker').show();
	});

	jQuery('body').click(function() {
		jQuery('.iris-picker').hide();
	});

	jQuery('.color_box, .colorpick').click(function(event){
	    event.stopPropagation();
	});

	// Edit prompt
	jQuery(function(){
		var changed = false;

		jQuery('input, textarea, select, checkbox').change(function(){
			changed = true;
		});

		jQuery('.woo-nav-tab-wrapper a').click(function(){
			if (changed) {
				window.onbeforeunload = function() {
				    return woocommerce_settings_params.i18n_nav_warning;
				}
			} else {
				window.onbeforeunload = '';
			}
		});

		jQuery('.submit input').click(function(){
			window.onbeforeunload = '';
		});
	});

	// Sorting
	jQuery('table.wc_gateways tbody, table.wc_shipping tbody').sortable({
		items:'tr',
		cursor:'move',
		axis:'y',
		handle: 'td',
		scrollSensitivity:40,
		helper:function(e,ui){
			ui.children().each(function(){
				jQuery(this).width(jQuery(this).width());
			});
			ui.css('left', '0');
			return ui;
		},
		start:function(event,ui){
			ui.item.css('background-color','#f6f6f6');
		},
		stop:function(event,ui){
			ui.item.removeAttr('style');
		}
	});

	// Chosen selects
	jQuery("select.chosen_select").chosen({
		width: '350px',
		disable_search_threshold: 5
	});

	jQuery("select.chosen_select_nostd").chosen({
		allow_single_deselect: 'true',
		width: '350px',
		disable_search_threshold: 5
	});

	// Select all/none
	jQuery( '.woocommerce' ).on( 'click', '.select_all', function() {
		jQuery(this).closest( 'td' ).find( 'select option' ).attr( "selected", "selected" );
		jQuery(this).closest( 'td' ).find('select').trigger( 'chosen:updated' );
		return false;
	});

	jQuery( '.woocommerce' ).on( 'click', '.select_none', function() {
		jQuery(this).closest( 'td' ).find( 'select option' ).removeAttr( "selected" );
		jQuery(this).closest( 'td' ).find('select').trigger( 'chosen:updated' );
		return false;
	});
});