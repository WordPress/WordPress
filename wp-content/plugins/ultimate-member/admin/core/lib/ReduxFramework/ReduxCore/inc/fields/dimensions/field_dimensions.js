
/*global jQuery, document, redux*/

(function( $ ) {
    "use strict";

    redux.field_objects = redux.field_objects || {};
    redux.field_objects.dimensions = redux.field_objects.dimensions || {};

    $( document ).ready(
        function() {
            //redux.field_objects.dimensions.init();
        }
    );

    redux.field_objects.dimensions.init = function( selector ) {

        if ( !selector ) {
            selector = $( document ).find( '.redux-container-dimensions:visible' );
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
                var default_params = {
                    width: 'resolve',
                    triggerChange: true,
                    allowClear: true
                };

                var select2_handle = el.find( '.select2_params' );
                if ( select2_handle.size() > 0 ) {
                    var select2_params = select2_handle.val();

                    select2_params = JSON.parse( select2_params );
                    default_params = $.extend( {}, default_params, select2_params );
                }

                el.find( ".redux-dimensions-units" ).select2( default_params );

                el.find( '.redux-dimensions-input' ).on(
                    'change', function() {
                        var units = $( this ).parents( '.redux-field:first' ).find( '.field-units' ).val();
                        if ( $( this ).parents( '.redux-field:first' ).find( '.redux-dimensions-units' ).length !== 0 ) {
                            units = $( this ).parents( '.redux-field:first' ).find( '.redux-dimensions-units option:selected' ).val();
                        }
                        if ( typeof units !== 'undefined' ) {
                            el.find( '#' + $( this ).attr( 'rel' ) ).val( $( this ).val() + units );
                        } else {
                            el.find( '#' + $( this ).attr( 'rel' ) ).val( $( this ).val() );
                        }
                    }
                );

                el.find( '.redux-dimensions-units' ).on(
                    'change', function() {
                        $( this ).parents( '.redux-field:first' ).find( '.redux-dimensions-input' ).change();
                    }
                );
            }
        );


    };
})( jQuery );