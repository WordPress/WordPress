/*global redux_change, redux*/

(function( $ ) {
    "use strict";

    redux.field_objects = redux.field_objects || {};
    redux.field_objects.slider = redux.field_objects.slider || {};

    $( document ).ready(
        function() {
            
        }
    );

    redux.field_objects.slider.init = function( selector ) {

        if ( !selector ) {
            selector = $( document ).find( ".redux-group-tab:visible" ).find( '.redux-container-slider:visible' );
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

                el.find( 'div.redux-slider-container' ).each(
                    function() {

                        var start, toClass, defClassOne, defClassTwo, connectVal;
                        var DISPLAY_NONE = 0;
                        var DISPLAY_LABEL = 1;
                        var DISPLAY_TEXT = 2;
                        var DISPLAY_SELECT = 3;

                        var mainID          = $( this ).data( 'id' );
                        var minVal          = $( this ).data( 'min' );
                        var maxVal          = $( this ).data( 'max' );
                        var stepVal         = $( this ).data( 'step' );
                        var handles         = $( this ).data( 'handles' );
                        var defValOne       = $( this ).data( 'default-one' );
                        var defValTwo       = $( this ).data( 'default-two' );
                        var resVal          = $( this ).data( 'resolution' );
                        var displayValue    = parseInt( ($( this ).data( 'display' )) );
                        var rtlVal          = Boolean( $( this ).data( 'rtl' ) );
                        var floatMark       = ($( this ).data( 'float-mark' ));
                        var forced          = Boolean($( this ).data( 'forced' ));
                        
                        var rtl;
                        if ( rtlVal === true ) {
                            rtl = 'rtl';
                        } else {
                            rtl = 'ltr';
                        }

                        // range array
                        var range = [minVal, maxVal];

                        // Set default values for dual slides.
                        var startTwo = [defValOne, defValTwo];

                        // Set default value for single slide
                        var startOne = [defValOne];

                        var inputOne, inputTwo;
                        if ( displayValue == DISPLAY_TEXT ) {
                            defClassOne = el.find( '.redux-slider-input-one-' + mainID );
                            defClassTwo = el.find( '.redux-slider-input-two-' + mainID );

                            inputOne = defClassOne;
                            inputTwo = defClassTwo;
                        } else if ( displayValue == DISPLAY_SELECT ) {
                            defClassOne = el.find( '.redux-slider-select-one-' + mainID );
                            defClassTwo = el.find( '.redux-slider-select-two-' + mainID );

                            redux.field_objects.slider.loadSelect( defClassOne, minVal, maxVal, resVal, stepVal );

                            if ( handles === 2 ) {
                                redux.field_objects.slider.loadSelect( defClassTwo, minVal, maxVal, resVal, stepVal );
                            }

                        } else if ( displayValue == DISPLAY_LABEL ) {
                            defClassOne = el.find( '#redux-slider-label-one-' + mainID );
                            defClassTwo = el.find( '#redux-slider-label-two-' + mainID );
                        } else if ( displayValue == DISPLAY_NONE ) {
                            defClassOne = el.find( '.redux-slider-value-one-' + mainID );
                            defClassTwo = el.find( '.redux-slider-value-two-' + mainID );
                        }

                        var classOne, classTwo;
                        if ( displayValue == DISPLAY_LABEL ) {
                            var x = [defClassOne, 'html'];
                            var y = [defClassTwo, 'html'];

                            classOne = [x];
                            classTwo = [x, y];
                        } else {
                            classOne = [defClassOne];
                            classTwo = [defClassOne, defClassTwo];
                        }

                        if ( handles === 2 ) {
                            start = startTwo;
                            toClass = classTwo;
                            connectVal = true;
                        } else {
                            start = startOne;
                            toClass = classOne;
                            connectVal = 'lower';
                        }

                        var slider = $( this ).noUiSlider(
                            {
                                range: range,
                                start: start,
                                handles: handles,
                                step: stepVal,
                                connect: connectVal,
                                behaviour: "tap-drag",
                                direction: rtl,
                                serialization: {
                                    resolution: resVal,
                                    to: toClass,
                                    mark: floatMark,
                                },
                                slide: function() {
                                    if ( displayValue == DISPLAY_LABEL ) {
                                        if ( handles === 2 ) {
                                            var inpSliderVal = slider.val();
                                            el.find( 'input.redux-slider-value-one-' + mainID ).attr(
                                                'value', inpSliderVal[0]
                                            );
                                            el.find( 'input.redux-slider-value-two-' + mainID ).attr(
                                                'value', inpSliderVal[1]
                                            );
                                        } else {
                                            el.find( 'input.redux-slider-value-one-' + mainID ).attr(
                                                'value', slider.val()
                                            );
                                        }
                                    }

                                    if ( displayValue == DISPLAY_SELECT ) {
                                        if ( handles === 2 ) {
                                            el.find( '.redux-slider-select-one' ).select2( 'val', slider.val()[0] );
                                            el.find( '.redux-slider-select-two' ).select2( 'val', slider.val()[1] );
                                        } else {
                                            el.find( '.redux-slider-select-one' ).select2( 'val', slider.val() );
                                        }
                                    }

                                    redux_change( $( this ).parents( '.redux-field-container:first' ).find( 'input' ) );
                                }
                            }
                        );

                        if ( displayValue === DISPLAY_TEXT ) {
                            inputOne.keydown(
                                function( e ) {

                                    var sliderOne = slider.val();
                                    var value = parseInt( sliderOne[0] );

                                    switch ( e.which ) {
                                        case 38:
                                            slider.val( [value + 1, null] );
                                            break;
                                        case 40:
                                            slider.val( [value - 1, null] );
                                            break;
                                        case 13:
                                            e.preventDefault();
                                            break;
                                    }
                                }
                            );

                            if ( handles === 2 ) {
                                inputTwo.keydown(
                                    function( e ) {
                                        var sliderTwo = slider.val();
                                        var value = parseInt( sliderTwo[1] );

                                        switch ( e.which ) {
                                            case 38:
                                                slider.val( [null, value + 1] );
                                                break;
                                            case 40:
                                                slider.val( [null, value - 1] );
                                                break;
                                            case 13:
                                                e.preventDefault();
                                                break;
                                        }
                                    }
                                );
                            }
                        }
                    }
                );

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

                el.find( 'select.redux-slider-select-one, select.redux-slider-select-two' ).select2( default_params );
                
            }
        );

    };

    // Return true for float value, false otherwise
    redux.field_objects.slider.isFloat = function( mixed_var ) {
        return +mixed_var === mixed_var && (!(isFinite( mixed_var ))) || Boolean( (mixed_var % 1) );
    };

    // Return number of integers after the decimal point.
    redux.field_objects.slider.decimalCount = function( res ) {
        var q = res.toString().split( '.' );
        return q[1].length;
    };

    redux.field_objects.slider.loadSelect = function( myClass, min, max, res, step ) {

        //var j = step + ((decCount ) - (step )); //  18;

        for ( var i = min; i <= max; i = i + res ) {
            //var step = 2;

            //if (j === (step + ((decCount ) - (step )))) {
            var n = i;
            if ( redux.field_objects.slider.isFloat( res ) ) {
                var decCount = redux.field_objects.slider.decimalCount( res );
                n = i.toFixed( decCount );
            }

            $( myClass ).append(
                '<option value="' + n + '">' + n + '</option>'
            );
            //j = 0;
            //}
            //j++;
        }
    };


})( jQuery );
