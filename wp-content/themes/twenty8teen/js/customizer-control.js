/**
 * Javascript for Customizer control pane.
 * @package Twenty8teen
 */

( function( wp ) {

	/* Use active_callback to set the transport.
	 * Using postMessage with no corresponding code means nothing will happen.
	 */
	_.each( ['excerpt_length', 'show_full_content',
		'featured_size_archives'], function( id ) {
		wp.customize.control( id, function( control ) {
			control.onChangeActive = function( active ) {
				control.setting.transport = active ? 'refresh' : 'postMessage';
			};
		} );
	}	);

	wp.customize.control( 'featured_size_single', function( control ) {
		control.onChangeActive = function( active ) {
			control.setting.transport = active ? 'postMessage' : 'refresh';
		};
	} );

	var dummy = document.createElement('_').style;
	dummy.cssText = '--foo: red; background: var(--foo);';

	if (! dummy.background) {  // no Custom Property support
		_.each( ['header_textcolor', 'background_color', 'body_textcolor',
		'accent_color', 'link_color', 'font_size_adjust', 'identimage_alpha'], function( id ) {
			wp.customize.control( id, function( control ) {
				control.onChangeActive = function( active ) {
					control.setting.transport = 'refresh';
				};
			} );
		} );
	}
} )( wp );
