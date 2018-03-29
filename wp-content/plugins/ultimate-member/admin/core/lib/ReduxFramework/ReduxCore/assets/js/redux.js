/* global redux, confirm, relid:true, jsonView */

(function( $ ) {
    'use strict';

    $.redux = $.redux || {};

    $( document ).ready(
        function() {
            $.fn.isOnScreen = function() {
                if ( !window ) {
                    return;
                }

                var win = $( window );
                var viewport = {
                    top: win.scrollTop(),
                };

                viewport.right = viewport.left + win.width();
                viewport.bottom = viewport.top + win.height();

                var bounds = this.offset();

                bounds.right = bounds.left + this.outerWidth();
                bounds.bottom = bounds.top + this.outerHeight();

                return (!(viewport.right < bounds.left || viewport.left > bounds.right || viewport.bottom < bounds.top || viewport.top > bounds.bottom));
            };

            $.redux.hideFields();
            $.redux.checkRequired();
            $.redux.initEvents();
            $.redux.initQtip();
            $.redux.tabCheck();
            $.redux.notices();
            $.redux.tabControl();
        }
    );

    $.redux.ajax_save = function( button ) {

        var overlay = $( document.getElementById( 'redux_ajax_overlay' ) );
        overlay.fadeIn();

        // Add the loading mechanism
        jQuery( '.redux-action_bar .spinner' ).addClass( 'is-active' );

        jQuery( '.redux-action_bar input' ).attr( 'disabled', 'disabled' );
        var $notification_bar = jQuery( document.getElementById( 'redux_notification_bar' ) );
        $notification_bar.slideUp();
        jQuery( '.redux-save-warn' ).slideUp();
        jQuery( '.redux_ajax_save_error' ).slideUp(
            'medium', function() {
                jQuery( this ).remove();
            }
        );

        var $parent = jQuery( document.getElementById( "redux-form-wrapper" ) );

        // Editor field doesn't auto save. Have to call it. Boo.
        if ( redux.fields.hasOwnProperty( "editor" ) ) {
            $.each(
                redux.fields.editor, function( $key, $index ) {
                    if ( typeof(tinyMCE) !== 'undefined' ) {
                        var editor = tinyMCE.get( $key );
                        if ( editor ) {
                            editor.save();
                        }
                    }
                }
            );
        }

        var $data = $parent.serialize();
        // add values for checked and unchecked checkboxes fields
        $parent.find( 'input[type=checkbox]' ).each(
            function() {
                if ( typeof $( this ).attr( 'name' ) !== "undefined" ) {
                    var chkVal = $( this ).is( ':checked' ) ? $( this ).val() : "0";
                    $data += "&" + $( this ).attr( 'name' ) + "=" + chkVal;
                }
            }
        );


        if ( button.attr( 'name' ) != "redux_save" ) {
            $data += "&" + button.attr( 'name' ) + "=" + button.val();
        }

        var $nonce = $parent.attr( "data-nonce" );

        jQuery.ajax(
            {
                type: "post",
                dataType: "json",
                url: ajaxurl,
                data: {
                    action: redux.args.opt_name + "_ajax_save",
                    nonce: $nonce,
                    'opt_name': redux.args.opt_name,
                    data: $data
                },
                error: function( response ) {
                    if ( !window.console ) console = {};
                    console.log = console.log || function( name, data ) {
                        };
                    console.log( redux.ajax.console );
                    console.log( response.responseText );
                    jQuery( '.redux-action_bar input' ).removeAttr( 'disabled' );
                    overlay.fadeOut( 'fast' );
                    jQuery( '.redux-action_bar .spinner' ).removeClass( 'is-active' );
                    alert( redux.ajax.alert );
                },
                success: function( response ) {
                    if ( response.action && response.action == "reload" ) {
                        location.reload( true );
                    } else if ( response.status == "success" ) {
                        jQuery( '.redux-action_bar input' ).removeAttr( 'disabled' );
                        overlay.fadeOut( 'fast' );
                        jQuery( '.redux-action_bar .spinner' ).removeClass( 'is-active' );
                        redux.options = response.options;
                        //redux.defaults = response.defaults;
                        redux.errors = response.errors;
                        redux.warnings = response.warnings;

                        $notification_bar.html( response.notification_bar ).slideDown( 'fast' );
                        if ( response.errors !== null || response.warnings !== null ) {
                            $.redux.notices();
                        }
                        var $save_notice = $( document.getElementById( 'redux_notification_bar' ) ).find( '.saved_notice' );
                        $save_notice.slideDown();
                        $save_notice.delay( 4000 ).slideUp();
                    } else {
                        jQuery( '.redux-action_bar input' ).removeAttr( 'disabled' );
                        jQuery( '.redux-action_bar .spinner' ).removeClass( 'is-active' );
                        overlay.fadeOut( 'fast' );
                        jQuery( '.wrap h2:first' ).parent().append( '<div class="error redux_ajax_save_error" style="display:none;"><p>' + response.status + '</p></div>' );
                        jQuery( '.redux_ajax_save_error' ).slideDown();
                        jQuery( "html, body" ).animate( {scrollTop: 0}, "slow" );
                    }
                }
            }
        );
        return false;
    };

    $.redux.initEvents = function() {
        $( '.redux-presets-bar' ).on(
            'click', function() {
                window.onbeforeunload = null;
            }
        );


        $( '#toplevel_page_' + redux.args.slug + ' .wp-submenu a, #wp-admin-bar-' + redux.args.slug + ' a.ab-item' ).click(
            function( e ) {

                if ( ( $( '#toplevel_page_' + redux.args.slug ).hasClass( 'wp-menu-open' ) || $( this ).hasClass( 'ab-item' ) ) && !$( this ).parents( 'ul.ab-submenu:first' ).hasClass( 'ab-sub-secondary' ) && $( this ).attr( 'href' ).toLowerCase().indexOf( redux.args.slug + "&tab=" ) >= 0 ) {
                    e.preventDefault();
                    var url = $( this ).attr( 'href' ).split( '&tab=' );
                    $( '#' + url[1] + '_section_group_li_a' ).click();
                    $( this ).parents( 'ul:first' ).find( '.current' ).removeClass( 'current' );
                    $( this ).addClass( 'current' );
                    $( this ).parent().addClass( 'current' );
                    return false;
                }
            }
        );

        // Save button clicked
        $( '.redux-action_bar input' ).on(
            'click', function( e ) {
                if ( $( this ).attr( 'name' ) == redux.args.opt_name + '[defaults]' ) {
                    // Defaults button clicked
                    if ( !confirm( redux.args.reset_confirm ) ) {
                        return false;
                    }
                } else if ( $( this ).attr( 'name' ) == redux.args.opt_name + '[defaults-section]' ) {
                    // Default section clicked
                    if ( !confirm( redux.args.reset_section_confirm ) ) {
                        return false;
                    }
                }

                window.onbeforeunload = null;

                if ( redux.args.ajax_save === true ) {
                    $.redux.ajax_save( $( this ) );
                    e.preventDefault();
                }
            }
        );
        //
        //// Default button clicked
        //$( 'input[name="' + redux.args.opt_name + '[defaults]"]' ).click(
        //    function() {
        //        if ( !confirm( redux.args.reset_confirm ) ) {
        //            return false;
        //        }
        //        window.onbeforeunload = null;
        //    }
        //);


        //$( 'input[name="' + redux.args.opt_name + '[defaults-section]"]' ).click(
        //    function() {
        //        if ( !confirm( redux.args.reset_section_confirm ) ) {
        //            return false;
        //        }
        //
        //        window.onbeforeunload = null;
        //    }
        //);
        //$( '.redux-save' ).click(
        //    function() {
        //        window.onbeforeunload = null;
        //    }
        //);

        $( '.expand_options' ).click(
            function( e ) {

                e.preventDefault();

                var container = $( '.redux-container' );
                if ( $( container ).hasClass( 'fully-expanded' ) ) {
                    $( container ).removeClass( 'fully-expanded' );

                    var tab = $.cookie( "redux_current_tab" );

                    $( '.redux-container:first' ).find( '#' + tab + '_section_group' ).fadeIn(
                        200, function() {
                            if ( $( '.redux-container:first' ).find( '#redux-footer' ).length !== 0 ) {
                                $.redux.stickyInfo(); // race condition fix
                            }
                            $.redux.initFields();
                        }
                    );
                }

                $.redux.expandOptions( $( this ).parents( '.redux-container:first' ) );

                return false;
            }
        );

        if ( $( '.saved_notice' ).is( ':visible' ) ) {
            $( '.saved_notice' ).slideDown();
        }

        $( document.body ).on(
            'change', '.redux-field input, .redux-field textarea, .redux-field select', function() {
                if ( !$( this ).hasClass( 'noUpdate' ) ) {
                    redux_change( $( this ) );
                }
            }
        );

        var stickyHeight = $( '#redux-footer' ).height();

        $( '#redux-sticky-padder' ).css(
            {
                height: stickyHeight
            }
        );
        $( '#redux-footer-sticky' ).removeClass( 'hide' );

        if ( $( '#redux-footer' ).length !== 0 ) {
            $( window ).scroll(
                function() {
                    $.redux.stickyInfo();
                }
            );

            $( window ).resize(
                function() {
                    $.redux.stickyInfo();
                }
            );
        }

        $( '.saved_notice' ).delay( 4000 ).slideUp();


    };

    $.redux.hideFields = function() {
        $( "label[for='redux_hide_field']" ).each(
            function( idx, val ) {
                var tr = $( this ).parent().parent();
                $( tr ).addClass( 'hidden' );
            }
        );
    };

    $.redux.checkRequired = function() {
        $.redux.required();

        $( "body" ).on(
            'change',
            '.redux-main select, .redux-main radio, .redux-main input[type=checkbox], .redux-main input[type=hidden]',
            function( e ) {
                $.redux.check_dependencies( this );
            }
        );

        $( "body" ).on(
            'check_dependencies', function( e, variable ) {
                $.redux.check_dependencies( variable );
            }
        );

        $( 'td > fieldset:empty,td > div:empty' ).parent().parent().hide();
    };

    $.redux.initQtip = function() {
        if ( $().qtip ) {
            // Shadow
            var shadow = '';
            var tip_shadow = redux.args.hints.tip_style.shadow;

            if ( tip_shadow === true ) {
                shadow = 'qtip-shadow';
            }

            // Color
            var color = '';
            var tip_color = redux.args.hints.tip_style.color;

            if ( tip_color !== '' ) {
                color = 'qtip-' + tip_color;
            }

            // Rounded
            var rounded = '';
            var tip_rounded = redux.args.hints.tip_style.rounded;

            if ( tip_rounded === true ) {
                rounded = 'qtip-rounded';
            }

            // Tip style
            var style = '';
            var tip_style = redux.args.hints.tip_style.style;

            if ( tip_style !== '' ) {
                style = 'qtip-' + tip_style;
            }

            var classes = shadow + ',' + color + ',' + rounded + ',' + style + ',redux-qtip';
            classes = classes.replace( /,/g, ' ' );

            // Get position data
            var myPos = redux.args.hints.tip_position.my;
            var atPos = redux.args.hints.tip_position.at;

            // Gotta be lowercase, and in proper format
            myPos = $.redux.verifyPos( myPos.toLowerCase(), true );
            atPos = $.redux.verifyPos( atPos.toLowerCase(), false );

            // Tooltip trigger action
            var showEvent = redux.args.hints.tip_effect.show.event;
            var hideEvent = redux.args.hints.tip_effect.hide.event;

            // Tip show effect
            var tipShowEffect = redux.args.hints.tip_effect.show.effect;
            var tipShowDuration = redux.args.hints.tip_effect.show.duration;

            // Tip hide effect
            var tipHideEffect = redux.args.hints.tip_effect.hide.effect;
            var tipHideDuration = redux.args.hints.tip_effect.hide.duration;

            $( 'div.redux-dev-qtip' ).each(
                function() {
                    $( this ).qtip(
                        {
                            content: {
                                text: $( this ).attr( 'qtip-content' ),
                                title: $( this ).attr( 'qtip-title' )
                            },
                            show: {
                                effect: function() {
                                    $( this ).slideDown( 500 );
                                },
                                event: 'mouseover',
                            },
                            hide: {
                                effect: function() {
                                    $( this ).slideUp( 500 );
                                },
                                event: 'mouseleave',
                            },
                            style: {
                                classes: 'qtip-shadow qtip-light',
                            },
                            position: {
                                my: 'top center',
                                at: 'bottom center',
                            },
                        }
                    );
                }
            );

            $( 'div.redux-hint-qtip' ).each(
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
                                            $( this ).hide( tipHideDuration );
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
            // });

            $( 'input[qtip-content]' ).each(
                function() {
                    $( this ).qtip(
                        {
                            content: {
                                text: $( this ).attr( 'qtip-content' ),
                                title: $( this ).attr( 'qtip-title' )
                            },
                            show: 'focus',
                            hide: 'blur',
                            style: classes,
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

    $.redux.tabCheck = function() {
        $( '.redux-group-tab-link-a' ).click(
            function() {
                var link = $( this );
                if ( link.parent().hasClass( 'empty_section' ) && link.parent().hasClass( 'hasSubSections' ) ) {
                    var elements = $( this ).closest( 'ul' ).find( '.redux-group-tab-link-a' );
                    var index = elements.index( this );
                    link = elements.slice( index + 1, index + 2 );
                }
                var el = link.parents( '.redux-container:first' );
                var relid = link.data( 'rel' ); // The group ID of interest
                var oldid = el.find( '.redux-group-tab-link-li.active:first .redux-group-tab-link-a' ).data( 'rel' );

                //console.log('id: '+relid+' oldid: '+oldid);

                if ( oldid === relid ) {
                    return;
                }

                $( '#currentSection' ).val( relid );
                if ( !link.parents( '.postbox-container:first' ).length ) {
                    // Set the proper page cookie
                    $.cookie(
                        'redux_current_tab', relid, {
                            expires: 7,
                            path: '/'
                        }
                    );
                }

                if ( el.find( '#' + relid + '_section_group_li' ).parents( '.redux-group-tab-link-li' ).length ) {
                    var parentID = el.find( '#' + relid + '_section_group_li' ).parents( '.redux-group-tab-link-li' ).attr( 'id' ).split( '_' );
                    parentID = parentID[0];
                }

                el.find( '#toplevel_page_' + redux.args.slug + ' .wp-submenu a.current' ).removeClass( 'current' );
                el.find( '#toplevel_page_' + redux.args.slug + ' .wp-submenu li.current' ).removeClass( 'current' );

                el.find( '#toplevel_page_' + redux.args.slug + ' .wp-submenu a' ).each(
                    function() {
                        var url = $( this ).attr( 'href' ).split( '&tab=' );
                        if ( url[1] == relid || url[1] == parentID ) {
                            $( this ).addClass( 'current' );
                            $( this ).parent().addClass( 'current' );
                        }
                    }
                );

                if ( el.find( '#' + oldid + '_section_group_li' ).find( '#' + oldid + '_section_group_li' ).length ) {
                    //console.log('RELID is child of oldid');
                    el.find( '#' + oldid + '_section_group_li' ).addClass( 'activeChild' );
                    el.find( '#' + relid + '_section_group_li' ).addClass( 'active' ).removeClass( 'activeChild' );
                } else if ( el.find( '#' + relid + '_section_group_li' ).parents( '#' + oldid + '_section_group_li' ).length || el.find( '#' + oldid + '_section_group_li' ).parents( 'ul.subsection' ).find( '#' + relid + '_section_group_li' ).length ) {
                    //console.log('RELID is sibling or child of OLDID');
                    if ( el.find( '#' + relid + '_section_group_li' ).parents( '#' + oldid + '_section_group_li' ).length ) {
                        //console.log('child of oldid');
                        el.find( '#' + oldid + '_section_group_li' ).addClass( 'activeChild' ).removeClass( 'active' );
                    } else {
                        //console.log('sibling');
                        el.find( '#' + relid + '_section_group_li' ).addClass( 'active' );
                        el.find( '#' + oldid + '_section_group_li' ).removeClass( 'active' );
                    }
                    el.find( '#' + relid + '_section_group_li' ).removeClass( 'activeChild' ).addClass( 'active' );
                } else {
                    el.find( '#' + relid + '_section_group_li' ).addClass( 'active' ).removeClass( 'activeChild' ).find( 'ul.subsection' ).slideDown();

                    if ( el.find( '#' + oldid + '_section_group_li' ).find( 'ul.subsection' ).length ) {
                        //console.log('oldid is parent');
                        //console.log('#' + relid + '_section_group_li');

                        el.find( '#' + oldid + '_section_group_li' ).find( 'ul.subsection' ).slideUp(
                            'fast', function() {
                                el.find( '#' + oldid + '_section_group_li' ).removeClass( 'active' ).removeClass( 'activeChild' );
                            }
                        );
                        var newParent = el.find( '#' + relid + '_section_group_li' ).parents( '.hasSubSections:first' );

                        if ( newParent.length > 0 ) {
                            el.find( '#' + relid + '_section_group_li' ).removeClass( 'active' );
                            relid = newParent.find( '.redux-group-tab-link-a:first' ).data( 'rel' );
                            //console.log(relid);
                            if ( newParent.hasClass( 'empty_section' ) ) {
                                newParent.find( '.subsection li:first' ).addClass( 'active' );
                                el.find( '#' + relid + '_section_group_li' ).removeClass( 'active' ).addClass( 'activeChild' ).find( 'ul.subsection' ).slideDown();
                                newParent = newParent.find( '.subsection li:first' );
                                relid = newParent.find( '.redux-group-tab-link-a:first' ).data( 'rel' );
                                //console.log('Empty section, do the next one?');
                            } else {
                                el.find( '#' + relid + '_section_group_li' ).addClass( 'active' ).removeClass( 'activeChild' ).find( 'ul.subsection' ).slideDown();
                            }
                        }
                    } else if ( el.find( '#' + oldid + '_section_group_li' ).parents( 'ul.subsection' ).length ) {
                        //console.log('oldid is a child');
                        if ( !el.find( '#' + oldid + '_section_group_li' ).parents( '#' + relid + '_section_group_li' ).length ) {
                            //console.log('oldid is child, but not of relid');
                            el.find( '#' + oldid + '_section_group_li' ).parents( 'ul.subsection' ).slideUp(
                                'fast', function() {
                                    el.find( '#' + oldid + '_section_group_li' ).removeClass( 'active' );
                                    el.find( '#' + oldid + '_section_group_li' ).parents( '.redux-group-tab-link-li' ).removeClass( 'active' ).removeClass( 'activeChild' );
                                    el.find( '#' + relid + '_section_group_li' ).parents( '.redux-group-tab-link-li' ).addClass( 'activeChild' ).find( 'ul.subsection' ).slideDown();
                                    el.find( '#' + relid + '_section_group_li' ).addClass( 'active' );
                                }
                            );
                        } else {
                            //console.log('oldid is child, but not of relid2');
                            el.find( '#' + oldid + '_section_group_li' ).removeClass( 'active' );
                        }
                    } else {
                        //console.log('Normal remove active from child');
                        el.find( '#' + oldid + '_section_group_li' ).removeClass( 'active' );
                        if ( el.find( '#' + relid + '_section_group_li' ).parents( '.redux-group-tab-link-li' ).length ) {
                            //console.log('here');
                            el.find( '#' + relid + '_section_group_li' ).parents( '.redux-group-tab-link-li' ).addClass( 'activeChild' ).find( 'ul.subsection' ).slideDown();
                            el.find( '#' + relid + '_section_group_li' ).addClass( 'active' );
                        }
                    }
                }

                // Show the group
                el.find( '#' + oldid + '_section_group' ).hide();

                el.find( '#' + relid + '_section_group' ).fadeIn(
                    200, function() {
                        if ( el.find( '#redux-footer' ).length !== 0 ) {
                            $.redux.stickyInfo(); // race condition fix
                        }
                        $.redux.initFields();
                    }
                );
                $( '#toplevel_page_' + redux.args.slug ).find( '.current' ).removeClass( 'current' );

            }
        );

        if ( redux.last_tab !== undefined ) {
            $( '#' + redux.last_tab + '_section_group_li_a' ).click();
            return;
        }

        var tab = decodeURI( (new RegExp( 'tab' + '=' + '(.+?)(&|$)' ).exec( location.search ) || [, ''])[1] );

        if ( tab !== "" ) {
            if ( $.cookie( "redux_current_tab_get" ) !== tab ) {
                $.cookie(
                    'redux_current_tab', tab, {
                        expires: 7,
                        path: '/'
                    }
                );
                $.cookie(
                    'redux_current_tab_get', tab, {
                        expires: 7,
                        path: '/'
                    }
                );

                $( '#' + tab + '_section_group_li' ).click();
            }
        } else if ( $.cookie( 'redux_current_tab_get' ) !== "" ) {
            $.removeCookie( 'redux_current_tab_get' );
        }

        var sTab = $( '#' + $.cookie( "redux_current_tab" ) + '_section_group_li_a' );

        // Tab the first item or the saved one
        if ( $.cookie( "redux_current_tab" ) === null || typeof ($.cookie( "redux_current_tab" )) === "undefined" || sTab.length === 0 ) {
            $( '.redux-container' ).find( '.redux-group-tab-link-a:first' ).click();
        } else {
            sTab.click();
        }

    };

    $.redux.initFields = function() {
        $( ".redux-group-tab:visible" ).find( ".redux-field-init:visible" ).each(
            function() {

                var type = $( this ).attr( 'data-type' );
                //console.log(type);
                if ( typeof redux.field_objects != 'undefined' && redux.field_objects[type] && redux.field_objects[type] ) {
                    redux.field_objects[type].init();
                }
                if ( !redux.customizer && $( this ).hasClass( 'redux_remove_th' ) ) {

                    var tr = $( this ).parents( 'tr:first' );
                    var th = tr.find( 'th:first' );
                    if ( th.html() && th.html().length > 0 ) {
                        $( this ).prepend( th.html() );
                        $( this ).find( '.redux_field_th' ).css( 'padding', '0 0 10px 0' );
                    }
                    $( this ).parent().attr( 'colspan', '2' );
                    th.remove();
                }
            }
        );
    };

    $.redux.notices = function() {
        if ( redux.errors && redux.errors.errors ) {
            $.each(
                redux.errors.errors, function( sectionID, sectionArray ) {
                    $.each(
                        sectionArray.errors, function( key, value ) {
                            $( "#" + redux.args.opt_name + '-' + value.id ).addClass( "redux-field-error" );
                            if ( $( "#" + redux.args.opt_name + '-' + value.id ).parent().find( '.redux-th-error' ).length === 0 ) {
                                $( "#" + redux.args.opt_name + '-' + value.id ).append( '<div class="redux-th-error">' + value.msg + '</div>' );
                            } else {
                                $( "#" + redux.args.opt_name + '-' + value.id ).parent().find( '.redux-th-error' ).html( value.msg ).css(
                                    'display', 'block'
                                );
                            }
                        }
                    );
                }
            );
            $( '.redux-container' ).each(
                function() {
                    var container = $( this );
                    // Ajax cleanup
                    container.find( '.redux-menu-error' ).remove();
                    var totalErrors = container.find( '.redux-field-error' ).length;
                    if ( totalErrors > 0 ) {
                        container.find( ".redux-field-errors span" ).text( totalErrors );
                        container.find( ".redux-field-errors" ).slideDown();
                        container.find( '.redux-group-tab' ).each(
                            function() {
                                var total = $( this ).find( '.redux-field-error' ).length;
                                if ( total > 0 ) {
                                    var sectionID = $( this ).attr( 'id' ).split( '_' );
                                    sectionID = sectionID[0];
                                    container.find( '.redux-group-tab-link-a[data-key="' + sectionID + '"]' ).prepend( '<span class="redux-menu-error">' + total + '</span>' );
                                    container.find( '.redux-group-tab-link-a[data-key="' + sectionID + '"]' ).addClass( "hasError" );
                                    var subParent = container.find( '.redux-group-tab-link-a[data-key="' + sectionID + '"]' ).parents( '.hasSubSections:first' );
                                    if ( subParent ) {
                                        subParent.find( '.redux-group-tab-link-a:first' ).addClass( 'hasError' );
                                    }
                                }
                            }
                        );
                    }
                }
            );
        }
        if ( redux.warnings && redux.warnings.warnings ) {
            $.each(
                redux.warnings.warnings, function( sectionID, sectionArray ) {
                    $.each(
                        sectionArray.warnings, function( key, value ) {
                            $( "#" + redux.args.opt_name + '-' + value.id ).addClass( "redux-field-warning" );
                            if ( $( "#" + redux.args.opt_name + '-' + value.id ).parent().find( '.redux-th-warning' ).length === 0 ) {
                                $( "#" + redux.args.opt_name + '-' + value.id ).append( '<div class="redux-th-warning">' + value.msg + '</div>' );
                            } else {
                                $( "#" + redux.args.opt_name + '-' + value.id ).parent().find( '.redux-th-warning' ).html( value.msg ).css(
                                    'display', 'block'
                                );
                            }
                        }
                    );
                }
            );
            $( '.redux-container' ).each(
                function() {
                    var container = $( this );
                    // Ajax cleanup
                    container.find( '.redux-menu-warning' ).remove();
                    var totalWarnings = container.find( '.redux-field-warning' ).length;
                    if ( totalWarnings > 0 ) {
                        container.find( ".redux-field-warnings span" ).text( totalWarnings );
                        container.find( ".redux-field-warnings" ).slideDown();
                        container.find( '.redux-group-tab' ).each(
                            function() {
                                var total = $( this ).find( '.redux-field-warning' ).length;
                                if ( total > 0 ) {
                                    var sectionID = $( this ).attr( 'id' ).split( '_' );
                                    sectionID = sectionID[0];
                                    container.find( '.redux-group-tab-link-a[data-key="' + sectionID + '"]' ).prepend( '<span class="redux-menu-warning">' + total + '</span>' );
                                    container.find( '.redux-group-tab-link-a[data-key="' + sectionID + '"]' ).addClass( "hasWarning" );
                                    var subParent = container.find( '.redux-group-tab-link-a[data-key="' + sectionID + '"]' ).parents( '.hasSubSections:first' );
                                    if ( subParent ) {
                                        subParent.find( '.redux-group-tab-link-a:first' ).addClass( 'hasWarning' );
                                    }
                                }
                            }
                        );
                    }
                }
            );
        }
    };

    $.redux.tabControl = function() {
        $( '.redux-section-tabs div' ).hide();
        $( '.redux-section-tabs div:first' ).show();
        $( '.redux-section-tabs ul li:first' ).addClass( 'active' );

        $( '.redux-section-tabs ul li a' ).click(
            function() {
                $( '.redux-section-tabs ul li' ).removeClass( 'active' );
                $( this ).parent().addClass( 'active' );

                var currentTab = $( this ).attr( 'href' );

                $( '.redux-section-tabs div' ).hide();
                $( currentTab ).fadeIn(
                    'medium', function() {
                        $.redux.initFields();
                    }
                );

                return false;
            }
        );
    };

    $.redux.required = function() {

        // Hide the fold elements on load ,
        // It's better to do this by PHP but there is no filter in tr tag , so is not possible
        // we going to move each attributes we may need for folding to tr tag
        $.each(
            redux.folds, function( i, v ) {
                var fieldset = $( '#' + redux.args.opt_name + '-' + i );

                fieldset.parents( 'tr:first' ).addClass( 'fold' );

                if ( v == "hide" ) {
                    fieldset.parents( 'tr:first' ).addClass( 'hide' );

                    if ( fieldset.hasClass( 'redux-container-section' ) ) {
                        var div = $( '#section-' + i );

                        if ( div.hasClass( 'redux-section-indent-start' ) ) {
                            $( '#section-table-' + i ).hide().addClass( 'hide' );
                            div.hide().addClass( 'hide' );
                        }
                    }

                    if ( fieldset.hasClass( 'redux-container-info' ) ) {
                        $( '#info-' + i ).hide().addClass( 'hide' );
                    }

                    if ( fieldset.hasClass( 'redux-container-divide' ) ) {
                        $( '#divide-' + i ).hide().addClass( 'hide' );
                    }

                    if ( fieldset.hasClass( 'redux-container-raw' ) ) {
                        var rawTable = fieldset.parents().find( 'table#' + redux.args.opt_name + '-' + i );
                        rawTable.hide().addClass( 'hide' );
                    }
                }
            }
        );
    };

    $.redux.get_container_value = function( id ) {
        var value = $( '#' + redux.args.opt_name + '-' + id ).serializeForm();

        if ( value !== null && typeof value === 'object' && value.hasOwnProperty( redux.args.opt_name ) ) {
            value = value[redux.args.opt_name][id];
        }
        if ( $( '#' + redux.args.opt_name + '-' + id ).hasClass( 'redux-container-media' ) ) {
            value = value.url;
        }
        return value;
    };

    $.redux.check_dependencies = function( variable ) {
        if ( redux.required === null ) {
            return;
        }

        var current = $( variable ),
            id = current.parents( '.redux-field:first' ).data( 'id' );

        if ( !redux.required.hasOwnProperty( id ) ) {
            return;
        }

        var container = current.parents( '.redux-field-container:first' ),
            is_hidden = container.parents( 'tr:first' ).hasClass( '.hide' );

        if ( !container.parents( 'tr:first' ).length ) {
            is_hidden = container.parents( '.customize-control:first' ).hasClass( '.hide' );
        }

        $.each(
            redux.required[id], function( child, dependents ) {

                var current = $( this ),
                    show = false,
                    childFieldset = $( '#' + redux.args.opt_name + '-' + child ),
                    tr = childFieldset.parents( 'tr:first' );

                if ( !is_hidden ) {
                    show = $.redux.check_parents_dependencies( child );
                }

                if ( show === true ) {
                    // Shim for sections
                    if ( childFieldset.hasClass( 'redux-container-section' ) ) {
                        var div = $( '#section-' + child );

                        if ( div.hasClass( 'redux-section-indent-start' ) && div.hasClass( 'hide' ) ) {
                            $( '#section-table-' + child ).fadeIn( 300 ).removeClass( 'hide' );
                            div.fadeIn( 300 ).removeClass( 'hide' );
                        }
                    }

                    if ( childFieldset.hasClass( 'redux-container-info' ) ) {
                        $( '#info-' + child ).fadeIn( 300 ).removeClass( 'hide' );
                    }

                    if ( childFieldset.hasClass( 'redux-container-divide' ) ) {
                        $( '#divide-' + child ).fadeIn( 300 ).removeClass( 'hide' );
                    }

                    if ( childFieldset.hasClass( 'redux-container-raw' ) ) {
                        var rawTable = childFieldset.parents().find( 'table#' + redux.args.opt_name + '-' + child );
                        rawTable.fadeIn( 300 ).removeClass( 'hide' );
                    }

                    tr.fadeIn(
                        300, function() {
                            $( this ).removeClass( 'hide' );
                            if ( redux.required.hasOwnProperty( child ) ) {
                                $.redux.check_dependencies( $( '#' + redux.args.opt_name + '-' + child ).children().first() );
                            }
                            $.redux.initFields();
                        }
                    );
                    if ( childFieldset.hasClass( 'redux-container-section' ) || childFieldset.hasClass( 'redux-container-info' ) ) {
                        tr.css( {display: 'none'} );
                    }
                } else if ( show === false ) {
                    tr.fadeOut(
                        100, function() {
                            $( this ).addClass( 'hide' );
                            if ( redux.required.hasOwnProperty( child ) ) {
                                //console.log('Now check, reverse: '+child);
                                $.redux.required_recursive_hide( child );
                            }
                        }
                    );
                }

                current.find( 'select, radio, input[type=checkbox]' ).trigger( 'change' );
            }
        );
    };

    $.redux.required_recursive_hide = function( id ) {
        var toFade = $( '#' + redux.args.opt_name + '-' + id ).parents( 'tr:first' );

        toFade.fadeOut(
            50, function() {
                $( this ).addClass( 'hide' );

                if ( $( '#' + redux.args.opt_name + '-' + id ).hasClass( 'redux-container-section' ) ) {
                    var div = $( '#section-' + id );
                    if ( div.hasClass( 'redux-section-indent-start' ) ) {
                        $( '#section-table-' + id ).fadeOut( 50 ).addClass( 'hide' );
                        div.fadeOut( 50 ).addClass( 'hide' );
                    }
                }

                if ( $( '#' + redux.args.opt_name + '-' + id ).hasClass( 'redux-container-info' ) ) {
                    $( '#info-' + id ).fadeOut( 50 ).addClass( 'hide' );
                }

                if ( $( '#' + redux.args.opt_name + '-' + id ).hasClass( 'redux-container-divide' ) ) {
                    $( '#divide-' + id ).fadeOut( 50 ).addClass( 'hide' );
                }

                if ( $( '#' + redux.args.opt_name + '-' + id ).hasClass( 'redux-container-raw' ) ) {
                    var rawTable = $( '#' + redux.args.opt_name + '-' + id ).parents().find( 'table#' + redux.args.opt_name + '-' + id );
                    rawTable.fadeOut( 50 ).addClass( 'hide' );
                }

                if ( redux.required.hasOwnProperty( id ) ) {
                    $.each(
                        redux.required[id], function( child ) {
                            $.redux.required_recursive_hide( child );
                        }
                    );
                }
            }
        );
    };

    $.redux.check_parents_dependencies = function( id ) {
        var show = "";

        if ( redux.required_child.hasOwnProperty( id ) ) {
            $.each(
                redux.required_child[id], function( i, parentData ) {
                    if ( $( '#' + redux.args.opt_name + '-' + parentData.parent ).parents( 'tr:first' ).hasClass( '.hide' ) ) {
                        show = false;
                    } else {
                        if ( show !== false ) {
                            var parentValue = $.redux.get_container_value( parentData.parent );
                            show = $.redux.check_dependencies_visibility( parentValue, parentData );
                        }
                    }
                }
            );
        } else {
            show = true;
        }
        return show;
    };

    $.redux.check_dependencies_visibility = function( parentValue, data ) {
        var show = false,
            checkValue_array,
            checkValue = data.checkValue,
            operation = data.operation,
            arr;

        switch ( operation ) {
            case '=':
            case 'equals':
                //                if ($.isPlainObject(parentValue)) {
                //                    var arr = Object.keys(parentValue).map(function (key) {return parentValue[key]});
                //                    parentValue = arr;
                //                }

                if ( $.isArray( parentValue ) ) {
                    $( parentValue[0] ).each(
                        function( idx, val ) {
                            if ( $.isArray( checkValue ) ) {
                                $( checkValue ).each(
                                    function( i, v ) {
                                        if ( val == v ) {
                                            show = true;
                                            return true;
                                        }
                                    }
                                );
                            } else {
                                if ( val == checkValue ) {
                                    show = true;
                                    return true;
                                }
                            }
                        }
                    );
                } else {
                    if ( $.isArray( checkValue ) ) {
                        $( checkValue ).each(
                            function( i, v ) {
                                if ( parentValue == v ) {
                                    show = true;
                                }
                            }
                        );
                    } else {
                        if ( parentValue == checkValue ) {
                            show = true;
                        }
                    }
                }
                break;

            case '!=':
            case 'not':
                if ( $.isArray( parentValue ) ) {
                    $( parentValue ).each(
                        function( idx, val ) {
                            if ( $.isArray( checkValue ) ) {
                                $( checkValue ).each(
                                    function( i, v ) {
                                        if ( val != v ) {
                                            show = true;
                                            return true;
                                        }
                                    }
                                );
                            } else {
                                if ( val != checkValue ) {
                                    show = true;
                                    return true;
                                }
                            }
                        }
                    );
                } else {
                    if ( $.isArray( checkValue ) ) {
                        $( checkValue ).each(
                            function( i, v ) {
                                if ( parentValue != v ) {
                                    show = true;
                                }
                            }
                        );
                    } else {
                        if ( parentValue != checkValue ) {
                            show = true;
                        }
                    }
                }
                break;

            case '>':
            case 'greater':
            case 'is_larger':
                if ( parseFloat( parentValue ) > parseFloat( checkValue ) ) {
                    show = true;
                }
                break;

            case '>=':
            case 'greater_equal':
            case 'is_larger_equal':
                if ( parseFloat( parentValue ) >= parseFloat( checkValue ) ) {
                    show = true;
                }
                break;

            case '<':
            case 'less':
            case 'is_smaller':
                if ( parseFloat( parentValue ) < parseFloat( checkValue ) ) {
                    show = true;
                }
                break;

            case '<=':
            case 'less_equal':
            case 'is_smaller_equal':
                if ( parseFloat( parentValue ) <= parseFloat( checkValue ) ) {
                    show = true;
                }
                break;

            case 'contains':
                if ( $.isPlainObject( parentValue ) ) {
                    parentValue = Object.keys( parentValue ).map(
                        function( key ) {
                            return [key, parentValue[key]];
                        }
                    );
                }

                if ( $.isPlainObject( checkValue ) ) {
                    checkValue = Object.keys( checkValue ).map(
                        function( key ) {
                            return [key, checkValue[key]];
                        }
                    );
                }

                if ( $.isArray( checkValue ) ) {
                    $( checkValue ).each(
                        function( idx, val ) {
                            var breakMe = false;
                            var toFind = val[0];
                            var findVal = val[1];

                            $( parentValue ).each(
                                function( i, v ) {
                                    var toMatch = v[0];
                                    var matchVal = v[1];

                                    if ( toFind === toMatch ) {
                                        if ( findVal == matchVal ) {
                                            show = true;
                                            breakMe = true;

                                            return false;
                                        }
                                    }
                                }
                            );

                            if ( breakMe === true ) {
                                return false;
                            }
                        }
                    );
                } else {
                    if ( parentValue.toString().indexOf( checkValue ) !== -1 ) {
                        show = true;
                    }
                }
                break;

            case 'doesnt_contain':
            case 'not_contain':
                if ( $.isPlainObject( parentValue ) ) {
                    arr = Object.keys( parentValue ).map(
                        function( key ) {
                            return parentValue[key];
                        }
                    );
                    parentValue = arr;
                }

                if ( $.isPlainObject( checkValue ) ) {
                    arr = Object.keys( checkValue ).map(
                        function( key ) {
                            return checkValue[key];
                        }
                    );
                    checkValue = arr;
                }

                if ( $.isArray( checkValue ) ) {
                    $( checkValue ).each(
                        function( idx, val ) {
                            if ( parentValue.toString().indexOf( val ) === -1 ) {
                                show = true;
                            }
                        }
                    );
                } else {
                    if ( parentValue.toString().indexOf( checkValue ) === -1 ) {
                        show = true;
                    }
                }
                break;

            case 'is_empty_or':
                if ( parentValue === "" || parentValue == checkValue ) {
                    show = true;
                }
                break;

            case 'not_empty_and':
                if ( parentValue !== "" && parentValue != checkValue ) {
                    show = true;
                }
                break;

            case 'is_empty':
            case 'empty':
            case '!isset':
                if ( !parentValue || parentValue === "" || parentValue === null ) {
                    show = true;
                }
                break;

            case 'not_empty':
            case '!empty':
            case 'isset':
                if ( parentValue && parentValue !== "" && parentValue !== null ) {
                    show = true;
                }
                break;
        }
        return show;

    };

    $.redux.verifyPos = function( s, b ) {

        // trim off spaces
        s = s.replace( /^\s+|\s+$/gm, '' );

        // position value is blank, set the default
        if ( s === '' || s.search( ' ' ) == -1 ) {
            if ( b === true ) {
                return 'top left';
            } else {
                return 'bottom right';
            }
        }

        // split string into array
        var split = s.split( ' ' );

        // Evaluate first string.  Must be top, center, or bottom
        var paramOne = b ? 'top' : 'bottom';
        if ( split[0] == 'top' || split[0] == 'center' || split[0] == 'bottom' ) {
            paramOne = split[0];
        }

        // Evaluate second string.  Must be left, center, or right.
        var paramTwo = b ? 'left' : 'right';
        if ( split[1] == 'left' || split[1] == 'center' || split[1] == 'right' ) {
            paramTwo = split[1];
        }

        return paramOne + ' ' + paramTwo;
    };

    $.redux.stickyInfo = function() {
        var stickyWidth = $( '.redux-main' ).innerWidth() - 20;

        if ( !$( '#info_bar' ).isOnScreen() && !$( '#redux-footer-sticky' ).isOnScreen() ) {
            $( '#redux-footer' ).css(
                {
                    position: 'fixed',
                    bottom: '0',
                    width: stickyWidth,
                    right: 21
                }
            );
            $( '#redux-footer' ).addClass( 'sticky-footer-fixed' );
            $( '.redux-save-warn' ).css( 'left', $( '#redux-sticky' ).offset().left );
            $( '#redux-sticky-padder' ).show();
        } else {
            $( '#redux-footer' ).css(
                {
                    background: '#eee',
                    position: 'inherit',
                    bottom: 'inherit',
                    width: 'inherit'
                }
            );
            $( '#redux-sticky-padder' ).hide();
            $( '#redux-footer' ).removeClass( 'sticky-footer-fixed' );
        }
        if ( !$( '#info_bar' ).isOnScreen() ) {
            $( '#redux-sticky' ).addClass( 'sticky-save-warn' );
        } else {
            $( '#redux-sticky' ).removeClass( 'sticky-save-warn' );
        }
    };

    $.redux.expandOptions = function( parent ) {
        var trigger = parent.find( '.expand_options' );
        var width = parent.find( '.redux-sidebar' ).width() - 1;
        var id = $( '.redux-group-menu .active a' ).data( 'rel' ) + '_section_group';

        if ( trigger.hasClass( 'expanded' ) ) {
            trigger.removeClass( 'expanded' );
            parent.find( '.redux-main' ).removeClass( 'expand' );

            parent.find( '.redux-sidebar' ).stop().animate(
                {
                    'margin-left': '0px'
                }, 500
            );

            parent.find( '.redux-main' ).stop().animate(
                {
                    'margin-left': width
                }, 500, function() {
                    parent.find( '.redux-main' ).attr( 'style', '' );
                }
            );

            parent.find( '.redux-group-tab' ).each(
                function() {
                    if ( $( this ).attr( 'id' ) !== id ) {
                        $( this ).fadeOut( 'fast' );
                    }
                }
            );
            // Show the only active one
        } else {
            trigger.addClass( 'expanded' );
            parent.find( '.redux-main' ).addClass( 'expand' );

            parent.find( '.redux-sidebar' ).stop().animate(
                {
                    'margin-left': -width - 113
                }, 500
            );

            parent.find( '.redux-main' ).stop().animate(
                {
                    'margin-left': '-1px'
                }, 500
            );

            parent.find( '.redux-group-tab' ).fadeIn(
                'medium', function() {
                    $.redux.initFields();
                }
            );
        }
        return false;
    };


    $.redux.scaleToRatio = function( el, maxHeight, maxWidth ) {
        var ratio = 0;  // Used for aspect ratio

        var width = el.attr( 'data-width' );
        if ( !width ) {
            width = el.width();
            el.attr( 'data-width', width );
        }
        var height = el.attr( 'data-height' );
        var eHeight = el.height();
        if ( !height || eHeight > height ) {
            height = eHeight;
            el.attr( 'data-height', height );
            el.css( "width", 'auto' );
            el.attr( 'data-width', el.width() );
            width = el.width();
        }


        // Check if the current width is larger than the max
        if ( width > maxWidth ) {
            ratio = maxWidth / width;   // get ratio for scaling image
            el.css( "width", maxWidth ); // Set new width
            el.css( "height", height * ratio );  // Scale height based on ratio
            height = height * ratio;    // Reset height to match scaled image
            width = width * ratio;    // Reset width to match scaled image

        } else {
            el.css( "width", 'auto' );   // Set new height

        }

        // Check if current height is larger than max
        if ( height > maxHeight ) {
            ratio = maxHeight / height; // get ratio for scaling image
            el.css( "height", maxHeight );   // Set new height
            el.css( "width", width * ratio );    // Scale width based on ratio
            width = width * ratio;    // Reset width to match scaled image
            height = height * ratio;    // Reset height to match scaled image


        } else {
            el.css( "height", 'auto' );   // Set new height

        }

        var test = ($( document.getElementById( 'redux-header' ) ).height() - el.height()) / 2;
        if ( test > 0 ) {
            el.css( "margin-top", test );
        } else {
            el.css( "margin-top", 0 );
        }

        if ( $( '#redux-header .redux_field_search' ) ) {
            $( '#redux-header .redux_field_search' ).css( 'right', ($( el ).width() + 20) );
        }


    };
    $.redux.resizeAds = function() {
        var el = $( '#redux-header' );
        var maxWidth;
        if ( el.length ) {
            maxWidth = el.width() - el.find( '.display_header' ).width() - 30;
        } else {
            el = $( '#customize-info' );
            maxWidth = el.width();
        }

        var maxHeight = el.height();
        var rAds = el.find( '.rAds' );

        $( rAds ).find( 'video' ).each(
            function() {
                $.redux.scaleToRatio( $( this ), maxHeight, maxWidth );
            }
        );
        $( rAds ).find( 'img' ).each(
            function() {
                $.redux.scaleToRatio( $( this ), maxHeight, maxWidth );
            }
        );
        $( rAds ).find( 'div' ).each(
            function() {
                $.redux.scaleToRatio( $( this ), maxHeight, maxWidth );
            }
        );

        if ( rAds.css( 'left' ) == "-99999px" ) {
            rAds.css( 'display', 'none' ).css( 'left', 'auto' );
        }
        rAds.fadeIn( 'slow' );
    };
    $( document ).ready(
        function() {
            if ( redux.rAds ) {
                setTimeout(
                    function() {
                        var el;
                        if ( $( '#redux-header' ).length > 0 ) {
                            $( '#redux-header' ).append( '<div class="rAds"></div>' );
                            el = $( '#redux-header' );
                        } else {
                            $( '#customize-theme-controls ul' ).first().prepend( '<li id="redux_rAds" class="accordion-section rAdsContainer" style="position: relative;"><div class="rAds"></div></li>' );
                            el = $( '#redux_rAds' );
                        }

                        el.css( 'position', 'relative' );

                        el.find( '.rAds' ).attr(
                            'style',
                            'position:absolute; top: 6px; right: 6px; display:block !important;overflow:hidden;'
                        ).css( 'left', '-99999px' );
                        el.find( '.rAds' ).html( redux.rAds.replace( /<br\s?\/?>/, '' ) );
                        var rAds = el.find( '.rAds' );

                        var maxHeight = el.height();
                        var maxWidth = el.width() - el.find( '.display_header' ).width() - 30;

                        rAds.find( 'a' ).css( 'float', 'right' ).css( 'line-height', el.height() + 'px' ).css(
                            'margin-left', '5px'
                        );

                        $( document ).ajaxComplete(
                            function() {
                                rAds.find( 'a' ).hide();
                                setTimeout(
                                    function() {
                                        $.redux.resizeAds();
                                        rAds.find( 'a' ).fadeIn();
                                    }, 1400
                                );
                                setTimeout(
                                    function() {
                                        $.redux.resizeAds();

                                    }, 1500
                                );
                                $( document ).unbind( 'ajaxComplete' );
                            }
                        );

                        $( window ).resize(
                            function() {
                                $.redux.resizeAds();
                            }
                        );
                    }, 400
                );

            }
        }
    );
})( jQuery );

jQuery.noConflict();

var confirmOnPageExit = function( e ) {
    //return; // ONLY FOR DEBUGGING
    // If we haven't been passed the event get the window.event
    e = e || window.event;

    var message = redux.args.save_pending;

    // For IE6-8 and Firefox prior to version 4
    if ( e ) {
        e.returnValue = message;
    }

    window.onbeforeunload = null;

    // For Chrome, Safari, IE8+ and Opera 12+
    return message;
};

function redux_change( variable ) {

    jQuery( 'body' ).trigger( 'check_dependencies', variable );

    if ( variable.hasClass( 'compiler' ) ) {
        jQuery( '#redux-compiler-hook' ).val( 1 );
    }

//    var test = jQuery( variable ).parents( '.redux-field-container:first' );
//    if ( test.hasClass( 'redux-container-typography' ) && redux.field_objects.typography ) {
//        redux.field_objects.typography.change( test );
//    }

    var rContainer = jQuery( variable ).parents( '.redux-container:first' );

    var parentID = jQuery( variable ).closest( '.redux-group-tab' ).attr( 'id' );

    // Let's count down the errors now. Fancy.  ;)
    var id = parentID.split( '_' );
    id = id[0];

    var th = rContainer.find( '.redux-group-tab-link-a[data-key="' + id + '"]' ).parents( '.redux-group-tab-link-li:first' );
    var subParent = jQuery( '#' + parentID + '_li' ).parents( '.hasSubSections:first' );

    if ( jQuery( variable ).parents( 'fieldset.redux-field:first' ).hasClass( 'redux-field-error' ) ) {
        jQuery( variable ).parents( 'fieldset.redux-field:first' ).removeClass( 'redux-field-error' );
        jQuery( variable ).parent().find( '.redux-th-error' ).slideUp();

        var errorCount = (parseInt( rContainer.find( '.redux-field-errors span' ).text() ) - 1);

        if ( errorCount <= 0 ) {
            //console.log('HERE');
            jQuery( '#' + parentID + '_li .redux-menu-error' ).fadeOut( 'fast' ).remove();
            jQuery( '#' + parentID + '_li .redux-group-tab-link-a' ).removeClass( 'hasError' );

            jQuery( '#' + parentID + '_li' ).parents( '.inside:first' ).find( '.redux-field-errors' ).slideUp();
            jQuery( variable ).parents( '.redux-container:first' ).find( '.redux-field-errors' ).slideUp();
            jQuery( '#redux_metaboxes_errors' ).slideUp();
        } else {

            var errorsLeft = (parseInt( th.find( '.redux-menu-error:first' ).text() ) - 1);
            if ( errorsLeft <= 0 ) {
                th.find( '.redux-menu-error:first' ).fadeOut().remove();
            } else {
                th.find( '.redux-menu-error:first' ).text( errorsLeft );
            }

            rContainer.find( '.redux-field-errors span' ).text( errorCount );
        }

        if ( subParent.length !== 0 ) {
            if ( subParent.find( '.redux-menu-error' ).length === 0 ) {
                subParent.find( '.hasError' ).removeClass( 'hasError' );
            }
        }
    }
    if ( jQuery( variable ).parents( 'fieldset.redux-field:first' ).hasClass( 'redux-field-warning' ) ) {
        jQuery( variable ).parents( 'fieldset.redux-field:first' ).removeClass( 'redux-field-warning' );
        jQuery( variable ).parent().find( '.redux-th-warning' ).slideUp();

        var warningCount = (parseInt( rContainer.find( '.redux-field-warnings span' ).text() ) - 1);

        if ( warningCount <= 0 ) {
            //console.log('HERE');
            jQuery( '#' + parentID + '_li .redux-menu-warning' ).fadeOut( 'fast' ).remove();
            jQuery( '#' + parentID + '_li .redux-group-tab-link-a' ).removeClass( 'hasWarning' );

            jQuery( '#' + parentID + '_li' ).parents( '.inside:first' ).find( '.redux-field-warnings' ).slideUp();
            jQuery( variable ).parents( '.redux-container:first' ).find( '.redux-field-warnings' ).slideUp();
            jQuery( '#redux_metaboxes_warnings' ).slideUp();
        } else {
            // Let's count down the warnings now. Fancy.  ;)

            var warningsLeft = (parseInt( th.find( '.redux-menu-warning:first' ).text() ) - 1);
            if ( warningsLeft <= 0 ) {
                th.find( '.redux-menu-warning:first' ).fadeOut().remove();
            } else {
                th.find( '.redux-menu-warning:first' ).text( warningsLeft );
            }

            rContainer.find( '.redux-field-warning span' ).text( warningCount );

        }
        if ( subParent.length !== 0 ) {
            if ( subParent.find( '.redux-menu-warning' ).length === 0 ) {
                subParent.find( '.hasWarning' ).removeClass( 'hasWarning' );
            }
        }
    }
    // Don't show the changed value notice while save_notice is visible.
    if ( rContainer.find( '.saved_notice:visible' ).length > 0 ) {
        return;
    }


    if ( redux.customizer ) {
        redux.customizer.save( variable, rContainer, parentID );
        return;
    }

    if ( !redux.args.disable_save_warn ) {
        rContainer.find( '.redux-save-warn' ).slideDown();
        window.onbeforeunload = confirmOnPageExit;
    }
}

function colorValidate( field ) {
    var value = jQuery( field ).val();

    var hex = colorNameToHex( value );
    if ( hex !== value.replace( '#', '' ) ) {
        return hex;
    }

    return value;
}

function colorNameToHex( colour ) {
    var tcolour = colour.replace( /^\s\s*/, '' ).replace( /\s\s*$/, '' ).replace( "#", "" );

    var colours = {
        "aliceblue": "#f0f8ff",
        "antiquewhite": "#faebd7",
        "aqua": "#00ffff",
        "aquamarine": "#7fffd4",
        "azure": "#f0ffff",
        "beige": "#f5f5dc",
        "bisque": "#ffe4c4",
        "black": "#000000",
        "blanchedalmond": "#ffebcd",
        "blue": "#0000ff",
        "blueviolet": "#8a2be2",
        "brown": "#a52a2a",
        "burlywood": "#deb887",
        "cadetblue": "#5f9ea0",
        "chartreuse": "#7fff00",
        "chocolate": "#d2691e",
        "coral": "#ff7f50",
        "cornflowerblue": "#6495ed",
        "cornsilk": "#fff8dc",
        "crimson": "#dc143c",
        "cyan": "#00ffff",
        "darkblue": "#00008b",
        "darkcyan": "#008b8b",
        "darkgoldenrod": "#b8860b",
        "darkgray": "#a9a9a9",
        "darkgreen": "#006400",
        "darkkhaki": "#bdb76b",
        "darkmagenta": "#8b008b",
        "darkolivegreen": "#556b2f",
        "darkorange": "#ff8c00",
        "darkorchid": "#9932cc",
        "darkred": "#8b0000",
        "darksalmon": "#e9967a",
        "darkseagreen": "#8fbc8f",
        "darkslateblue": "#483d8b",
        "darkslategray": "#2f4f4f",
        "darkturquoise": "#00ced1",
        "darkviolet": "#9400d3",
        "deeppink": "#ff1493",
        "deepskyblue": "#00bfff",
        "dimgray": "#696969",
        "dodgerblue": "#1e90ff",
        "firebrick": "#b22222",
        "floralwhite": "#fffaf0",
        "forestgreen": "#228b22",
        "fuchsia": "#ff00ff",
        "gainsboro": "#dcdcdc",
        "ghostwhite": "#f8f8ff",
        "gold": "#ffd700",
        "goldenrod": "#daa520",
        "gray": "#808080",
        "green": "#008000",
        "greenyellow": "#adff2f",
        "honeydew": "#f0fff0",
        "hotpink": "#ff69b4",
        "indianred ": "#cd5c5c",
        "indigo ": "#4b0082",
        "ivory": "#fffff0",
        "khaki": "#f0e68c",
        "lavender": "#e6e6fa",
        "lavenderblush": "#fff0f5",
        "lawngreen": "#7cfc00",
        "lemonchiffon": "#fffacd",
        "lightblue": "#add8e6",
        "lightcoral": "#f08080",
        "lightcyan": "#e0ffff",
        "lightgoldenrodyellow": "#fafad2",
        "lightgrey": "#d3d3d3",
        "lightgreen": "#90ee90",
        "lightpink": "#ffb6c1",
        "lightsalmon": "#ffa07a",
        "lightseagreen": "#20b2aa",
        "lightskyblue": "#87cefa",
        "lightslategray": "#778899",
        "lightsteelblue": "#b0c4de",
        "lightyellow": "#ffffe0",
        "lime": "#00ff00",
        "limegreen": "#32cd32",
        "linen": "#faf0e6",
        "magenta": "#ff00ff",
        "maroon": "#800000",
        "mediumaquamarine": "#66cdaa",
        "mediumblue": "#0000cd",
        "mediumorchid": "#ba55d3",
        "mediumpurple": "#9370d8",
        "mediumseagreen": "#3cb371",
        "mediumslateblue": "#7b68ee",
        "mediumspringgreen": "#00fa9a",
        "mediumturquoise": "#48d1cc",
        "mediumvioletred": "#c71585",
        "midnightblue": "#191970",
        "mintcream": "#f5fffa",
        "mistyrose": "#ffe4e1",
        "moccasin": "#ffe4b5",
        "navajowhite": "#ffdead",
        "navy": "#000080",
        "oldlace": "#fdf5e6",
        "olive": "#808000",
        "olivedrab": "#6b8e23",
        "orange": "#ffa500",
        "orangered": "#ff4500",
        "orchid": "#da70d6",
        "palegoldenrod": "#eee8aa",
        "palegreen": "#98fb98",
        "paleturquoise": "#afeeee",
        "palevioletred": "#d87093",
        "papayawhip": "#ffefd5",
        "peachpuff": "#ffdab9",
        "peru": "#cd853f",
        "pink": "#ffc0cb",
        "plum": "#dda0dd",
        "powderblue": "#b0e0e6",
        "purple": "#800080",
        "red": "#ff0000",
        "redux": "#01a3e3",
        "rosybrown": "#bc8f8f",
        "royalblue": "#4169e1",
        "saddlebrown": "#8b4513",
        "salmon": "#fa8072",
        "sandybrown": "#f4a460",
        "seagreen": "#2e8b57",
        "seashell": "#fff5ee",
        "sienna": "#a0522d",
        "silver": "#c0c0c0",
        "skyblue": "#87ceeb",
        "slateblue": "#6a5acd",
        "slategray": "#708090",
        "snow": "#fffafa",
        "springgreen": "#00ff7f",
        "steelblue": "#4682b4",
        "tan": "#d2b48c",
        "teal": "#008080",
        "thistle": "#d8bfd8",
        "tomato": "#ff6347",
        "turquoise": "#40e0d0",
        "violet": "#ee82ee",
        "wheat": "#f5deb3",
        "white": "#ffffff",
        "whitesmoke": "#f5f5f5",
        "yellow": "#ffff00",
        "yellowgreen": "#9acd32"
    };

    if ( colours[tcolour.toLowerCase()] !== 'undefined' ) {
        return colours[tcolour.toLowerCase()];
    }

    return colour;
}

function redux_hook( object, functionName, callback, before ) {
    (function( originalFunction ) {
        object[functionName] = function() {

            if ( before === true ) {
                callback.apply( this, [returnValue, originalFunction, arguments] );
            }
            var returnValue = originalFunction.apply( this, arguments );
            if ( before !== true ) {
                callback.apply( this, [returnValue, originalFunction, arguments] );
            }

            return returnValue;
        };
    }( object[functionName] ));
}