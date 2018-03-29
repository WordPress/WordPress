/*global redux_change, redux*/

(function( $ ) {
    "use strict";

    redux.field_objects                 = redux.field_objects || {};
    redux.field_objects.options_object  = redux.field_objects.options_object || {};

//    $( document ).ready(
//        function() {
//            redux.field_objects.import_export.init();
//        }
//    );

    redux.field_objects.options_object.init = function( selector ) {

        if ( !selector ) {
            selector = $( document ).find( '.redux-container-options_object' );
        }

        var parent = selector;

        if ( !selector.hasClass( 'redux-field-container' ) ) {
            parent = selector.parents( '.redux-field-container:first' );
        }

        if ( parent.hasClass( 'redux-field-init' ) ) {
            parent.removeClass( 'redux-field-init' );
        } else {
            return;
        }

        $( '#consolePrintObject' ).on(
            'click', function( e ) {
                e.preventDefault();
                console.log( $.parseJSON( $( "#redux-object-json" ).html() ) );
            }
        );

        if ( typeof jsonView === 'function' ) {
            jsonView( '#redux-object-json', '#redux-object-browser' );
        }        
    };
})( jQuery );