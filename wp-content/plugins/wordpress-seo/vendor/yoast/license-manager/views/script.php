<?php
if ( ! defined( 'ABSPATH' ) ) {
	header( 'HTTP/1.0 403 Forbidden' );
	die;
} 
?><script type="text/javascript">
(function($) {
	
	if( typeof YoastLicenseManager !== "undefined" ) {
		return;
	}

	window.YoastLicenseManager = (function () {

		function init() {
			var $keyInputs = $(".yoast-license-key-field.yoast-license-obfuscate");
			var $actionButtons = $('.yoast-license-toggler button');
			var $submitButtons = $('input[type="submit"], button[type="submit"]');

			$submitButtons.click( addDisableEvent );
			$actionButtons.click( actOnLicense );
			$keyInputs.click( setEmptyValue );
		}

		function setEmptyValue() {
			if( ! $(this).is('[readonly]') ) {
				$(this).val('');
			}
		}

		function actOnLicense() {
			var $formScope = $(this).closest('form');
			var $actionButton = $formScope.find('.yoast-license-toggler button');

			// fake input field with exact same name => value			
			$("<input />")
				.attr('type', 'hidden')
				.attr( 'name', $(this).attr('name') )
				.val( $(this).val() )
				.appendTo( $formScope );

			// change button text to show we're working..
			var text = ( $actionButton.hasClass('yoast-license-activate') ) ? "Activating..." : "Deactivating...";
			$actionButton.text( text );
		}

		function addDisableEvent() {
			var $formScope = $(this).closest('form');
			$formScope.submit(disableButtons);
		}

		function disableButtons() {
			var $formScope = $(this).closest('form');
			var $submitButton = $formScope.find('input[type="submit"], button[type="submit"]');
			$submitButton.prop( 'disabled', true );
		}

		return {
			init: init
		}
	
	})();

	YoastLicenseManager.init();

})(jQuery);
</script>