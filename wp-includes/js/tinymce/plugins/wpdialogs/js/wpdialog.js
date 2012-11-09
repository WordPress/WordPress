(function($){
	$.ui.dialog.prototype.options.closeOnEscape = false;
	$.widget('wp.wpdialog', $.ui.dialog, {
		// Work around a bug in jQuery UI 1.9.1.
		// http://bugs.jqueryui.com/ticket/8805
		widgetEventPrefix: 'wpdialog',

		open: function() {
			var ed;

			// Initialize tinyMCEPopup if it exists and the editor is active.
			if ( tinyMCEPopup && typeof tinyMCE != 'undefined' && ( ed = tinyMCE.activeEditor ) && !ed.isHidden() ) {
				tinyMCEPopup.init();
			}

			// Add beforeOpen event.
			if ( this.isOpen() || false === this._trigger('beforeOpen') ) {
				return;
			}

			// Open the dialog.
			this._super();
			// WebKit leaves focus in the TinyMCE editor unless we shift focus.
			this.element.focus();
			this._trigger('refresh');
		}
	});
})(jQuery);
