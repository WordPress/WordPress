"use strict";
(function ($) {
    $.fn.tabSlideOut = function (callerSettings) {

        /**
         * @param node Element to get the height of.
         * @return string e.g. '123px'
         */
        function heightAsString(node) {
            return parseInt(node.outerHeight() + 1, 10) + 'px';
        }
        /**
         * @param node Element to get the width of.
         * @return string e.g. '123px'
         */
        function widthAsString(node) {
            return parseInt(node.outerWidth() + 1, 10) + 'px';
        }

        /*
         * Get the width of the given border, in pixels.
         * 
         * @param node element
         * @param string edge
         * @returns int
         */
        function borderWidth(element, edge) {
            return parseInt(element.css('border-' + edge + '-width'), 10);
        }

        /**
         * Return the desired height of the panel to maintain both offsets.
         */
        function calculatePanelSize() {
            var available = $(window).height();
            if (edge === 'top' || edge === 'bottom') {
                available = $(window).width();
            }
            return available - parseInt(settings.otherOffset) - parseInt(settings.offset);
        }

        var panel = this;

        /**
         * True if the tab is open.
         * 
         * @returns boolean
         */
        function isOpen() {
            return panel.hasClass('ui-slideouttab-open');
        }

        if (typeof callerSettings == 'string')
        {
            // param is a string, use command mode
            switch (callerSettings)
            {
                case 'open':
                    this.trigger('open');
                    return this;
                case 'close':
                    this.trigger('close');
                    return this;
                case 'isOpen':
                    return isOpen();
                case 'toggle':
                    this.trigger('toggle');
                    return this;
                case 'bounce':
                    this.trigger('bounce');
                    return this;
                default:
                    throw "Invalid tabSlideOut command";
            }
        } else
        {
            // param is an object, it's initialisation mode
            var settings = $.extend({
                tabLocation: 'left', // left, right, top or bottom
                tabHandle: '.handle', // JQuery selector for the tab, can use any JQuery selector
                action: 'click', // action which will open the panel, e.g. 'hover'
                hoverTimeout: 5000, // ms to keep tab open after no longer hovered - only if action = 'hover'
                offset: '200px', // panel dist from top or left (bottom or right if offsetReverse is true)
                offsetReverse: false, // if true, panel is offset from  right or bottom of window instead of left or top
                otherOffset: null, // if set, panel size is also set to maintain this dist from bottom or right of view port (top or left if offsetReverse)
                handleOffset: null, // e.g. '10px'. If null, detects panel border to align handle nicely on edge
                handleOffsetReverse: false, // if true, handle is offset from right or bottom of panel instead of left or top
                bounceDistance: '50px', // how far bounce event will move everything
                bounceTimes: 4, // how many bounces when 'bounce' is called
                bounceSpeed: 300, // time to animate bounces
                tabImage: null, // optional image to show in the tab
                tabImageHeight: null, // optional IE8 and lower only, else autodetected size
                tabImageWidth: null, // optional IE8 and lower only, else autodetected size
                onLoadSlideOut: false, // slide out after DOM load
                clickScreenToClose: true, // close tab when somewhere outside the tab is clicked
                clickScreenToCloseFilters: ['.ui-slideouttab-panel'], // if click target or parents match any of these, click won't close this tab
                onOpen: function () {}, // handler called after opening
                onClose: function () {}, // handler called after closing
                onSlide: function () {}, // handler called after opening or closing
                onBeforeOpen: function () {
                    return true;
                }, // handler called before opening, return false to cancel
                onBeforeClose: function () {
                    return true;
                }, // handler called before closing, return false to cancel
                onBeforeSlide: function () {
                    return true;
                } // handler called before opening or closing, return false to cancel
            }, callerSettings || {});

            var edge = settings.tabLocation;
            var handle = settings.tabHandle = $(settings.tabHandle, panel);

            panel.addClass('ui-slideouttab-panel')
                    .addClass('ui-slideouttab-' + edge);
            if (settings.offsetReverse)
                panel.addClass('ui-slideouttab-panel-reverse');
            handle.addClass('ui-slideouttab-handle'); // need this to find it later
            if (settings.handleOffsetReverse)
                handle.addClass('ui-slideouttab-handle-reverse');
            settings.toggleButton = $(settings.toggleButton);

            // apply an image to the tab if one is defined
            if (settings.tabImage !== null) {
                var imageHeight = 0;
                var imageWidth = 0;
                if (settings.tabImageHeight !== null && settings.tabImageWidth !== null) {
                    imageHeight = settings.tabImageHeight;
                    imageWidth = settings.tabImageWidth;
                } else {
                    var img = new Image();
                    img.src = settings.tabImage;
                    imageHeight = img.naturalHeight;
                    imageWidth = img.naturalWidth;
                }

                handle.addClass('ui-slideouttab-handle-image');
                handle.css({
                    'background': 'url(' + settings.tabImage + ') no-repeat',
                    'width': imageWidth,
                    'height': imageHeight
                });
            }

            // determine whether panel and handle are positioned from top, bottom, left, or right
            if (edge === 'top' || edge === 'bottom') {
                settings.panelOffsetFrom =
                        settings.offsetReverse ? 'right' : 'left';
                settings.handleOffsetFrom =
                        settings.handleOffsetReverse ? 'right' : 'left';
            } else {
                settings.panelOffsetFrom =
                        settings.offsetReverse ? 'bottom' : 'top';
                settings.handleOffsetFrom =
                        settings.handleOffsetReverse ? 'bottom' : 'top';
            }

            /* autodetect the correct offset for the handle using appropriate panel border*/
            if (settings.handleOffset === null) {
                settings.handleOffset = '-' + borderWidth(panel, settings.handleOffsetFrom) + 'px';
            }

            if (edge === 'top' || edge === 'bottom') {
                /* set left or right edges */
                panel.css(settings.panelOffsetFrom, settings.offset);
                handle.css(settings.handleOffsetFrom, settings.handleOffset);

                // possibly drive the panel size
                if (settings.otherOffset !== null) {
                    panel.css('width', calculatePanelSize() + 'px');
                    // install resize handler
                    $(window).resize(function () {
                        panel.css('width', calculatePanelSize() + 'px');
                    });
                }

                if (edge === 'top') {
                    handle.css({'bottom': '-' + heightAsString(handle)});
                } else {
                    handle.css({'top': '-' + heightAsString(handle)});
                }
            } else {
                /* set top or bottom edge */
                panel.css(settings.panelOffsetFrom, settings.offset);
                handle.css(settings.handleOffsetFrom, settings.handleOffset);

                // possibly drive the panel size
                if (settings.otherOffset !== null) {
                    panel.css('height', calculatePanelSize() + 'px');
                    // install resize handler
                    $(window).resize(function () {
                        panel.css('height', calculatePanelSize() + 'px');
                    });
                }

                if (edge === 'left') {
                    handle.css({'right': '0'});
                } else {
                    handle.css({'left': '0'});
                }
            }

            handle.on('click', function (event) {
                event.preventDefault();
            });
            settings.toggleButton.on('click', function (event) {
                event.preventDefault();
            });

            // now everything is set up, add the class which enables CSS tab animation
            panel.addClass('ui-slideouttab-ready');

            var close = function () {
                if (settings.onBeforeSlide() && settings.onBeforeClose()) {
                    panel.removeClass('ui-slideouttab-open').trigger('slideouttabclose');
                    settings.onSlide();
                    settings.onClose();
                }
            };

            var open = function () {
                if (settings.onBeforeSlide() && settings.onBeforeOpen()) {
                    panel.addClass('ui-slideouttab-open').trigger('slideouttabopen');
                    settings.onSlide();
                    settings.onOpen();
                }
            };

            var toggle = function () {
                if (isOpen()) {
                    close();
                } else {
                    open();
                }
            };

            // animate the tab in and out when 'bounced'
            var moveIn = [];
            moveIn[edge] = '-=' + settings.bounceDistance;
            var moveOut = [];
            moveOut[edge] = '+=' + settings.bounceDistance;

            var bounceIn = function () {
                var temp = panel;
                for (var i = 0; i < settings.bounceTimes; i++)
                {
                    temp = temp.animate(moveIn, settings.bounceSpeed)
                            .animate(moveOut, settings.bounceSpeed);
                }
                panel.trigger('slideouttabbounce');
            };

            var bounceOut = function () {
                var temp = panel;
                for (var i = 0; i < settings.bounceTimes; i++)
                {
                    temp = temp.animate(moveOut, settings.bounceSpeed)
                            .animate(moveIn, settings.bounceSpeed);
                }
                panel.trigger('slideouttabbounce');
            };

            // handle clicks in rest of document to close tabs if they're open
            if (settings.clickScreenToClose) {
                // install a click handler to close tab if anywhere outside the tab is clicked,
                // that isn't filtered out by the configured filters
                $(document).on('click', function (event) {
                    // first check the tab is open and the click isn't inside it
                    if (isOpen() && !panel[0].contains(event.target)) {
                        // something other than this panel was clicked
                        var clicked = $(event.target);

                        // check to see if any filters return true
                        for (var i = 0; i < settings.clickScreenToCloseFilters.length; i++) {
                            var filter = settings.clickScreenToCloseFilters[i];
                            if (typeof filter === 'string') {
                                // checked clicked element itself, and all parents
                                if (clicked.is(filter) || clicked.parents().is(filter)) {
                                    return; // don't close the tab
                                }
                            } else if (typeof filter === 'function') {
                                // call custom filter
                                if (filter.call(panel, event))
                                    return; // don't close the tab
                            }
                        }

                        // we haven't returned true from any filter, so close the tab
                        close();
                    }
                });
            }
            ;

            //choose which type of action to bind
            if (settings.action === 'click') {
                handle.on('click', function (event) {
                    toggle();
                });
            } else if (settings.action === 'hover') {
                var timer = null;
                panel.hover(
                        function () {
                            if (!isOpen()) {
                                open();
                            }
                            timer = null; // eliminate the timer, ensure we don't close now
                        },
                        function () {
                            if (isOpen() && timer === null) {
                                timer = setTimeout(function () {
                                    if (timer)
                                        close();
                                    timer = null;
                                }, settings.hoverTimeout);
                            }
                        });

                handle.on('click', function (event) {
                    if (isOpen()) {
                        close();
                    }
                });
            }

            if (settings.onLoadSlideOut) {
                open();
                setTimeout(open, 500);
            }

            // custom event handlers -------
            panel.on('open', function (event) {
                if (!isOpen()) {
                    open();
                }
            });
            panel.on('close', function (event) {
                if (isOpen()) {
                    close();
                }
            });
            panel.on('toggle', function (event) {
                toggle();
            });
            panel.on('bounce', function (event) {
                if (isOpen()) {
                    bounceIn();
                } else {
                    bounceOut();
                }
            });

        }
        return this;
    };
})(jQuery);
