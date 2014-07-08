(function($){
	if ( $('body').hasClass('language-chooser') === false ) {
		return;
	}

	var mouseDown = 0,
		$fieldset = $('fieldset');

	// simple way to check if mousebutton is depressed while accounting for multiple mouse buttons being used independently
	document.body.onmousedown = function() {
		++mouseDown;
	};
	document.body.onmouseup = function() {
		--mouseDown;
	};

	/*
		we can't rely upon the focusout event
		since clicking on a label triggers it
	*/
	function maybeRemoveFieldsetFocus(){
		if (mouseDown) {
			setTimeout( maybeRemoveFieldsetFocus, 50);
			return;
		}
		if ( $(':focus').hasClass('language-chooser-input') !== true ) {
			$fieldset.removeClass('focus');
		}
	}

	$fieldset.focusin( function() {
		$(this).addClass('focus');
	});

	$fieldset.focusout( function() {
		setTimeout( maybeRemoveFieldsetFocus, 50);
	});

})(jQuery);
