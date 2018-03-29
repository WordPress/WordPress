(function( $ ) {
    'use strict';

    $.redux_welcome = $.redux_welcome || {};

    $( document ).ready(
        function() {
            $.redux_welcome.initQtip();
            if ( jQuery( document.getElementById( "support_div" ) ).is( ":visible" ) ) {
                $.redux_welcome.initSupportPage();
            }
            $.redux_welcome.supportHash();
        }
    );


    $.redux_welcome.supportHash = function() {

        jQuery( "#support_hash" ).focus(
            function() {
                var $this = jQuery( this );
                $this.select();

                // Work around Chrome's little problem
                $this.mouseup(
                    function() {
                        // Prevent further mouseup intervention
                        $this.unbind( "mouseup" );
                        return false;
                    }
                );
            }
        );

        jQuery( '.redux_support_hash' ).click(
            function( e ) {

                var $button = jQuery( this );
                if ( $button.hasClass( 'disabled' ) ) {
                    return;
                }
                var $nonce = jQuery( '#redux_support_nonce' ).val();
                $button.addClass( 'disabled' );
                $button.parent().append( '<span class="spinner" style="display:block;float: none;margin: 10px auto;"></span>' );
                $button.closest( '.spinner' ).fadeIn();
                if ( !window.console ) console = {};
                console.log = console.log || function( name, data ) {};
                jQuery.ajax(
                    {
                        type: "post",
                        dataType: "json",
                        url: ajaxurl,
                        data: {
                            action: "redux_support_hash",
                            nonce: $nonce
                        },
                        error: function( response ) {
                            console.log( response );
                            $button.removeClass( 'disabled' );
                            $button.parent().find( '.spinner' ).remove();
                            alert( 'There was an error. Please try again later.' );
                        },
                        success: function( response ) {
                            if ( response.status == "success" ) {
                                jQuery( '#support_hash' ).val( 'http://support.redux.io/?id=' + response.identifier );
                                $button.parents( 'fieldset:first' ).find( '.next' ).removeAttr( 'disabled' ).click();
                            } else {
                                console.log( response );
                                alert( 'There was an error. Please try again later.' );
                            }
                        }
                    }
                );
                e.preventDefault();
            }
        );
    };

    $.redux_welcome.initSupportPage = function() {
        //jQuery time
        var current_fs, next_fs, previous_fs; //fieldsets
        var left, opacity, scale; //fieldset properties which we will animate
        var animating; //flag to prevent quick multi-click glitches

        $.fn.actualHeight = function() {
            // find the closest visible parent and get it's hidden children
            var visibleParent = this.closest( ':visible' ).children(),
                thisHeight;

            // set a temporary class on the hidden parent of the element
            visibleParent.addClass( 'temp-show' );

            // get the height
            thisHeight = this.height();

            // remove the temporary class
            visibleParent.removeClass( 'temp-show' );

            return thisHeight;
        };

        function setHeight() {
            var $height = 0;
            jQuery( document ).find( '#support_div fieldset' ).each(
                function() {
                    var $actual = $( this ).actualHeight();
                    if ( $height < $actual ) {
                        $height = $actual;
                    }
                }
            );
            jQuery( '#support_div' ).height( $height + 20 );
        }

        setHeight();
        $( window ).on(
            'resize', function() {
                setHeight();
            }
        );
        jQuery( '#is_user' ).click(
            function() {
                jQuery( '#final_support .is_user' ).show();
                jQuery( '#final_support .is_developer' ).hide();
                jQuery( this ).parents( 'fieldset:first' ).find( '.next' ).click();
            }
        );
        jQuery( '#is_developer' ).click(
            function() {
                jQuery( '#final_support .is_user' ).hide();
                jQuery( '#final_support .is_developer' ).show();
                jQuery( this ).parents( 'fieldset:first' ).find( '.next' ).click();
            }
        );

        jQuery( "#support_div .next" ).click(
            function() {
                if ( animating ) return false;
                animating = true;

                current_fs = jQuery( this ).parent();
                next_fs = jQuery( this ).parent().next();

                //activate next step on progressbar using the index of next_fs
                jQuery( "#progressbar li" ).eq( jQuery( "fieldset" ).index( next_fs ) ).addClass( "active" );

                //show the next fieldset
                next_fs.show();
                //hide the current fieldset with style
                current_fs.animate(
                    {opacity: 0}, {
                        step: function( now, mx ) {
                            //as the opacity of current_fs reduces to 0 - stored in "now"
                            //1. scale current_fs down to 80%
                            scale = 1 - (1 - now) * 0.2;
                            //2. bring next_fs from the right(50%)
                            left = (now * 50) + "%";
                            //3. increase opacity of next_fs to 1 as it moves in
                            opacity = 1 - now;
                            current_fs.css( {'transform': 'scale(' + scale + ')'} );
                            next_fs.css( {'left': left, 'opacity': opacity} );
                        },
                        duration: 800,
                        complete: function() {
                            current_fs.hide();
                            animating = false;
                        },
                        //this comes from the custom easing plugin
                        easing: 'easeInOutBack'
                    }
                );
            }
        );

        jQuery( "#support_div .previous" ).click(
            function() {
                if ( animating ) return false;
                animating = true;

                current_fs = jQuery( this ).parent();
                previous_fs = jQuery( this ).parent().prev();

                //de-activate current step on progressbar
                jQuery( "#progressbar li" ).eq( jQuery( "fieldset" ).index( current_fs ) ).removeClass( "active" );

                //show the previous fieldset
                previous_fs.show();
                //hide the current fieldset with style
                current_fs.animate(
                    {opacity: 0}, {
                        step: function( now, mx ) {
                            //as the opacity of current_fs reduces to 0 - stored in "now"
                            //1. scale previous_fs from 80% to 100%
                            scale = 0.8 + (1 - now) * 0.2;
                            //2. take current_fs to the right(50%) - from 0%
                            left = ((1 - now) * 50) + "%";
                            //3. increase opacity of previous_fs to 1 as it moves in
                            opacity = 1 - now;
                            current_fs.css( {'left': left} );
                            previous_fs.css( {'transform': 'scale(' + scale + ')', 'opacity': opacity} );
                        },
                        duration: 800,
                        complete: function() {
                            current_fs.hide();
                            animating = false;
                        },
                        //this comes from the custom easing plugin
                        easing: 'easeInOutBack'
                    }
                );
            }
        );
    }

    $.redux_welcome.initQtip = function() {
        if ( $().qtip ) {
            var shadow = 'qtip-shadow';
            var color = 'qtip-dark';
            var rounded = '';
            var style = ''; //qtip-bootstrap';

            var classes = shadow + ',' + color + ',' + rounded + ',' + style;
            classes = classes.replace( /,/g, ' ' );

            // Get position data
            var myPos = 'top center';
            var atPos = 'bottom center';

            // Tooltip trigger action
            var showEvent = 'click';
            var hideEvent = 'click mouseleave';

            // Tip show effect
            var tipShowEffect = 'slide';
            var tipShowDuration = '500';

            // Tip hide effect
            var tipHideEffect = 'slide';
            var tipHideDuration = '500';

            $( '.redux-hint-qtip' ).each(
                function() {
                    $( this ).qtip(
                        {
                            content: {
                                text: $( this ).attr( 'qtip-content' ),
                                title: $( this ).attr( 'qtip-title' )
                            },
                            show: {
                                effect: function() {
                                    switch ( tipShowEffect ) {
                                        case 'slide':
                                            $( this ).slideDown( tipShowDuration );
                                            break;
                                        case 'fade':
                                            $( this ).fadeIn( tipShowDuration );
                                            break;
                                        default:
                                            $( this ).show();
                                            break;
                                    }
                                },
                                event: showEvent,
                            },
                            hide: {
                                effect: function() {
                                    switch ( tipHideEffect ) {
                                        case 'slide':
                                            $( this ).slideUp( tipHideDuration );
                                            break;
                                        case 'fade':
                                            $( this ).fadeOut( tipHideDuration );
                                            break;
                                        default:
                                            $( this ).show( tipHideDuration );
                                            break;
                                    }
                                },
                                event: hideEvent,
                            },
                            style: {
                                classes: classes,
                            },
                            position: {
                                my: myPos,
                                at: atPos,
                            },
                        }
                    );
                }
            );
        }
    };
})( jQuery );