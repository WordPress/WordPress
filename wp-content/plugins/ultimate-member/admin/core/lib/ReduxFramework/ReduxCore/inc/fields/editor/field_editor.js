/**
 * Redux Editor on change callback
 * Dependencies        : jquery
 * Feature added by    : Dovy Paukstys
 *                     : Kevin Provance (who helped)  :P
 * Date                : 07 June 2014
 */

/*global redux_change, wp, tinymce, redux*/
(function( $ ) {
    "use strict";

    redux.field_objects = redux.field_objects || {};
    redux.field_objects.editor = redux.field_objects.editor || {};
    
    $( document ).ready(
        function() {
            //redux.field_objects.editor.init();
        }
    );

    redux.field_objects.editor.init = function( selector ) {
        setTimeout(
            function() {
                if (typeof(tinymce) !== 'undefined') {
                    for ( var i = 0; i < tinymce.editors.length; i++ ) {
                        redux.field_objects.editor.onChange( i );
                    }   
                }
            }, 1000
        );
    };

    redux.field_objects.editor.onChange = function( i ) {
        tinymce.editors[i].on(
            'change', function( e ) {
                var el = jQuery( e.target.contentAreaContainer );
                if ( el.parents( '.redux-container-editor:first' ).length !== 0 ) {
                    redux_change( $( '.wp-editor-area' ) );
                }
            }
        );
    };
})( jQuery );
