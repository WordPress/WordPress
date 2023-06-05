/*global wc_tokenization_form_params */
jQuery( function( $ ) {

	/**
	 * WCTokenizationForm class.
	 */
	var TokenizationForm = function( $target ) {
		this.$target   = $target;
		this.$formWrap = $target.closest( '.payment_box' );

		// Params.
		this.params = $.extend( {}, {
			'is_registration_required': false,
			'is_logged_in'            : false
		}, wc_tokenization_form_params );

		// Bind functions to this.
		this.onDisplay             = this.onDisplay.bind( this );
		this.hideForm              = this.hideForm.bind( this );
		this.showForm              = this.showForm.bind( this );
		this.showSaveNewCheckbox   = this.showSaveNewCheckbox.bind( this );
		this.hideSaveNewCheckbox   = this.hideSaveNewCheckbox.bind( this );

		// When a radio button is changed, make sure to show/hide our new CC info area.
		this.$target.on(
			'click change',
			':input.woocommerce-SavedPaymentMethods-tokenInput',
			{ tokenizationForm: this },
			this.onTokenChange
		);

		// OR if create account is checked.
		$( 'input#createaccount' ).on( 'change', { tokenizationForm: this }, this.onCreateAccountChange );

		// First display.
		this.onDisplay();
	};

	TokenizationForm.prototype.onDisplay = function() {
		// Make sure a radio button is selected if there is no is_default for this payment method..
		if ( 0 === $( ':input.woocommerce-SavedPaymentMethods-tokenInput:checked', this.$target ).length ) {
			$( ':input.woocommerce-SavedPaymentMethods-tokenInput:last', this.$target ).prop( 'checked', true );
		}

		// Don't show the "use new" radio button if we only have one method..
		if ( 0 === this.$target.data( 'count' ) ) {
			$( '.woocommerce-SavedPaymentMethods-new', this.$target ).remove();
		}

		// Hide "save card" if "Create Account" is not checked and registration is not forced.
		var hasCreateAccountCheckbox = 0 < $( 'input#createaccount' ).length,
			createAccount            = hasCreateAccountCheckbox && $( 'input#createaccount' ).is( ':checked' );

		if ( createAccount || this.params.is_logged_in || this.params.is_registration_required ) {
			this.showSaveNewCheckbox();
		} else {
			this.hideSaveNewCheckbox();
		}

		// Trigger change event
		$( ':input.woocommerce-SavedPaymentMethods-tokenInput:checked', this.$target ).trigger( 'change' );
	};

	TokenizationForm.prototype.onTokenChange = function( event ) {
		if ( 'new' === $( this ).val() ) {
			event.data.tokenizationForm.showForm();
			event.data.tokenizationForm.showSaveNewCheckbox();
		} else {
			event.data.tokenizationForm.hideForm();
			event.data.tokenizationForm.hideSaveNewCheckbox();
		}
	};

	TokenizationForm.prototype.onCreateAccountChange = function( event ) {
		if ( $( this ).is( ':checked' ) ) {
			event.data.tokenizationForm.showSaveNewCheckbox();
		} else {
			event.data.tokenizationForm.hideSaveNewCheckbox();
		}
	};

	TokenizationForm.prototype.hideForm = function() {
		$( '.wc-payment-form', this.$formWrap ).hide();
	};

	TokenizationForm.prototype.showForm = function() {
		$( '.wc-payment-form', this.$formWrap ).show();
	};

	TokenizationForm.prototype.showSaveNewCheckbox = function() {
		$( '.woocommerce-SavedPaymentMethods-saveNew', this.$formWrap ).show();
	};

	TokenizationForm.prototype.hideSaveNewCheckbox = function() {
		$( '.woocommerce-SavedPaymentMethods-saveNew', this.$formWrap ).hide();
	};

	/**
	 * Function to call wc_product_gallery on jquery selector.
	 */
	$.fn.wc_tokenization_form = function( args ) {
		new TokenizationForm( this, args );
		return this;
	};

	/**
	 * Initialize.
	 */
	$( document.body ).on( 'updated_checkout wc-credit-card-form-init', function() {
		// Loop over gateways with saved payment methods
		var $saved_payment_methods = $( 'ul.woocommerce-SavedPaymentMethods' );

		$saved_payment_methods.each( function() {
			$( this ).wc_tokenization_form();
		} );
	} );
} );
