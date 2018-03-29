/*
 Field Palette (color)
 */

/*global jQuery, document, redux_change, redux*/

(function( $ ) {
    'use strict';

    redux.field_objects         = redux.field_objects || {};
    redux.field_objects.palette = redux.field_objects.palette || {};

    redux.field_objects.palette.init = function( selector ) {
        
        if ( !selector ) {
            selector = $( document ).find( ".redux-group-tab:visible" ).find( '.redux-container-palette:visible' );
        }

        $( selector ).each(
            function() {

                var el = $( this );
                var parent = el;
                
                if ( !el.hasClass( 'redux-field-container' ) ) {
                    parent = el.parents( '.redux-field-container:first' );
                }
                
                if ( parent.is( ":hidden" ) ) { // Skip hidden fields
                    return;
                }
                
                if ( parent.hasClass( 'redux-field-init' ) ) {
                    parent.removeClass( 'redux-field-init' );
                } else {
                    return;
                }
                
                el.find( '.buttonset' ).each(
                    function() {
                        $( this ).buttonset();
                    }
                );
        
//                el.find('.redux-palette-set').click(
//                    function(){
//                        console.log($(this).val());
//                    }
//                )
            }
        );
    };
})( jQuery );