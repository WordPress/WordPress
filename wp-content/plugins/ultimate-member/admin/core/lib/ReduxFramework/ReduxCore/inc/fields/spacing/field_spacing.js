/*global redux*/

(function( $ ) {
    "use strict";

    redux.field_objects = redux.field_objects || {};
    redux.field_objects.spacing = redux.field_objects.spacing || {};

    $( document ).ready(
        function() {
            //redux.field_objects.spacing.init();
        }
    );

    redux.field_objects.spacing.init = function( selector ) {

        if ( !selector ) {
            selector = $( document ).find( ".redux-group-tab:visible" ).find( '.redux-container-spacing:visible' );
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

                el.find( ".redux-spacing-units" ).select2( default_params );

                el.find( '.redux-spacing-input' ).on(
                    'change', function() {
                        var units = $( this ).parents( '.redux-field:first' ).find( '.field-units' ).val();

                        if ( $( this ).parents( '.redux-field:first' ).find( '.redux-spacing-units' ).length !== 0 ) {
                            units = $( this ).parents( '.redux-field:first' ).find( '.redux-spacing-units option:selected' ).val();
                        }

                        var value = $( this ).val();

                        if ( typeof units !== 'undefined' && value ) {
                            value += units;
                        }

                        if ( $( this ).hasClass( 'redux-spacing-all' ) ) {
                            $( this ).parents( '.redux-field:first' ).find( '.redux-spacing-value' ).each(
                                function() {
                                    $( this ).val( value );
                                }
                            );
                        } else {
                            $( '#' + $( this ).attr( 'rel' ) ).val( value );
                        }
                    }
                );

                el.find( '.redux-spacing-units' ).on(
                    'change', function() {
                        $( this ).parents( '.redux-field:first' ).find( '.redux-spacing-input' ).change();
                    }
                );
            }
        );
    };
})( jQuery );