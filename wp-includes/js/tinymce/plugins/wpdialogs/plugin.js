/* global tinymce */

tinymce.WPWindowManager = function( editor ) {
	var element;

	this.parent = editor.windowManager;
	this.editor = editor;

	tinymce.extend( this, this.parent )

	this.open = function( args, params ) {
		var self = this, element;

		if ( ! args.wpDialog )
			return this.parent.open( args, params );
		else if ( ! args.id )
			return;

		self.element = element = jQuery('#' + args.id);
		if ( ! element.length )
			return;

		self.features = args;
		self.params = params;
		self.onOpen.dispatch( self, args, params );
		self.windows.push( element );

		// Store selection
	//	self.bookmark = self.editor.selection.getBookmark(1);

		// Create the dialog if necessary
		if ( ! element.data('wpdialog') ) {
			element.wpdialog({
				title: args.title,
				width: args.width,
				height: args.height,
				modal: true,
				dialogClass: 'wp-dialog',
				zIndex: 300000
			});
		}

		element.wpdialog('open');
	};

	this.close = function() {
		if ( ! this.features.wpDialog )
			return this.parent.close.apply( this, arguments );

		this.element.wpdialog('close');
	};
}

tinymce.PluginManager.add( 'wpdialogs', function( editor ) {
	// Replace window manager
	editor.on( 'init', function() {
		editor.windowManager = new tinymce.WPWindowManager( editor );
	});
});
