/**
 * JavaScript code for the "Table" button in the TinyMCE editor toolbar
 *
 * @package TablePress
 * @subpackage Views JavaScript
 * @author Tobias Bäthge
 * @since 1.0.0
 */

/* global tinymce , tablepress_editor_button*/

( function() {

	'use strict';

	// only do this if TinyMCE is available
	if ( 'undefined' === typeof( tinymce ) ) {
		return;
	}

	/**
	 * Register a button for the TinyMCE (aka Visual Editor) toolbar
	 *
	 * @since 1.0.0
	 */
	tinymce.create( 'tinymce.plugins.TablePressPlugin', {
		init: function( ed, url ) {
			ed.addCommand( 'TablePress_insert_table', window.tablepress_open_shortcode_thickbox );

			ed.addButton( 'tablepress_insert_table', {
				title: tablepress_editor_button.title,
				cmd: 'TablePress_insert_table',
				image: url.slice( 0, url.length - 2 ) + 'img/tablepress-editor-button.png'
			} );
		}
/* // no real need for getInfo(), as it is not displayed/used anywhere
		,
		getInfo: function() {
			return {
				longname: 'TablePress',
				author: 'Tobias Bäthge',
				authorurl: 'https://tobias.baethge.com/',
				infourl: 'https://tablepress.org/',
				version: '1.0.0'
			};
		}
*/
	} );
	tinymce.PluginManager.add( 'tablepress_tinymce', tinymce.plugins.TablePressPlugin );

} )();
