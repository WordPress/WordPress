jQuery( function ( $ ) {
	/**
	 * Function to check if the attribute and variation fields are empty.
	 */
	jQuery.is_attribute_or_variation_empty = function (
		attributes_and_variations_data
	) {
		var has_empty_fields = false;
		attributes_and_variations_data.each( function () {
			var $this = $( this );
			// Check if the field is checkbox or a search field.
			if (
				$this.hasClass( 'checkbox' ) ||
				$this.filter( '[class*=search__field]' ).length
			) {
				return;
			}

			var is_empty = $this.is( 'select' )
				? $this.find( ':selected' ).length === 0
				: ! $this.val();
			if ( is_empty ) {
				has_empty_fields = true;
			}
		} );
		return has_empty_fields;
	};

	/**
	 * Function to maybe disable the save button.
	 */
	jQuery.maybe_disable_save_button = function () {
		var $tab;
		var $save_button;
		if (
			$( '.woocommerce_variation_new_attribute_data' ).is( ':visible' )
		) {
			$tab = $( '.woocommerce_variation_new_attribute_data' );
			$save_button = $( 'button.create-variations' );
		} else {
			var $tab = $( '.product_attributes' );
			var $save_button = $( 'button.save_attributes' );
		}

		var attributes_and_variations_data = $tab.find(
			'input, select, textarea'
		);
		if (
			jQuery.is_attribute_or_variation_empty(
				attributes_and_variations_data
			)
		) {
			if ( ! $save_button.hasClass( 'disabled' ) ) {
				$save_button.addClass( 'disabled' );
				$save_button.attr( 'aria-disabled', true );
			}
		} else {
			$save_button.removeClass( 'disabled' );
			$save_button.removeAttr( 'aria-disabled' );
		}
	};

	// Run tipTip
	function runTipTip() {
		// Remove any lingering tooltips
		$( '#tiptip_holder' ).removeAttr( 'style' );
		$( '#tiptip_arrow' ).removeAttr( 'style' );
		$( '.tips' ).tipTip( {
			attribute: 'data-tip',
			fadeIn: 50,
			fadeOut: 50,
			delay: 200,
			keepAlive: true,
		} );
	}

	runTipTip();

	$( '.save_attributes' ).tipTip( {
		content: function () {
			return $( '.save_attributes' ).hasClass( 'disabled' )
				? woocommerce_admin_meta_boxes.i18n_save_attribute_variation_tip
				: '';
		},
		fadeIn: 50,
		fadeOut: 50,
		delay: 200,
		keepAlive: true,
	} );

	$( '.create-variations' ).tipTip( {
		content: function () {
			return $( '.create-variations' ).hasClass( 'disabled' )
				? woocommerce_admin_meta_boxes.i18n_save_attribute_variation_tip
				: '';
		},
		fadeIn: 50,
		fadeOut: 50,
		delay: 200,
		keepAlive: true,
	} );

	$( '.wc-metaboxes-wrapper' ).on( 'click', '.wc-metabox > h3', function () {
		var metabox = $( this ).parent( '.wc-metabox' );

		if ( metabox.hasClass( 'closed' ) ) {
			metabox.removeClass( 'closed' );
		} else {
			metabox.addClass( 'closed' );
		}

		if ( metabox.hasClass( 'open' ) ) {
			metabox.removeClass( 'open' );
		} else {
			metabox.addClass( 'open' );
		}
	} );

	// Tabbed Panels
	$( document.body )
		.on( 'wc-init-tabbed-panels', function () {
			$( 'ul.wc-tabs' ).show();
			$( 'ul.wc-tabs a' ).on( 'click', function ( e ) {
				e.preventDefault();
				var panel_wrap = $( this ).closest( 'div.panel-wrap' );
				$( 'ul.wc-tabs li', panel_wrap ).removeClass( 'active' );
				$( this ).parent().addClass( 'active' );
				$( 'div.panel', panel_wrap ).hide();
				$( $( this ).attr( 'href' ) ).show();
			} );
			$( 'div.panel-wrap' ).each( function () {
				$( this )
					.find( 'ul.wc-tabs li' )
					.eq( 0 )
					.find( 'a' )
					.trigger( 'click' );
			} );
		} )
		.trigger( 'wc-init-tabbed-panels' );

	// Date Picker
	$( document.body )
		.on( 'wc-init-datepickers', function () {
			$( '.date-picker-field, .date-picker' ).datepicker( {
				dateFormat: 'yy-mm-dd',
				numberOfMonths: 1,
				showButtonPanel: true,
			} );
		} )
		.trigger( 'wc-init-datepickers' );

	// Meta-Boxes - Open/close
	$( '.wc-metaboxes-wrapper' )
		.on( 'click', '.wc-metabox h3', function ( event ) {
			// If the user clicks on some form input inside the h3, like a select list (for variations), the box should not be toggled
			if ( $( event.target ).filter( ':input, option, .sort' ).length ) {
				return;
			}

			$( this ).next( '.wc-metabox-content' ).stop().slideToggle();
		} )
		.on( 'click', '.expand_all', function () {
			$( this )
				.closest( '.wc-metaboxes-wrapper' )
				.find( '.wc-metabox > .wc-metabox-content' )
				.show();
			return false;
		} )
		.on( 'click', '.close_all', function () {
			$( this )
				.closest( '.wc-metaboxes-wrapper' )
				.find( '.wc-metabox > .wc-metabox-content' )
				.hide();
			return false;
		} );
	$( '.wc-metabox.closed' ).each( function () {
		$( this ).find( '.wc-metabox-content' ).hide();
	} );

	$( '#product_attributes' ).on(
		'change',
		'select.attribute_values',
		jQuery.maybe_disable_save_button
	);
	$( '#product_attributes, #variable_product_options' ).on(
		'keyup',
		'input, textarea',
		jQuery.maybe_disable_save_button
	);

	// Maybe disable save buttons when editing products.
	jQuery.maybe_disable_save_button();
} );
