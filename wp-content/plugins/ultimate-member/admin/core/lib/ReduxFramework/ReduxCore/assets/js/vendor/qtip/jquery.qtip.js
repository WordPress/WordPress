/*
 * qTip2 - Pretty powerful tooltips - v2.2.1
 * http://qtip2.com
 *
 * Copyright (c) 2014 
 * Released under the MIT licenses
 * http://jquery.org/license
 *
 * Date: Sat Sep 6 2014 11:12 GMT+0100+0100
 * Plugins: tips modal viewport svg imagemap ie6
 * Styles: core basic css3
 */
/*global window: false, jQuery: false, console: false, define: false */

/* Cache window, document, undefined */
(function( window, document, undefined ) {

    // Uses AMD or browser globals to create a jQuery plugin.
    (function( factory ) {
        "use strict";
        if(typeof define === 'function' && define.amd) {
            define(['jquery'], factory);
        }
        else if(jQuery && !jQuery.fn.qtip) {
            factory(jQuery);
        }
    }
    (function($) {
        "use strict"; // Enable ECMAScript "strict" operation for this function. See more: http://ejohn.org/blog/ecmascript-5-strict-mode-json-and-more/
        ;// Munge the primitives - Paul Irish tip
        var TRUE = true,
            FALSE = false,
            NULL = null,

        // Common variables
            X = 'x', Y = 'y',
            WIDTH = 'width',
            HEIGHT = 'height',

        // Positioning sides
            TOP = 'top',
            LEFT = 'left',
            BOTTOM = 'bottom',
            RIGHT = 'right',
            CENTER = 'center',

        // Position adjustment types
            FLIP = 'flip',
            FLIPINVERT = 'flipinvert',
            SHIFT = 'shift',

        // Shortcut vars
            QTIP, PROTOTYPE, CORNER, CHECKS,
            PLUGINS = {},
            NAMESPACE = 'qtip',
            ATTR_HAS = 'data-hasqtip',
            ATTR_ID = 'data-qtip-id',
            WIDGET = ['ui-widget', 'ui-tooltip'],
            SELECTOR = '.'+NAMESPACE,
            INACTIVE_EVENTS = 'click dblclick mousedown mouseup mousemove mouseleave mouseenter'.split(' '),

            CLASS_FIXED = NAMESPACE+'-fixed',
            CLASS_DEFAULT = NAMESPACE + '-default',
            CLASS_FOCUS = NAMESPACE + '-focus',
            CLASS_HOVER = NAMESPACE + '-hover',
            CLASS_DISABLED = NAMESPACE+'-disabled',

            replaceSuffix = '_replacedByqTip',
            oldtitle = 'oldtitle',
            trackingBound,

        // Browser detection
            BROWSER = {
                /*
                 * IE version detection
                 *
                 * Adapted from: http://ajaxian.com/archives/attack-of-the-ie-conditional-comment
                 * Credit to James Padolsey for the original implemntation!
                 */
                ie: (function(){
                    for (
                        var v = 4, i = document.createElement("div");
                        (i.innerHTML = "<!--[if gt IE " + v + "]><i></i><![endif]-->") && i.getElementsByTagName("i")[0];
                        v+=1
                    ) {}
                    return v > 4 ? v : NaN;
                }()),

                /*
                 * iOS version detection
                 */
                iOS: parseFloat(
                    ('' + (/CPU.*OS ([0-9_]{1,5})|(CPU like).*AppleWebKit.*Mobile/i.exec(navigator.userAgent) || [0,''])[1])
                        .replace('undefined', '3_2').replace('_', '.').replace('_', '')
                ) || FALSE
            };
        ;function QTip(target, options, id, attr) {
            // Elements and ID
            this.id = id;
            this.target = target;
            this.tooltip = NULL;
            this.elements = { target: target };

            // Internal constructs
            this._id = NAMESPACE + '-' + id;
            this.timers = { img: {} };
            this.options = options;
            this.plugins = {};

            // Cache object
            this.cache = {
                event: {},
                target: $(),
                disabled: FALSE,
                attr: attr,
                onTooltip: FALSE,
                lastClass: ''
            };

            // Set the initial flags
            this.rendered = this.destroyed = this.disabled = this.waiting =
                this.hiddenDuringWait = this.positioning = this.triggering = FALSE;
        }
        PROTOTYPE = QTip.prototype;

        PROTOTYPE._when = function(deferreds) {
            return $.when.apply($, deferreds);
        };

        PROTOTYPE.render = function(show) {
            if(this.rendered || this.destroyed) { return this; } // If tooltip has already been rendered, exit

            var self = this,
                options = this.options,
                cache = this.cache,
                elements = this.elements,
                text = options.content.text,
                title = options.content.title,
                button = options.content.button,
                posOptions = options.position,
                namespace = '.'+this._id+' ',
                deferreds = [],
                tooltip;

            // Add ARIA attributes to target
            $.attr(this.target[0], 'aria-describedby', this._id);

            // Create public position object that tracks current position corners
            cache.posClass = this._createPosClass(
                (this.position = { my: posOptions.my, at: posOptions.at }).my
            );

            // Create tooltip element
            this.tooltip = elements.tooltip = tooltip = $('<div/>', {
                'id': this._id,
                'class': [ NAMESPACE, CLASS_DEFAULT, options.style.classes, cache.posClass ].join(' '),
                'width': options.style.width || '',
                'height': options.style.height || '',
                'tracking': posOptions.target === 'mouse' && posOptions.adjust.mouse,

                /* ARIA specific attributes */
                'role': 'alert',
                'aria-live': 'polite',
                'aria-atomic': FALSE,
                'aria-describedby': this._id + '-content',
                'aria-hidden': TRUE
            })
                .toggleClass(CLASS_DISABLED, this.disabled)
                .attr(ATTR_ID, this.id)
                .data(NAMESPACE, this)
                .appendTo(posOptions.container)
                .append(
                // Create content element
                elements.content = $('<div />', {
                    'class': NAMESPACE + '-content',
                    'id': this._id + '-content',
                    'aria-atomic': TRUE
                })
            );

            // Set rendered flag and prevent redundant reposition calls for now
            this.rendered = -1;
            this.positioning = TRUE;

            // Create title...
            if(title) {
                this._createTitle();

                // Update title only if its not a callback (called in toggle if so)
                if(!$.isFunction(title)) {
                    deferreds.push( this._updateTitle(title, FALSE) );
                }
            }

            // Create button
            if(button) { this._createButton(); }

            // Set proper rendered flag and update content if not a callback function (called in toggle)
            if(!$.isFunction(text)) {
                deferreds.push( this._updateContent(text, FALSE) );
            }
            this.rendered = TRUE;

            // Setup widget classes
            this._setWidget();

            // Initialize 'render' plugins
            $.each(PLUGINS, function(name) {
                var instance;
                if(this.initialize === 'render' && (instance = this(self))) {
                    self.plugins[name] = instance;
                }
            });

            // Unassign initial events and assign proper events
            this._unassignEvents();
            this._assignEvents();

            // When deferreds have completed
            this._when(deferreds).then(function() {
                // tooltiprender event
                self._trigger('render');

                // Reset flags
                self.positioning = FALSE;

                // Show tooltip if not hidden during wait period
                if(!self.hiddenDuringWait && (options.show.ready || show)) {
                    self.toggle(TRUE, cache.event, FALSE);
                }
                self.hiddenDuringWait = FALSE;
            });

            // Expose API
            QTIP.api[this.id] = this;

            return this;
        };

        PROTOTYPE.destroy = function(immediate) {
            // Set flag the signify destroy is taking place to plugins
            // and ensure it only gets destroyed once!
            if(this.destroyed) { return this.target; }

            function process() {
                if(this.destroyed) { return; }
                this.destroyed = TRUE;

                var target = this.target,
                    title = target.attr(oldtitle),
                    timer;

                // Destroy tooltip if rendered
                if(this.rendered) {
                    this.tooltip.stop(1,0).find('*').remove().end().remove();
                }

                // Destroy all plugins
                $.each(this.plugins, function(name) {
                    this.destroy && this.destroy();
                });

                // Clear timers
                for(timer in this.timers) {
                    clearTimeout(this.timers[timer]);
                }

                // Remove api object and ARIA attributes
                target.removeData(NAMESPACE)
                    .removeAttr(ATTR_ID)
                    .removeAttr(ATTR_HAS)
                    .removeAttr('aria-describedby');

                // Reset old title attribute if removed
                if(this.options.suppress && title) {
                    target.attr('title', title).removeAttr(oldtitle);
                }

                // Remove qTip events associated with this API
                this._unassignEvents();

                // Remove ID from used id objects, and delete object references
                // for better garbage collection and leak protection
                this.options = this.elements = this.cache = this.timers =
                    this.plugins = this.mouse = NULL;

                // Delete epoxsed API object
                delete QTIP.api[this.id];
            }

            // If an immediate destory is needed
            if((immediate !== TRUE || this.triggering === 'hide') && this.rendered) {
                this.tooltip.one('tooltiphidden', $.proxy(process, this));
                !this.triggering && this.hide();
            }

            // If we're not in the process of hiding... process
            else { process.call(this); }

            return this.target;
        };
        ;function invalidOpt(a) {
            return a === NULL || $.type(a) !== 'object';
        }

        function invalidContent(c) {
            return !( $.isFunction(c) || (c && c.attr) || c.length || ($.type(c) === 'object' && (c.jquery || c.then) ));
        }

        // Option object sanitizer
        function sanitizeOptions(opts) {
            var content, text, ajax, once;

            if(invalidOpt(opts)) { return FALSE; }

            if(invalidOpt(opts.metadata)) {
                opts.metadata = { type: opts.metadata };
            }

            if('content' in opts) {
                content = opts.content;

                if(invalidOpt(content) || content.jquery || content.done) {
                    content = opts.content = {
                        text: (text = invalidContent(content) ? FALSE : content)
                    };
                }
                else { text = content.text; }

                // DEPRECATED - Old content.ajax plugin functionality
                // Converts it into the proper Deferred syntax
                if('ajax' in content) {
                    ajax = content.ajax;
                    once = ajax && ajax.once !== FALSE;
                    delete content.ajax;

                    content.text = function(event, api) {
                        var loading = text || $(this).attr(api.options.content.attr) || 'Loading...',

                            deferred = $.ajax(
                                $.extend({}, ajax, { context: api })
                            )
                                .then(ajax.success, NULL, ajax.error)
                                .then(function(content) {
                                    if(content && once) { api.set('content.text', content); }
                                    return content;
                                },
                                function(xhr, status, error) {
                                    if(api.destroyed || xhr.status === 0) { return; }
                                    api.set('content.text', status + ': ' + error);
                                });

                        return !once ? (api.set('content.text', loading), deferred) : loading;
                    };
                }

                if('title' in content) {
                    if($.isPlainObject(content.title)) {
                        content.button = content.title.button;
                        content.title = content.title.text;
                    }

                    if(invalidContent(content.title || FALSE)) {
                        content.title = FALSE;
                    }
                }
            }

            if('position' in opts && invalidOpt(opts.position)) {
                opts.position = { my: opts.position, at: opts.position };
            }

            if('show' in opts && invalidOpt(opts.show)) {
                opts.show = opts.show.jquery ? { target: opts.show } :
                    opts.show === TRUE ? { ready: TRUE } : { event: opts.show };
            }

            if('hide' in opts && invalidOpt(opts.hide)) {
                opts.hide = opts.hide.jquery ? { target: opts.hide } : { event: opts.hide };
            }

            if('style' in opts && invalidOpt(opts.style)) {
                opts.style = { classes: opts.style };
            }

            // Sanitize plugin options
            $.each(PLUGINS, function() {
                this.sanitize && this.sanitize(opts);
            });

            return opts;
        }

        // Setup builtin .set() option checks
        CHECKS = PROTOTYPE.checks = {
            builtin: {
                // Core checks
                '^id$': function(obj, o, v, prev) {
                    var id = v === TRUE ? QTIP.nextid : v,
                        new_id = NAMESPACE + '-' + id;

                    if(id !== FALSE && id.length > 0 && !$('#'+new_id).length) {
                        this._id = new_id;

                        if(this.rendered) {
                            this.tooltip[0].id = this._id;
                            this.elements.content[0].id = this._id + '-content';
                            this.elements.title[0].id = this._id + '-title';
                        }
                    }
                    else { obj[o] = prev; }
                },
                '^prerender': function(obj, o, v) {
                    v && !this.rendered && this.render(this.options.show.ready);
                },

                // Content checks
                '^content.text$': function(obj, o, v) {
                    this._updateContent(v);
                },
                '^content.attr$': function(obj, o, v, prev) {
                    if(this.options.content.text === this.target.attr(prev)) {
                        this._updateContent( this.target.attr(v) );
                    }
                },
                '^content.title$': function(obj, o, v) {
                    // Remove title if content is null
                    if(!v) { return this._removeTitle(); }

                    // If title isn't already created, create it now and update
                    v && !this.elements.title && this._createTitle();
                    this._updateTitle(v);
                },
                '^content.button$': function(obj, o, v) {
                    this._updateButton(v);
                },
                '^content.title.(text|button)$': function(obj, o, v) {
                    this.set('content.'+o, v); // Backwards title.text/button compat
                },

                // Position checks
                '^position.(my|at)$': function(obj, o, v){
                    'string' === typeof v && (this.position[o] = obj[o] = new CORNER(v, o === 'at'));
                },
                '^position.container$': function(obj, o, v){
                    this.rendered && this.tooltip.appendTo(v);
                },

                // Show checks
                '^show.ready$': function(obj, o, v) {
                    v && (!this.rendered && this.render(TRUE) || this.toggle(TRUE));
                },

                // Style checks
                '^style.classes$': function(obj, o, v, p) {
                    this.rendered && this.tooltip.removeClass(p).addClass(v);
                },
                '^style.(width|height)': function(obj, o, v) {
                    this.rendered && this.tooltip.css(o, v);
                },
                '^style.widget|content.title': function() {
                    this.rendered && this._setWidget();
                },
                '^style.def': function(obj, o, v) {
                    this.rendered && this.tooltip.toggleClass(CLASS_DEFAULT, !!v);
                },

                // Events check
                '^events.(render|show|move|hide|focus|blur)$': function(obj, o, v) {
                    this.rendered && this.tooltip[($.isFunction(v) ? '' : 'un') + 'bind']('tooltip'+o, v);
                },

                // Properties which require event reassignment
                '^(show|hide|position).(event|target|fixed|inactive|leave|distance|viewport|adjust)': function() {
                    if(!this.rendered) { return; }

                    // Set tracking flag
                    var posOptions = this.options.position;
                    this.tooltip.attr('tracking', posOptions.target === 'mouse' && posOptions.adjust.mouse);

                    // Reassign events
                    this._unassignEvents();
                    this._assignEvents();
                }
            }
        };

        // Dot notation converter
        function convertNotation(options, notation) {
            var i = 0, obj, option = options,

            // Split notation into array
                levels = notation.split('.');

            // Loop through
            while( option = option[ levels[i++] ] ) {
                if(i < levels.length) { obj = option; }
            }

            return [obj || options, levels.pop()];
        }

        PROTOTYPE.get = function(notation) {
            if(this.destroyed) { return this; }

            var o = convertNotation(this.options, notation.toLowerCase()),
                result = o[0][ o[1] ];

            return result.precedance ? result.string() : result;
        };

        function setCallback(notation, args) {
            var category, rule, match;

            for(category in this.checks) {
                for(rule in this.checks[category]) {
                    if(match = (new RegExp(rule, 'i')).exec(notation)) {
                        args.push(match);

                        if(category === 'builtin' || this.plugins[category]) {
                            this.checks[category][rule].apply(
                                this.plugins[category] || this, args
                            );
                        }
                    }
                }
            }
        }

        var rmove = /^position\.(my|at|adjust|target|container|viewport)|style|content|show\.ready/i,
            rrender = /^prerender|show\.ready/i;

        PROTOTYPE.set = function(option, value) {
            if(this.destroyed) { return this; }

            var rendered = this.rendered,
                reposition = FALSE,
                options = this.options,
                checks = this.checks,
                name;

            // Convert singular option/value pair into object form
            if('string' === typeof option) {
                name = option; option = {}; option[name] = value;
            }
            else { option = $.extend({}, option); }

            // Set all of the defined options to their new values
            $.each(option, function(notation, value) {
                if(rendered && rrender.test(notation)) {
                    delete option[notation]; return;
                }

                // Set new obj value
                var obj = convertNotation(options, notation.toLowerCase()), previous;
                previous = obj[0][ obj[1] ];
                obj[0][ obj[1] ] = value && value.nodeType ? $(value) : value;

                // Also check if we need to reposition
                reposition = rmove.test(notation) || reposition;

                // Set the new params for the callback
                option[notation] = [obj[0], obj[1], value, previous];
            });

            // Re-sanitize options
            sanitizeOptions(options);

            /*
             * Execute any valid callbacks for the set options
             * Also set positioning flag so we don't get loads of redundant repositioning calls.
             */
            this.positioning = TRUE;
            $.each(option, $.proxy(setCallback, this));
            this.positioning = FALSE;

            // Update position if needed
            if(this.rendered && this.tooltip[0].offsetWidth > 0 && reposition) {
                this.reposition( options.position.target === 'mouse' ? NULL : this.cache.event );
            }

            return this;
        };
        ;PROTOTYPE._update = function(content, element, reposition) {
            var self = this,
                cache = this.cache;

            // Make sure tooltip is rendered and content is defined. If not return
            if(!this.rendered || !content) { return FALSE; }

            // Use function to parse content
            if($.isFunction(content)) {
                content = content.call(this.elements.target, cache.event, this) || '';
            }

            // Handle deferred content
            if($.isFunction(content.then)) {
                cache.waiting = TRUE;
                return content.then(function(c) {
                    cache.waiting = FALSE;
                    return self._update(c, element);
                }, NULL, function(e) {
                    return self._update(e, element);
                });
            }

            // If content is null... return false
            if(content === FALSE || (!content && content !== '')) { return FALSE; }

            // Append new content if its a DOM array and show it if hidden
            if(content.jquery && content.length > 0) {
                element.empty().append(
                    content.css({ display: 'block', visibility: 'visible' })
                );
            }

            // Content is a regular string, insert the new content
            else { element.html(content); }

            // Wait for content to be loaded, and reposition
            return this._waitForContent(element).then(function(images) {
                if(self.rendered && self.tooltip[0].offsetWidth > 0) {
                    self.reposition(cache.event, !images.length);
                }
            });
        };

        PROTOTYPE._waitForContent = function(element) {
            var cache = this.cache;

            // Set flag
            cache.waiting = TRUE;

            // If imagesLoaded is included, ensure images have loaded and return promise
            return ( $.fn.imagesLoaded ? element.imagesLoaded() : $.Deferred().resolve([]) )
                .done(function() { cache.waiting = FALSE; })
                .promise();
        };

        PROTOTYPE._updateContent = function(content, reposition) {
            this._update(content, this.elements.content, reposition);
        };

        PROTOTYPE._updateTitle = function(content, reposition) {
            if(this._update(content, this.elements.title, reposition) === FALSE) {
                this._removeTitle(FALSE);
            }
        };

        PROTOTYPE._createTitle = function()
        {
            var elements = this.elements,
                id = this._id+'-title';

            // Destroy previous title element, if present
            if(elements.titlebar) { this._removeTitle(); }

            // Create title bar and title elements
            elements.titlebar = $('<div />', {
                'class': NAMESPACE + '-titlebar ' + (this.options.style.widget ? createWidgetClass('header') : '')
            })
                .append(
                elements.title = $('<div />', {
                    'id': id,
                    'class': NAMESPACE + '-title',
                    'aria-atomic': TRUE
                })
            )
                .insertBefore(elements.content)

                // Button-specific events
                .delegate('.qtip-close', 'mousedown keydown mouseup keyup mouseout', function(event) {
                    $(this).toggleClass('ui-state-active ui-state-focus', event.type.substr(-4) === 'down');
                })
                .delegate('.qtip-close', 'mouseover mouseout', function(event){
                    $(this).toggleClass('ui-state-hover', event.type === 'mouseover');
                });

            // Create button if enabled
            if(this.options.content.button) { this._createButton(); }
        };

        PROTOTYPE._removeTitle = function(reposition)
        {
            var elements = this.elements;

            if(elements.title) {
                elements.titlebar.remove();
                elements.titlebar = elements.title = elements.button = NULL;

                // Reposition if enabled
                if(reposition !== FALSE) { this.reposition(); }
            }
        };
        ;PROTOTYPE._createPosClass = function(my) {
            return NAMESPACE + '-pos-' + (my || this.options.position.my).abbrev();
        };

        PROTOTYPE.reposition = function(event, effect) {
            if(!this.rendered || this.positioning || this.destroyed) { return this; }

            // Set positioning flag
            this.positioning = TRUE;

            var cache = this.cache,
                tooltip = this.tooltip,
                posOptions = this.options.position,
                target = posOptions.target,
                my = posOptions.my,
                at = posOptions.at,
                viewport = posOptions.viewport,
                container = posOptions.container,
                adjust = posOptions.adjust,
                method = adjust.method.split(' '),
                tooltipWidth = tooltip.outerWidth(FALSE),
                tooltipHeight = tooltip.outerHeight(FALSE),
                targetWidth = 0,
                targetHeight = 0,
                type = tooltip.css('position'),
                position = { left: 0, top: 0 },
                visible = tooltip[0].offsetWidth > 0,
                isScroll = event && event.type === 'scroll',
                win = $(window),
                doc = container[0].ownerDocument,
                mouse = this.mouse,
                pluginCalculations, offset, adjusted, newClass;

            // Check if absolute position was passed
            if($.isArray(target) && target.length === 2) {
                // Force left top and set position
                at = { x: LEFT, y: TOP };
                position = { left: target[0], top: target[1] };
            }

            // Check if mouse was the target
            else if(target === 'mouse') {
                // Force left top to allow flipping
                at = { x: LEFT, y: TOP };

                // Use the mouse origin that caused the show event, if distance hiding is enabled
                if((!adjust.mouse || this.options.hide.distance) && cache.origin && cache.origin.pageX) {
                    event =  cache.origin;
                }

                // Use cached event for resize/scroll events
                else if(!event || (event && (event.type === 'resize' || event.type === 'scroll'))) {
                    event = cache.event;
                }

                // Otherwise, use the cached mouse coordinates if available
                else if(mouse && mouse.pageX) {
                    event = mouse;
                }

                // Calculate body and container offset and take them into account below
                if(type !== 'static') { position = container.offset(); }
                if(doc.body.offsetWidth !== (window.innerWidth || doc.documentElement.clientWidth)) {
                    offset = $(document.body).offset();
                }

                // Use event coordinates for position
                position = {
                    left: event.pageX - position.left + (offset && offset.left || 0),
                    top: event.pageY - position.top + (offset && offset.top || 0)
                };

                // Scroll events are a pain, some browsers
                if(adjust.mouse && isScroll && mouse) {
                    position.left -= (mouse.scrollX || 0) - win.scrollLeft();
                    position.top -= (mouse.scrollY || 0) - win.scrollTop();
                }
            }

            // Target wasn't mouse or absolute...
            else {
                // Check if event targetting is being used
                if(target === 'event') {
                    if(event && event.target && event.type !== 'scroll' && event.type !== 'resize') {
                        cache.target = $(event.target);
                    }
                    else if(!event.target) {
                        cache.target = this.elements.target;
                    }
                }
                else if(target !== 'event'){
                    cache.target = $(target.jquery ? target : this.elements.target);
                }
                target = cache.target;

                // Parse the target into a jQuery object and make sure there's an element present
                target = $(target).eq(0);
                if(target.length === 0) { return this; }

                // Check if window or document is the target
                else if(target[0] === document || target[0] === window) {
                    targetWidth = BROWSER.iOS ? window.innerWidth : target.width();
                    targetHeight = BROWSER.iOS ? window.innerHeight : target.height();

                    if(target[0] === window) {
                        position = {
                            top: (viewport || target).scrollTop(),
                            left: (viewport || target).scrollLeft()
                        };
                    }
                }

                // Check if the target is an <AREA> element
                else if(PLUGINS.imagemap && target.is('area')) {
                    pluginCalculations = PLUGINS.imagemap(this, target, at, PLUGINS.viewport ? method : FALSE);
                }

                // Check if the target is an SVG element
                else if(PLUGINS.svg && target && target[0].ownerSVGElement) {
                    pluginCalculations = PLUGINS.svg(this, target, at, PLUGINS.viewport ? method : FALSE);
                }

                // Otherwise use regular jQuery methods
                else {
                    targetWidth = target.outerWidth(FALSE);
                    targetHeight = target.outerHeight(FALSE);
                    position = target.offset();
                }

                // Parse returned plugin values into proper variables
                if(pluginCalculations) {
                    targetWidth = pluginCalculations.width;
                    targetHeight = pluginCalculations.height;
                    offset = pluginCalculations.offset;
                    position = pluginCalculations.position;
                }

                // Adjust position to take into account offset parents
                position = this.reposition.offset(target, position, container);

                // Adjust for position.fixed tooltips (and also iOS scroll bug in v3.2-4.0 & v4.3-4.3.2)
                if((BROWSER.iOS > 3.1 && BROWSER.iOS < 4.1) ||
                    (BROWSER.iOS >= 4.3 && BROWSER.iOS < 4.33) ||
                    (!BROWSER.iOS && type === 'fixed')
                ){
                    position.left -= win.scrollLeft();
                    position.top -= win.scrollTop();
                }

                // Adjust position relative to target
                if(!pluginCalculations || (pluginCalculations && pluginCalculations.adjustable !== FALSE)) {
                    position.left += at.x === RIGHT ? targetWidth : at.x === CENTER ? targetWidth / 2 : 0;
                    position.top += at.y === BOTTOM ? targetHeight : at.y === CENTER ? targetHeight / 2 : 0;
                }
            }

            // Adjust position relative to tooltip
            position.left += adjust.x + (my.x === RIGHT ? -tooltipWidth : my.x === CENTER ? -tooltipWidth / 2 : 0);
            position.top += adjust.y + (my.y === BOTTOM ? -tooltipHeight : my.y === CENTER ? -tooltipHeight / 2 : 0);

            // Use viewport adjustment plugin if enabled
            if(PLUGINS.viewport) {
                adjusted = position.adjusted = PLUGINS.viewport(
                    this, position, posOptions, targetWidth, targetHeight, tooltipWidth, tooltipHeight
                );

                // Apply offsets supplied by positioning plugin (if used)
                if(offset && adjusted.left) { position.left += offset.left; }
                if(offset && adjusted.top) {  position.top += offset.top; }

                // Apply any new 'my' position
                if(adjusted.my) { this.position.my = adjusted.my; }
            }

            // Viewport adjustment is disabled, set values to zero
            else { position.adjusted = { left: 0, top: 0 }; }

            // Set tooltip position class if it's changed
            if(cache.posClass !== (newClass = this._createPosClass(this.position.my))) {
                tooltip.removeClass(cache.posClass).addClass( (cache.posClass = newClass) );
            }

            // tooltipmove event
            if(!this._trigger('move', [position, viewport.elem || viewport], event)) { return this; }
            delete position.adjusted;

            // If effect is disabled, target it mouse, no animation is defined or positioning gives NaN out, set CSS directly
            if(effect === FALSE || !visible || isNaN(position.left) || isNaN(position.top) || target === 'mouse' || !$.isFunction(posOptions.effect)) {
                tooltip.css(position);
            }

            // Use custom function if provided
            else if($.isFunction(posOptions.effect)) {
                posOptions.effect.call(tooltip, this, $.extend({}, position));
                tooltip.queue(function(next) {
                    // Reset attributes to avoid cross-browser rendering bugs
                    $(this).css({ opacity: '', height: '' });
                    if(BROWSER.ie) { this.style.removeAttribute('filter'); }

                    next();
                });
            }

            // Set positioning flag
            this.positioning = FALSE;

            return this;
        };

        // Custom (more correct for qTip!) offset calculator
        PROTOTYPE.reposition.offset = function(elem, pos, container) {
            if(!container[0]) { return pos; }

            var ownerDocument = $(elem[0].ownerDocument),
                quirks = !!BROWSER.ie && document.compatMode !== 'CSS1Compat',
                parent = container[0],
                scrolled, position, parentOffset, overflow;

            function scroll(e, i) {
                pos.left += i * e.scrollLeft();
                pos.top += i * e.scrollTop();
            }

            // Compensate for non-static containers offset
            do {
                if((position = $.css(parent, 'position')) !== 'static') {
                    if(position === 'fixed') {
                        parentOffset = parent.getBoundingClientRect();
                        scroll(ownerDocument, -1);
                    }
                    else {
                        parentOffset = $(parent).position();
                        parentOffset.left += (parseFloat($.css(parent, 'borderLeftWidth')) || 0);
                        parentOffset.top += (parseFloat($.css(parent, 'borderTopWidth')) || 0);
                    }

                    pos.left -= parentOffset.left + (parseFloat($.css(parent, 'marginLeft')) || 0);
                    pos.top -= parentOffset.top + (parseFloat($.css(parent, 'marginTop')) || 0);

                    // If this is the first parent element with an overflow of "scroll" or "auto", store it
                    if(!scrolled && (overflow = $.css(parent, 'overflow')) !== 'hidden' && overflow !== 'visible') { scrolled = $(parent); }
                }
            }
            while((parent = parent.offsetParent));

            // Compensate for containers scroll if it also has an offsetParent (or in IE quirks mode)
            if(scrolled && (scrolled[0] !== ownerDocument[0] || quirks)) {
                scroll(scrolled, 1);
            }

            return pos;
        };

        // Corner class
        var C = (CORNER = PROTOTYPE.reposition.Corner = function(corner, forceY) {
            corner = ('' + corner).replace(/([A-Z])/, ' $1').replace(/middle/gi, CENTER).toLowerCase();
            this.x = (corner.match(/left|right/i) || corner.match(/center/) || ['inherit'])[0].toLowerCase();
            this.y = (corner.match(/top|bottom|center/i) || ['inherit'])[0].toLowerCase();
            this.forceY = !!forceY;

            var f = corner.charAt(0);
            this.precedance = (f === 't' || f === 'b' ? Y : X);
        }).prototype;

        C.invert = function(z, center) {
            this[z] = this[z] === LEFT ? RIGHT : this[z] === RIGHT ? LEFT : center || this[z];
        };

        C.string = function(join) {
            var x = this.x, y = this.y;

            var result = x !== y ?
                (x === 'center' || y !== 'center' && (this.precedance === Y || this.forceY) ?
                        [y,x] : [x,y]
                ) :
                [x];

            return join !== false ? result.join(' ') : result;
        };

        C.abbrev = function() {
            var result = this.string(false);
            return result[0].charAt(0) + (result[1] && result[1].charAt(0) || '');
        };

        C.clone = function() {
            return new CORNER( this.string(), this.forceY );
        };

        ;
        PROTOTYPE.toggle = function(state, event) {
            var cache = this.cache,
                options = this.options,
                tooltip = this.tooltip;

            // Try to prevent flickering when tooltip overlaps show element
            if(event) {
                if((/over|enter/).test(event.type) && cache.event && (/out|leave/).test(cache.event.type) &&
                    options.show.target.add(event.target).length === options.show.target.length &&
                    tooltip.has(event.relatedTarget).length) {
                    return this;
                }

                // Cache event
                cache.event = $.event.fix(event);
            }

            // If we're currently waiting and we've just hidden... stop it
            this.waiting && !state && (this.hiddenDuringWait = TRUE);

            // Render the tooltip if showing and it isn't already
            if(!this.rendered) { return state ? this.render(1) : this; }
            else if(this.destroyed || this.disabled) { return this; }

            var type = state ? 'show' : 'hide',
                opts = this.options[type],
                otherOpts = this.options[ !state ? 'show' : 'hide' ],
                posOptions = this.options.position,
                contentOptions = this.options.content,
                width = this.tooltip.css('width'),
                visible = this.tooltip.is(':visible'),
                animate = state || opts.target.length === 1,
                sameTarget = !event || opts.target.length < 2 || cache.target[0] === event.target,
                identicalState, allow, showEvent, delay, after;

            // Detect state if valid one isn't provided
            if((typeof state).search('boolean|number')) { state = !visible; }

            // Check if the tooltip is in an identical state to the new would-be state
            identicalState = !tooltip.is(':animated') && visible === state && sameTarget;

            // Fire tooltip(show/hide) event and check if destroyed
            allow = !identicalState ? !!this._trigger(type, [90]) : NULL;

            // Check to make sure the tooltip wasn't destroyed in the callback
            if(this.destroyed) { return this; }

            // If the user didn't stop the method prematurely and we're showing the tooltip, focus it
            if(allow !== FALSE && state) { this.focus(event); }

            // If the state hasn't changed or the user stopped it, return early
            if(!allow || identicalState) { return this; }

            // Set ARIA hidden attribute
            $.attr(tooltip[0], 'aria-hidden', !!!state);

            // Execute state specific properties
            if(state) {
                // Store show origin coordinates
                this.mouse && (cache.origin = $.event.fix(this.mouse));

                // Update tooltip content & title if it's a dynamic function
                if($.isFunction(contentOptions.text)) { this._updateContent(contentOptions.text, FALSE); }
                if($.isFunction(contentOptions.title)) { this._updateTitle(contentOptions.title, FALSE); }

                // Cache mousemove events for positioning purposes (if not already tracking)
                if(!trackingBound && posOptions.target === 'mouse' && posOptions.adjust.mouse) {
                    $(document).bind('mousemove.'+NAMESPACE, this._storeMouse);
                    trackingBound = TRUE;
                }

                // Update the tooltip position (set width first to prevent viewport/max-width issues)
                if(!width) { tooltip.css('width', tooltip.outerWidth(FALSE)); }
                this.reposition(event, arguments[2]);
                if(!width) { tooltip.css('width', ''); }

                // Hide other tooltips if tooltip is solo
                if(!!opts.solo) {
                    (typeof opts.solo === 'string' ? $(opts.solo) : $(SELECTOR, opts.solo))
                        .not(tooltip).not(opts.target).qtip('hide', $.Event('tooltipsolo'));
                }
            }
            else {
                // Clear show timer if we're hiding
                clearTimeout(this.timers.show);

                // Remove cached origin on hide
                delete cache.origin;

                // Remove mouse tracking event if not needed (all tracking qTips are hidden)
                if(trackingBound && !$(SELECTOR+'[tracking="true"]:visible', opts.solo).not(tooltip).length) {
                    $(document).unbind('mousemove.'+NAMESPACE);
                    trackingBound = FALSE;
                }

                // Blur the tooltip
                this.blur(event);
            }

            // Define post-animation, state specific properties
            after = $.proxy(function() {
                if(state) {
                    // Prevent antialias from disappearing in IE by removing filter
                    if(BROWSER.ie) { tooltip[0].style.removeAttribute('filter'); }

                    // Remove overflow setting to prevent tip bugs
                    tooltip.css('overflow', '');

                    // Autofocus elements if enabled
                    if('string' === typeof opts.autofocus) {
                        $(this.options.show.autofocus, tooltip).focus();
                    }

                    // If set, hide tooltip when inactive for delay period
                    this.options.show.target.trigger('qtip-'+this.id+'-inactive');
                }
                else {
                    // Reset CSS states
                    tooltip.css({
                        display: '',
                        visibility: '',
                        opacity: '',
                        left: '',
                        top: ''
                    });
                }

                // tooltipvisible/tooltiphidden events
                this._trigger(state ? 'visible' : 'hidden');
            }, this);

            // If no effect type is supplied, use a simple toggle
            if(opts.effect === FALSE || animate === FALSE) {
                tooltip[ type ]();
                after();
            }

            // Use custom function if provided
            else if($.isFunction(opts.effect)) {
                tooltip.stop(1, 1);
                opts.effect.call(tooltip, this);
                tooltip.queue('fx', function(n) {
                    after(); n();
                });
            }

            // Use basic fade function by default
            else { tooltip.fadeTo(90, state ? 1 : 0, after); }

            // If inactive hide method is set, active it
            if(state) { opts.target.trigger('qtip-'+this.id+'-inactive'); }

            return this;
        };

        PROTOTYPE.show = function(event) { return this.toggle(TRUE, event); };

        PROTOTYPE.hide = function(event) { return this.toggle(FALSE, event); };
        ;PROTOTYPE.focus = function(event) {
            if(!this.rendered || this.destroyed) { return this; }

            var qtips = $(SELECTOR),
                tooltip = this.tooltip,
                curIndex = parseInt(tooltip[0].style.zIndex, 10),
                newIndex = QTIP.zindex + qtips.length,
                focusedElem;

            // Only update the z-index if it has changed and tooltip is not already focused
            if(!tooltip.hasClass(CLASS_FOCUS)) {
                // tooltipfocus event
                if(this._trigger('focus', [newIndex], event)) {
                    // Only update z-index's if they've changed
                    if(curIndex !== newIndex) {
                        // Reduce our z-index's and keep them properly ordered
                        qtips.each(function() {
                            if(this.style.zIndex > curIndex) {
                                this.style.zIndex = this.style.zIndex - 1;
                            }
                        });

                        // Fire blur event for focused tooltip
                        qtips.filter('.' + CLASS_FOCUS).qtip('blur', event);
                    }

                    // Set the new z-index
                    tooltip.addClass(CLASS_FOCUS)[0].style.zIndex = newIndex;
                }
            }

            return this;
        };

        PROTOTYPE.blur = function(event) {
            if(!this.rendered || this.destroyed) { return this; }

            // Set focused status to FALSE
            this.tooltip.removeClass(CLASS_FOCUS);

            // tooltipblur event
            this._trigger('blur', [ this.tooltip.css('zIndex') ], event);

            return this;
        };
        ;PROTOTYPE.disable = function(state) {
            if(this.destroyed) { return this; }

            // If 'toggle' is passed, toggle the current state
            if(state === 'toggle') {
                state = !(this.rendered ? this.tooltip.hasClass(CLASS_DISABLED) : this.disabled);
            }

            // Disable if no state passed
            else if('boolean' !== typeof state) {
                state = TRUE;
            }

            if(this.rendered) {
                this.tooltip.toggleClass(CLASS_DISABLED, state)
                    .attr('aria-disabled', state);
            }

            this.disabled = !!state;

            return this;
        };

        PROTOTYPE.enable = function() { return this.disable(FALSE); };
        ;PROTOTYPE._createButton = function()
        {
            var self = this,
                elements = this.elements,
                tooltip = elements.tooltip,
                button = this.options.content.button,
                isString = typeof button === 'string',
                close = isString ? button : 'Close tooltip';

            if(elements.button) { elements.button.remove(); }

            // Use custom button if one was supplied by user, else use default
            if(button.jquery) {
                elements.button = button;
            }
            else {
                elements.button = $('<a />', {
                    'class': 'qtip-close ' + (this.options.style.widget ? '' : NAMESPACE+'-icon'),
                    'title': close,
                    'aria-label': close
                })
                    .prepend(
                    $('<span />', {
                        'class': 'ui-icon ui-icon-close',
                        'html': '&times;'
                    })
                );
            }

            // Create button and setup attributes
            elements.button.appendTo(elements.titlebar || tooltip)
                .attr('role', 'button')
                .click(function(event) {
                    if(!tooltip.hasClass(CLASS_DISABLED)) { self.hide(event); }
                    return FALSE;
                });
        };

        PROTOTYPE._updateButton = function(button)
        {
            // Make sure tooltip is rendered and if not, return
            if(!this.rendered) { return FALSE; }

            var elem = this.elements.button;
            if(button) { this._createButton(); }
            else { elem.remove(); }
        };
        ;// Widget class creator
        function createWidgetClass(cls) {
            return WIDGET.concat('').join(cls ? '-'+cls+' ' : ' ');
        }

        // Widget class setter method
        PROTOTYPE._setWidget = function()
        {
            var on = this.options.style.widget,
                elements = this.elements,
                tooltip = elements.tooltip,
                disabled = tooltip.hasClass(CLASS_DISABLED);

            tooltip.removeClass(CLASS_DISABLED);
            CLASS_DISABLED = on ? 'ui-state-disabled' : 'qtip-disabled';
            tooltip.toggleClass(CLASS_DISABLED, disabled);

            tooltip.toggleClass('ui-helper-reset '+createWidgetClass(), on).toggleClass(CLASS_DEFAULT, this.options.style.def && !on);

            if(elements.content) {
                elements.content.toggleClass( createWidgetClass('content'), on);
            }
            if(elements.titlebar) {
                elements.titlebar.toggleClass( createWidgetClass('header'), on);
            }
            if(elements.button) {
                elements.button.toggleClass(NAMESPACE+'-icon', !on);
            }
        };
        ;function delay(callback, duration) {
            // If tooltip has displayed, start hide timer
            if(duration > 0) {
                return setTimeout(
                    $.proxy(callback, this), duration
                );
            }
            else{ callback.call(this); }
        }

        function showMethod(event) {
            if(this.tooltip.hasClass(CLASS_DISABLED)) { return; }

            // Clear hide timers
            clearTimeout(this.timers.show);
            clearTimeout(this.timers.hide);

            // Start show timer
            this.timers.show = delay.call(this,
                function() { this.toggle(TRUE, event); },
                this.options.show.delay
            );
        }

        function hideMethod(event) {
            if(this.tooltip.hasClass(CLASS_DISABLED) || this.destroyed) { return; }

            // Check if new target was actually the tooltip element
            var relatedTarget = $(event.relatedTarget),
                ontoTooltip = relatedTarget.closest(SELECTOR)[0] === this.tooltip[0],
                ontoTarget = relatedTarget[0] === this.options.show.target[0];

            // Clear timers and stop animation queue
            clearTimeout(this.timers.show);
            clearTimeout(this.timers.hide);

            // Prevent hiding if tooltip is fixed and event target is the tooltip.
            // Or if mouse positioning is enabled and cursor momentarily overlaps
            if(this !== relatedTarget[0] &&
                (this.options.position.target === 'mouse' && ontoTooltip) ||
                (this.options.hide.fixed && (
                    (/mouse(out|leave|move)/).test(event.type) && (ontoTooltip || ontoTarget))
                ))
            {
                try {
                    event.preventDefault();
                    event.stopImmediatePropagation();
                } catch(e) {}

                return;
            }

            // If tooltip has displayed, start hide timer
            this.timers.hide = delay.call(this,
                function() { this.toggle(FALSE, event); },
                this.options.hide.delay,
                this
            );
        }

        function inactiveMethod(event) {
            if(this.tooltip.hasClass(CLASS_DISABLED) || !this.options.hide.inactive) { return; }

            // Clear timer
            clearTimeout(this.timers.inactive);

            this.timers.inactive = delay.call(this,
                function(){ this.hide(event); },
                this.options.hide.inactive
            );
        }

        function repositionMethod(event) {
            if(this.rendered && this.tooltip[0].offsetWidth > 0) { this.reposition(event); }
        }

        // Store mouse coordinates
        PROTOTYPE._storeMouse = function(event) {
            (this.mouse = $.event.fix(event)).type = 'mousemove';
            return this;
        };

        // Bind events
        PROTOTYPE._bind = function(targets, events, method, suffix, context) {
            if(!targets || !method || !events.length) { return; }
            var ns = '.' + this._id + (suffix ? '-'+suffix : '');
            $(targets).bind(
                (events.split ? events : events.join(ns + ' ')) + ns,
                $.proxy(method, context || this)
            );
            return this;
        };
        PROTOTYPE._unbind = function(targets, suffix) {
            targets && $(targets).unbind('.' + this._id + (suffix ? '-'+suffix : ''));
            return this;
        };

        // Global delegation helper
        function delegate(selector, events, method) {
            $(document.body).delegate(selector,
                (events.split ? events : events.join('.'+NAMESPACE + ' ')) + '.'+NAMESPACE,
                function() {
                    var api = QTIP.api[ $.attr(this, ATTR_ID) ];
                    api && !api.disabled && method.apply(api, arguments);
                }
            );
        }
        // Event trigger
        PROTOTYPE._trigger = function(type, args, event) {
            var callback = $.Event('tooltip'+type);
            callback.originalEvent = (event && $.extend({}, event)) || this.cache.event || NULL;

            this.triggering = type;
            this.tooltip.trigger(callback, [this].concat(args || []));
            this.triggering = FALSE;

            return !callback.isDefaultPrevented();
        };

        PROTOTYPE._bindEvents = function(showEvents, hideEvents, showTargets, hideTargets, showMethod, hideMethod) {
            // Get tasrgets that lye within both
            var similarTargets = showTargets.filter( hideTargets ).add( hideTargets.filter(showTargets) ),
                toggleEvents = [];

            // If hide and show targets are the same...
            if(similarTargets.length) {

                // Filter identical show/hide events
                $.each(hideEvents, function(i, type) {
                    var showIndex = $.inArray(type, showEvents);

                    // Both events are identical, remove from both hide and show events
                    // and append to toggleEvents
                    showIndex > -1 && toggleEvents.push( showEvents.splice( showIndex, 1 )[0] );
                });

                // Toggle events are special case of identical show/hide events, which happen in sequence
                if(toggleEvents.length) {
                    // Bind toggle events to the similar targets
                    this._bind(similarTargets, toggleEvents, function(event) {
                        var state = this.rendered ? this.tooltip[0].offsetWidth > 0 : false;
                        (state ? hideMethod : showMethod).call(this, event);
                    });

                    // Remove the similar targets from the regular show/hide bindings
                    showTargets = showTargets.not(similarTargets);
                    hideTargets = hideTargets.not(similarTargets);
                }
            }

            // Apply show/hide/toggle events
            this._bind(showTargets, showEvents, showMethod);
            this._bind(hideTargets, hideEvents, hideMethod);
        };

        PROTOTYPE._assignInitialEvents = function(event) {
            var options = this.options,
                showTarget = options.show.target,
                hideTarget = options.hide.target,
                showEvents = options.show.event ? $.trim('' + options.show.event).split(' ') : [],
                hideEvents = options.hide.event ? $.trim('' + options.hide.event).split(' ') : [];

            // Catch remove/removeqtip events on target element to destroy redundant tooltips
            this._bind(this.elements.target, ['remove', 'removeqtip'], function(event) {
                this.destroy(true);
            }, 'destroy');

            /*
             * Make sure hoverIntent functions properly by using mouseleave as a hide event if
             * mouseenter/mouseout is used for show.event, even if it isn't in the users options.
             */
            if(/mouse(over|enter)/i.test(options.show.event) && !/mouse(out|leave)/i.test(options.hide.event)) {
                hideEvents.push('mouseleave');
            }

            /*
             * Also make sure initial mouse targetting works correctly by caching mousemove coords
             * on show targets before the tooltip has rendered. Also set onTarget when triggered to
             * keep mouse tracking working.
             */
            this._bind(showTarget, 'mousemove', function(event) {
                this._storeMouse(event);
                this.cache.onTarget = TRUE;
            });

            // Define hoverIntent function
            function hoverIntent(event) {
                // Only continue if tooltip isn't disabled
                if(this.disabled || this.destroyed) { return FALSE; }

                // Cache the event data
                this.cache.event = event && $.event.fix(event);
                this.cache.target = event && $(event.target);

                // Start the event sequence
                clearTimeout(this.timers.show);
                this.timers.show = delay.call(this,
                    function() { this.render(typeof event === 'object' || options.show.ready); },
                    options.prerender ? 0 : options.show.delay
                );
            }

            // Filter and bind events
            this._bindEvents(showEvents, hideEvents, showTarget, hideTarget, hoverIntent, function() {
                if(!this.timers) { return FALSE; }
                clearTimeout(this.timers.show);
            });

            // Prerendering is enabled, create tooltip now
            if(options.show.ready || options.prerender) { hoverIntent.call(this, event); }
        };

        // Event assignment method
        PROTOTYPE._assignEvents = function() {
            var self = this,
                options = this.options,
                posOptions = options.position,

                tooltip = this.tooltip,
                showTarget = options.show.target,
                hideTarget = options.hide.target,
                containerTarget = posOptions.container,
                viewportTarget = posOptions.viewport,
                documentTarget = $(document),
                bodyTarget = $(document.body),
                windowTarget = $(window),

                showEvents = options.show.event ? $.trim('' + options.show.event).split(' ') : [],
                hideEvents = options.hide.event ? $.trim('' + options.hide.event).split(' ') : [];


            // Assign passed event callbacks
            $.each(options.events, function(name, callback) {
                self._bind(tooltip, name === 'toggle' ? ['tooltipshow','tooltiphide'] : ['tooltip'+name], callback, null, tooltip);
            });

            // Hide tooltips when leaving current window/frame (but not select/option elements)
            if(/mouse(out|leave)/i.test(options.hide.event) && options.hide.leave === 'window') {
                this._bind(documentTarget, ['mouseout', 'blur'], function(event) {
                    if(!/select|option/.test(event.target.nodeName) && !event.relatedTarget) {
                        this.hide(event);
                    }
                });
            }

            // Enable hide.fixed by adding appropriate class
            if(options.hide.fixed) {
                hideTarget = hideTarget.add( tooltip.addClass(CLASS_FIXED) );
            }

            /*
             * Make sure hoverIntent functions properly by using mouseleave to clear show timer if
             * mouseenter/mouseout is used for show.event, even if it isn't in the users options.
             */
            else if(/mouse(over|enter)/i.test(options.show.event)) {
                this._bind(hideTarget, 'mouseleave', function() {
                    clearTimeout(this.timers.show);
                });
            }

            // Hide tooltip on document mousedown if unfocus events are enabled
            if(('' + options.hide.event).indexOf('unfocus') > -1) {
                this._bind(containerTarget.closest('html'), ['mousedown', 'touchstart'], function(event) {
                    var elem = $(event.target),
                        enabled = this.rendered && !this.tooltip.hasClass(CLASS_DISABLED) && this.tooltip[0].offsetWidth > 0,
                        isAncestor = elem.parents(SELECTOR).filter(this.tooltip[0]).length > 0;

                    if(elem[0] !== this.target[0] && elem[0] !== this.tooltip[0] && !isAncestor &&
                        !this.target.has(elem[0]).length && enabled
                    ) {
                        this.hide(event);
                    }
                });
            }

            // Check if the tooltip hides when inactive
            if('number' === typeof options.hide.inactive) {
                // Bind inactive method to show target(s) as a custom event
                this._bind(showTarget, 'qtip-'+this.id+'-inactive', inactiveMethod, 'inactive');

                // Define events which reset the 'inactive' event handler
                this._bind(hideTarget.add(tooltip), QTIP.inactiveEvents, inactiveMethod);
            }

            // Filter and bind events
            this._bindEvents(showEvents, hideEvents, showTarget, hideTarget, showMethod, hideMethod);

            // Mouse movement bindings
            this._bind(showTarget.add(tooltip), 'mousemove', function(event) {
                // Check if the tooltip hides when mouse is moved a certain distance
                if('number' === typeof options.hide.distance) {
                    var origin = this.cache.origin || {},
                        limit = this.options.hide.distance,
                        abs = Math.abs;

                    // Check if the movement has gone beyond the limit, and hide it if so
                    if(abs(event.pageX - origin.pageX) >= limit || abs(event.pageY - origin.pageY) >= limit) {
                        this.hide(event);
                    }
                }

                // Cache mousemove coords on show targets
                this._storeMouse(event);
            });

            // Mouse positioning events
            if(posOptions.target === 'mouse') {
                // If mouse adjustment is on...
                if(posOptions.adjust.mouse) {
                    // Apply a mouseleave event so we don't get problems with overlapping
                    if(options.hide.event) {
                        // Track if we're on the target or not
                        this._bind(showTarget, ['mouseenter', 'mouseleave'], function(event) {
                            if(!this.cache) {return FALSE; }
                            this.cache.onTarget = event.type === 'mouseenter';
                        });
                    }

                    // Update tooltip position on mousemove
                    this._bind(documentTarget, 'mousemove', function(event) {
                        // Update the tooltip position only if the tooltip is visible and adjustment is enabled
                        if(this.rendered && this.cache.onTarget && !this.tooltip.hasClass(CLASS_DISABLED) && this.tooltip[0].offsetWidth > 0) {
                            this.reposition(event);
                        }
                    });
                }
            }

            // Adjust positions of the tooltip on window resize if enabled
            if(posOptions.adjust.resize || viewportTarget.length) {
                this._bind( $.event.special.resize ? viewportTarget : windowTarget, 'resize', repositionMethod );
            }

            // Adjust tooltip position on scroll of the window or viewport element if present
            if(posOptions.adjust.scroll) {
                this._bind( windowTarget.add(posOptions.container), 'scroll', repositionMethod );
            }
        };

        // Un-assignment method
        PROTOTYPE._unassignEvents = function() {
            var options = this.options,
                showTargets = options.show.target,
                hideTargets = options.hide.target,
                targets = $.grep([
                    this.elements.target[0],
                    this.rendered && this.tooltip[0],
                    options.position.container[0],
                    options.position.viewport[0],
                    options.position.container.closest('html')[0], // unfocus
                    window,
                    document
                ], function(i) {
                    return typeof i === 'object';
                });

            // Add show and hide targets if they're valid
            if(showTargets && showTargets.toArray) {
                targets = targets.concat(showTargets.toArray());
            }
            if(hideTargets && hideTargets.toArray) {
                targets = targets.concat(hideTargets.toArray());
            }

            // Unbind the events
            this._unbind(targets)
                ._unbind(targets, 'destroy')
                ._unbind(targets, 'inactive');
        };

        // Apply common event handlers using delegate (avoids excessive .bind calls!)
        $(function() {
            delegate(SELECTOR, ['mouseenter', 'mouseleave'], function(event) {
                var state = event.type === 'mouseenter',
                    tooltip = $(event.currentTarget),
                    target = $(event.relatedTarget || event.target),
                    options = this.options;

                // On mouseenter...
                if(state) {
                    // Focus the tooltip on mouseenter (z-index stacking)
                    this.focus(event);

                    // Clear hide timer on tooltip hover to prevent it from closing
                    tooltip.hasClass(CLASS_FIXED) && !tooltip.hasClass(CLASS_DISABLED) && clearTimeout(this.timers.hide);
                }

                // On mouseleave...
                else {
                    // When mouse tracking is enabled, hide when we leave the tooltip and not onto the show target (if a hide event is set)
                    if(options.position.target === 'mouse' && options.position.adjust.mouse &&
                        options.hide.event && options.show.target && !target.closest(options.show.target[0]).length) {
                        this.hide(event);
                    }
                }

                // Add hover class
                tooltip.toggleClass(CLASS_HOVER, state);
            });

            // Define events which reset the 'inactive' event handler
            delegate('['+ATTR_ID+']', INACTIVE_EVENTS, inactiveMethod);
        });
        ;// Initialization method
        function init(elem, id, opts) {
            var obj, posOptions, attr, config, title,

            // Setup element references
                docBody = $(document.body),

            // Use document body instead of document element if needed
                newTarget = elem[0] === document ? docBody : elem,

            // Grab metadata from element if plugin is present
                metadata = (elem.metadata) ? elem.metadata(opts.metadata) : NULL,

            // If metadata type if HTML5, grab 'name' from the object instead, or use the regular data object otherwise
                metadata5 = opts.metadata.type === 'html5' && metadata ? metadata[opts.metadata.name] : NULL,

            // Grab data from metadata.name (or data-qtipopts as fallback) using .data() method,
                html5 = elem.data(opts.metadata.name || 'qtipopts');

            // If we don't get an object returned attempt to parse it manualyl without parseJSON
            try { html5 = typeof html5 === 'string' ? $.parseJSON(html5) : html5; } catch(e) {}

            // Merge in and sanitize metadata
            config = $.extend(TRUE, {}, QTIP.defaults, opts,
                typeof html5 === 'object' ? sanitizeOptions(html5) : NULL,
                sanitizeOptions(metadata5 || metadata));

            // Re-grab our positioning options now we've merged our metadata and set id to passed value
            posOptions = config.position;
            config.id = id;

            // Setup missing content if none is detected
            if('boolean' === typeof config.content.text) {
                attr = elem.attr(config.content.attr);

                // Grab from supplied attribute if available
                if(config.content.attr !== FALSE && attr) { config.content.text = attr; }

                // No valid content was found, abort render
                else { return FALSE; }
            }

            // Setup target options
            if(!posOptions.container.length) { posOptions.container = docBody; }
            if(posOptions.target === FALSE) { posOptions.target = newTarget; }
            if(config.show.target === FALSE) { config.show.target = newTarget; }
            if(config.show.solo === TRUE) { config.show.solo = posOptions.container.closest('body'); }
            if(config.hide.target === FALSE) { config.hide.target = newTarget; }
            if(config.position.viewport === TRUE) { config.position.viewport = posOptions.container; }

            // Ensure we only use a single container
            posOptions.container = posOptions.container.eq(0);

            // Convert position corner values into x and y strings
            posOptions.at = new CORNER(posOptions.at, TRUE);
            posOptions.my = new CORNER(posOptions.my);

            // Destroy previous tooltip if overwrite is enabled, or skip element if not
            if(elem.data(NAMESPACE)) {
                if(config.overwrite) {
                    elem.qtip('destroy', true);
                }
                else if(config.overwrite === FALSE) {
                    return FALSE;
                }
            }

            // Add has-qtip attribute
            elem.attr(ATTR_HAS, id);

            // Remove title attribute and store it if present
            if(config.suppress && (title = elem.attr('title'))) {
                // Final attr call fixes event delegatiom and IE default tooltip showing problem
                elem.removeAttr('title').attr(oldtitle, title).attr('title', '');
            }

            // Initialize the tooltip and add API reference
            obj = new QTip(elem, config, id, !!attr);
            elem.data(NAMESPACE, obj);

            return obj;
        }

        // jQuery $.fn extension method
        QTIP = $.fn.qtip = function(options, notation, newValue)
        {
            var command = ('' + options).toLowerCase(), // Parse command
                returned = NULL,
                args = $.makeArray(arguments).slice(1),
                event = args[args.length - 1],
                opts = this[0] ? $.data(this[0], NAMESPACE) : NULL;

            // Check for API request
            if((!arguments.length && opts) || command === 'api') {
                return opts;
            }

            // Execute API command if present
            else if('string' === typeof options) {
                this.each(function() {
                    var api = $.data(this, NAMESPACE);
                    if(!api) { return TRUE; }

                    // Cache the event if possible
                    if(event && event.timeStamp) { api.cache.event = event; }

                    // Check for specific API commands
                    if(notation && (command === 'option' || command === 'options')) {
                        if(newValue !== undefined || $.isPlainObject(notation)) {
                            api.set(notation, newValue);
                        }
                        else {
                            returned = api.get(notation);
                            return FALSE;
                        }
                    }

                    // Execute API command
                    else if(api[command]) {
                        api[command].apply(api, args);
                    }
                });

                return returned !== NULL ? returned : this;
            }

            // No API commands. validate provided options and setup qTips
            else if('object' === typeof options || !arguments.length) {
                // Sanitize options first
                opts = sanitizeOptions($.extend(TRUE, {}, options));

                return this.each(function(i) {
                    var api, id;

                    // Find next available ID, or use custom ID if provided
                    id = $.isArray(opts.id) ? opts.id[i] : opts.id;
                    id = !id || id === FALSE || id.length < 1 || QTIP.api[id] ? QTIP.nextid++ : id;

                    // Initialize the qTip and re-grab newly sanitized options
                    api = init($(this), id, opts);
                    if(api === FALSE) { return TRUE; }
                    else { QTIP.api[id] = api; }

                    // Initialize plugins
                    $.each(PLUGINS, function() {
                        if(this.initialize === 'initialize') { this(api); }
                    });

                    // Assign initial pre-render events
                    api._assignInitialEvents(event);
                });
            }
        };

        // Expose class
        $.qtip = QTip;

        // Populated in render method
        QTIP.api = {};
        ;$.each({
            /* Allow other plugins to successfully retrieve the title of an element with a qTip applied */
            attr: function(attr, val) {
                if(this.length) {
                    var self = this[0],
                        title = 'title',
                        api = $.data(self, 'qtip');

                    if(attr === title && api && 'object' === typeof api && api.options.suppress) {
                        if(arguments.length < 2) {
                            return $.attr(self, oldtitle);
                        }

                        // If qTip is rendered and title was originally used as content, update it
                        if(api && api.options.content.attr === title && api.cache.attr) {
                            api.set('content.text', val);
                        }

                        // Use the regular attr method to set, then cache the result
                        return this.attr(oldtitle, val);
                    }
                }

                return $.fn['attr'+replaceSuffix].apply(this, arguments);
            },

            /* Allow clone to correctly retrieve cached title attributes */
            clone: function(keepData) {
                var titles = $([]), title = 'title',

                // Clone our element using the real clone method
                    elems = $.fn['clone'+replaceSuffix].apply(this, arguments);

                // Grab all elements with an oldtitle set, and change it to regular title attribute, if keepData is false
                if(!keepData) {
                    elems.filter('['+oldtitle+']').attr('title', function() {
                        return $.attr(this, oldtitle);
                    })
                        .removeAttr(oldtitle);
                }

                return elems;
            }
        }, function(name, func) {
            if(!func || $.fn[name+replaceSuffix]) { return TRUE; }

            var old = $.fn[name+replaceSuffix] = $.fn[name];
            $.fn[name] = function() {
                return func.apply(this, arguments) || old.apply(this, arguments);
            };
        });

        /* Fire off 'removeqtip' handler in $.cleanData if jQuery UI not present (it already does similar).
         * This snippet is taken directly from jQuery UI source code found here:
         *     http://code.jquery.com/ui/jquery-ui-git.js
         */
        if(!$.ui) {
            $['cleanData'+replaceSuffix] = $.cleanData;
            $.cleanData = function( elems ) {
                for(var i = 0, elem; (elem = $( elems[i] )).length; i++) {
                    if(elem.attr(ATTR_HAS)) {
                        try { elem.triggerHandler('removeqtip'); }
                        catch( e ) {}
                    }
                }
                $['cleanData'+replaceSuffix].apply(this, arguments);
            };
        }
        ;// qTip version
        QTIP.version = '2.2.1';

        // Base ID for all qTips
        QTIP.nextid = 0;

        // Inactive events array
        QTIP.inactiveEvents = INACTIVE_EVENTS;

        // Base z-index for all qTips
        QTIP.zindex = 15000;

        // Define configuration defaults
        QTIP.defaults = {
            prerender: FALSE,
            id: FALSE,
            overwrite: TRUE,
            suppress: TRUE,
            content: {
                text: TRUE,
                attr: 'title',
                title: FALSE,
                button: FALSE
            },
            position: {
                my: 'top left',
                at: 'bottom right',
                target: FALSE,
                container: FALSE,
                viewport: FALSE,
                adjust: {
                    x: 0, y: 0,
                    mouse: TRUE,
                    scroll: TRUE,
                    resize: TRUE,
                    method: 'flipinvert flipinvert'
                },
                effect: function(api, pos, viewport) {
                    $(this).animate(pos, {
                        duration: 200,
                        queue: FALSE
                    });
                }
            },
            show: {
                target: FALSE,
                event: 'mouseenter',
                effect: TRUE,
                delay: 90,
                solo: FALSE,
                ready: FALSE,
                autofocus: FALSE
            },
            hide: {
                target: FALSE,
                event: 'mouseleave',
                effect: TRUE,
                delay: 0,
                fixed: FALSE,
                inactive: FALSE,
                leave: 'window',
                distance: FALSE
            },
            style: {
                classes: '',
                widget: FALSE,
                width: FALSE,
                height: FALSE,
                def: TRUE
            },
            events: {
                render: NULL,
                move: NULL,
                show: NULL,
                hide: NULL,
                toggle: NULL,
                visible: NULL,
                hidden: NULL,
                focus: NULL,
                blur: NULL
            }
        };
        ;var TIP,

        // .bind()/.on() namespace
            TIPNS = '.qtip-tip',

        // Common CSS strings
            MARGIN = 'margin',
            BORDER = 'border',
            COLOR = 'color',
            BG_COLOR = 'background-color',
            TRANSPARENT = 'transparent',
            IMPORTANT = ' !important',

        // Check if the browser supports <canvas/> elements
            HASCANVAS = !!document.createElement('canvas').getContext,

        // Invalid colour values used in parseColours()
            INVALID = /rgba?\(0, 0, 0(, 0)?\)|transparent|#123456/i;

        // Camel-case method, taken from jQuery source
        // http://code.jquery.com/jquery-1.8.0.js
        function camel(s) { return s.charAt(0).toUpperCase() + s.slice(1); }

        /*
         * Modified from Modernizr's testPropsAll()
         * http://modernizr.com/downloads/modernizr-latest.js
         */
        var cssProps = {}, cssPrefixes = ["Webkit", "O", "Moz", "ms"];
        function vendorCss(elem, prop) {
            var ucProp = prop.charAt(0).toUpperCase() + prop.slice(1),
                props = (prop + ' ' + cssPrefixes.join(ucProp + ' ') + ucProp).split(' '),
                cur, val, i = 0;

            // If the property has already been mapped...
            if(cssProps[prop]) { return elem.css(cssProps[prop]); }

            while((cur = props[i++])) {
                if((val = elem.css(cur)) !== undefined) {
                    return cssProps[prop] = cur, val;
                }
            }
        }

        // Parse a given elements CSS property into an int
        function intCss(elem, prop) {
            return Math.ceil(parseFloat(vendorCss(elem, prop)));
        }


        // VML creation (for IE only)
        if(!HASCANVAS) {
            var createVML = function(tag, props, style) {
                return '<qtipvml:'+tag+' xmlns="urn:schemas-microsoft.com:vml" class="qtip-vml" '+(props||'')+
                    ' style="behavior: url(#default#VML); '+(style||'')+ '" />';
            };
        }

        // Canvas only definitions
        else {
            var PIXEL_RATIO = window.devicePixelRatio || 1,
                BACKING_STORE_RATIO = (function() {
                    var context = document.createElement('canvas').getContext('2d');
                    return context.backingStorePixelRatio || context.webkitBackingStorePixelRatio || context.mozBackingStorePixelRatio ||
                        context.msBackingStorePixelRatio || context.oBackingStorePixelRatio || 1;
                }()),
                SCALE = PIXEL_RATIO / BACKING_STORE_RATIO;
        }


        function Tip(qtip, options) {
            this._ns = 'tip';
            this.options = options;
            this.offset = options.offset;
            this.size = [ options.width, options.height ];

            // Initialize
            this.init( (this.qtip = qtip) );
        }

        $.extend(Tip.prototype, {
            init: function(qtip) {
                var context, tip;

                // Create tip element and prepend to the tooltip
                tip = this.element = qtip.elements.tip = $('<div />', { 'class': NAMESPACE+'-tip' }).prependTo(qtip.tooltip);

                // Create tip drawing element(s)
                if(HASCANVAS) {
                    // save() as soon as we create the canvas element so FF2 doesn't bork on our first restore()!
                    context = $('<canvas />').appendTo(this.element)[0].getContext('2d');

                    // Setup constant parameters
                    context.lineJoin = 'miter';
                    context.miterLimit = 100000;
                    context.save();
                }
                else {
                    context = createVML('shape', 'coordorigin="0,0"', 'position:absolute;');
                    this.element.html(context + context);

                    // Prevent mousing down on the tip since it causes problems with .live() handling in IE due to VML
                    qtip._bind( $('*', tip).add(tip), ['click', 'mousedown'], function(event) { event.stopPropagation(); }, this._ns);
                }

                // Bind update events
                qtip._bind(qtip.tooltip, 'tooltipmove', this.reposition, this._ns, this);

                // Create it
                this.create();
            },

            _swapDimensions: function() {
                this.size[0] = this.options.height;
                this.size[1] = this.options.width;
            },
            _resetDimensions: function() {
                this.size[0] = this.options.width;
                this.size[1] = this.options.height;
            },

            _useTitle: function(corner) {
                var titlebar = this.qtip.elements.titlebar;
                return titlebar && (
                        corner.y === TOP || (corner.y === CENTER && this.element.position().top + (this.size[1] / 2) + this.options.offset < titlebar.outerHeight(TRUE))
                    );
            },

            _parseCorner: function(corner) {
                var my = this.qtip.options.position.my;

                // Detect corner and mimic properties
                if(corner === FALSE || my === FALSE) {
                    corner = FALSE;
                }
                else if(corner === TRUE) {
                    corner = new CORNER( my.string() );
                }
                else if(!corner.string) {
                    corner = new CORNER(corner);
                    corner.fixed = TRUE;
                }

                return corner;
            },

            _parseWidth: function(corner, side, use) {
                var elements = this.qtip.elements,
                    prop = BORDER + camel(side) + 'Width';

                return (use ? intCss(use, prop) : (
                        intCss(elements.content, prop) ||
                        intCss(this._useTitle(corner) && elements.titlebar || elements.content, prop) ||
                        intCss(elements.tooltip, prop)
                    )) || 0;
            },

            _parseRadius: function(corner) {
                var elements = this.qtip.elements,
                    prop = BORDER + camel(corner.y) + camel(corner.x) + 'Radius';

                return BROWSER.ie < 9 ? 0 :
                intCss(this._useTitle(corner) && elements.titlebar || elements.content, prop) ||
                intCss(elements.tooltip, prop) || 0;
            },

            _invalidColour: function(elem, prop, compare) {
                var val = elem.css(prop);
                return !val || (compare && val === elem.css(compare)) || INVALID.test(val) ? FALSE : val;
            },

            _parseColours: function(corner) {
                var elements = this.qtip.elements,
                    tip = this.element.css('cssText', ''),
                    borderSide = BORDER + camel(corner[ corner.precedance ]) + camel(COLOR),
                    colorElem = this._useTitle(corner) && elements.titlebar || elements.content,
                    css = this._invalidColour, color = [];

                // Attempt to detect the background colour from various elements, left-to-right precedance
                color[0] = css(tip, BG_COLOR) || css(colorElem, BG_COLOR) || css(elements.content, BG_COLOR) ||
                    css(elements.tooltip, BG_COLOR) || tip.css(BG_COLOR);

                // Attempt to detect the correct border side colour from various elements, left-to-right precedance
                color[1] = css(tip, borderSide, COLOR) || css(colorElem, borderSide, COLOR) ||
                    css(elements.content, borderSide, COLOR) || css(elements.tooltip, borderSide, COLOR) || elements.tooltip.css(borderSide);

                // Reset background and border colours
                $('*', tip).add(tip).css('cssText', BG_COLOR+':'+TRANSPARENT+IMPORTANT+';'+BORDER+':0'+IMPORTANT+';');

                return color;
            },

            _calculateSize: function(corner) {
                var y = corner.precedance === Y,
                    width = this.options['width'],
                    height = this.options['height'],
                    isCenter = corner.abbrev() === 'c',
                    base = (y ? width: height) * (isCenter ? 0.5 : 1),
                    pow = Math.pow,
                    round = Math.round,
                    bigHyp, ratio, result,

                    smallHyp = Math.sqrt( pow(base, 2) + pow(height, 2) ),
                    hyp = [ (this.border / base) * smallHyp, (this.border / height) * smallHyp ];

                hyp[2] = Math.sqrt( pow(hyp[0], 2) - pow(this.border, 2) );
                hyp[3] = Math.sqrt( pow(hyp[1], 2) - pow(this.border, 2) );

                bigHyp = smallHyp + hyp[2] + hyp[3] + (isCenter ? 0 : hyp[0]);
                ratio = bigHyp / smallHyp;

                result = [ round(ratio * width), round(ratio * height) ];
                return y ? result : result.reverse();
            },

            // Tip coordinates calculator
            _calculateTip: function(corner, size, scale) {
                scale = scale || 1;
                size = size || this.size;

                var width = size[0] * scale,
                    height = size[1] * scale,
                    width2 = Math.ceil(width / 2), height2 = Math.ceil(height / 2),

                // Define tip coordinates in terms of height and width values
                    tips = {
                        br:	[0,0,		width,height,	width,0],
                        bl:	[0,0,		width,0,		0,height],
                        tr:	[0,height,	width,0,		width,height],
                        tl:	[0,0,		0,height,		width,height],
                        tc:	[0,height,	width2,0,		width,height],
                        bc:	[0,0,		width,0,		width2,height],
                        rc:	[0,0,		width,height2,	0,height],
                        lc:	[width,0,	width,height,	0,height2]
                    };

                // Set common side shapes
                tips.lt = tips.br; tips.rt = tips.bl;
                tips.lb = tips.tr; tips.rb = tips.tl;

                return tips[ corner.abbrev() ];
            },

            // Tip coordinates drawer (canvas)
            _drawCoords: function(context, coords) {
                context.beginPath();
                context.moveTo(coords[0], coords[1]);
                context.lineTo(coords[2], coords[3]);
                context.lineTo(coords[4], coords[5]);
                context.closePath();
            },

            create: function() {
                // Determine tip corner
                var c = this.corner = (HASCANVAS || BROWSER.ie) && this._parseCorner(this.options.corner);

                // If we have a tip corner...
                if( (this.enabled = !!this.corner && this.corner.abbrev() !== 'c') ) {
                    // Cache it
                    this.qtip.cache.corner = c.clone();

                    // Create it
                    this.update();
                }

                // Toggle tip element
                this.element.toggle(this.enabled);

                return this.corner;
            },

            update: function(corner, position) {
                if(!this.enabled) { return this; }

                var elements = this.qtip.elements,
                    tip = this.element,
                    inner = tip.children(),
                    options = this.options,
                    curSize = this.size,
                    mimic = options.mimic,
                    round = Math.round,
                    color, precedance, context,
                    coords, bigCoords, translate, newSize, border, BACKING_STORE_RATIO;

                // Re-determine tip if not already set
                if(!corner) { corner = this.qtip.cache.corner || this.corner; }

                // Use corner property if we detect an invalid mimic value
                if(mimic === FALSE) { mimic = corner; }

                // Otherwise inherit mimic properties from the corner object as necessary
                else {
                    mimic = new CORNER(mimic);
                    mimic.precedance = corner.precedance;

                    if(mimic.x === 'inherit') { mimic.x = corner.x; }
                    else if(mimic.y === 'inherit') { mimic.y = corner.y; }
                    else if(mimic.x === mimic.y) {
                        mimic[ corner.precedance ] = corner[ corner.precedance ];
                    }
                }
                precedance = mimic.precedance;

                // Ensure the tip width.height are relative to the tip position
                if(corner.precedance === X) { this._swapDimensions(); }
                else { this._resetDimensions(); }

                // Update our colours
                color = this.color = this._parseColours(corner);

                // Detect border width, taking into account colours
                if(color[1] !== TRANSPARENT) {
                    // Grab border width
                    border = this.border = this._parseWidth(corner, corner[corner.precedance]);

                    // If border width isn't zero, use border color as fill if it's not invalid (1.0 style tips)
                    if(options.border && border < 1 && !INVALID.test(color[1])) { color[0] = color[1]; }

                    // Set border width (use detected border width if options.border is true)
                    this.border = border = options.border !== TRUE ? options.border : border;
                }

                // Border colour was invalid, set border to zero
                else { this.border = border = 0; }

                // Determine tip size
                newSize = this.size = this._calculateSize(corner);
                tip.css({
                    width: newSize[0],
                    height: newSize[1],
                    lineHeight: newSize[1]+'px'
                });

                // Calculate tip translation
                if(corner.precedance === Y) {
                    translate = [
                        round(mimic.x === LEFT ? border : mimic.x === RIGHT ? newSize[0] - curSize[0] - border : (newSize[0] - curSize[0]) / 2),
                        round(mimic.y === TOP ? newSize[1] - curSize[1] : 0)
                    ];
                }
                else {
                    translate = [
                        round(mimic.x === LEFT ? newSize[0] - curSize[0] : 0),
                        round(mimic.y === TOP ? border : mimic.y === BOTTOM ? newSize[1] - curSize[1] - border : (newSize[1] - curSize[1]) / 2)
                    ];
                }

                // Canvas drawing implementation
                if(HASCANVAS) {
                    // Grab canvas context and clear/save it
                    context = inner[0].getContext('2d');
                    context.restore(); context.save();
                    context.clearRect(0,0,6000,6000);

                    // Calculate coordinates
                    coords = this._calculateTip(mimic, curSize, SCALE);
                    bigCoords = this._calculateTip(mimic, this.size, SCALE);

                    // Set the canvas size using calculated size
                    inner.attr(WIDTH, newSize[0] * SCALE).attr(HEIGHT, newSize[1] * SCALE);
                    inner.css(WIDTH, newSize[0]).css(HEIGHT, newSize[1]);

                    // Draw the outer-stroke tip
                    this._drawCoords(context, bigCoords);
                    context.fillStyle = color[1];
                    context.fill();

                    // Draw the actual tip
                    context.translate(translate[0] * SCALE, translate[1] * SCALE);
                    this._drawCoords(context, coords);
                    context.fillStyle = color[0];
                    context.fill();
                }

                // VML (IE Proprietary implementation)
                else {
                    // Calculate coordinates
                    coords = this._calculateTip(mimic);

                    // Setup coordinates string
                    coords = 'm' + coords[0] + ',' + coords[1] + ' l' + coords[2] +
                        ',' + coords[3] + ' ' + coords[4] + ',' + coords[5] + ' xe';

                    // Setup VML-specific offset for pixel-perfection
                    translate[2] = border && /^(r|b)/i.test(corner.string()) ?
                        BROWSER.ie === 8 ? 2 : 1 : 0;

                    // Set initial CSS
                    inner.css({
                        coordsize: (newSize[0]+border) + ' ' + (newSize[1]+border),
                        antialias: ''+(mimic.string().indexOf(CENTER) > -1),
                        left: translate[0] - (translate[2] * Number(precedance === X)),
                        top: translate[1] - (translate[2] * Number(precedance === Y)),
                        width: newSize[0] + border,
                        height: newSize[1] + border
                    })
                        .each(function(i) {
                            var $this = $(this);

                            // Set shape specific attributes
                            $this[ $this.prop ? 'prop' : 'attr' ]({
                                coordsize: (newSize[0]+border) + ' ' + (newSize[1]+border),
                                path: coords,
                                fillcolor: color[0],
                                filled: !!i,
                                stroked: !i
                            })
                                .toggle(!!(border || i));

                            // Check if border is enabled and add stroke element
                            !i && $this.html( createVML(
                                'stroke', 'weight="'+(border*2)+'px" color="'+color[1]+'" miterlimit="1000" joinstyle="miter"'
                            ) );
                        });
                }

                // Opera bug #357 - Incorrect tip position
                // https://github.com/Craga89/qTip2/issues/367
                window.opera && setTimeout(function() {
                    elements.tip.css({
                        display: 'inline-block',
                        visibility: 'visible'
                    });
                }, 1);

                // Position if needed
                if(position !== FALSE) { this.calculate(corner, newSize); }
            },

            calculate: function(corner, size) {
                if(!this.enabled) { return FALSE; }

                var self = this,
                    elements = this.qtip.elements,
                    tip = this.element,
                    userOffset = this.options.offset,
                    isWidget = elements.tooltip.hasClass('ui-widget'),
                    position = {  },
                    precedance, corners;

                // Inherit corner if not provided
                corner = corner || this.corner;
                precedance = corner.precedance;

                // Determine which tip dimension to use for adjustment
                size = size || this._calculateSize(corner);

                // Setup corners and offset array
                corners = [ corner.x, corner.y ];
                if(precedance === X) { corners.reverse(); }

                // Calculate tip position
                $.each(corners, function(i, side) {
                    var b, bc, br;

                    if(side === CENTER) {
                        b = precedance === Y ? LEFT : TOP;
                        position[ b ] = '50%';
                        position[MARGIN+'-' + b] = -Math.round(size[ precedance === Y ? 0 : 1 ] / 2) + userOffset;
                    }
                    else {
                        b = self._parseWidth(corner, side, elements.tooltip);
                        bc = self._parseWidth(corner, side, elements.content);
                        br = self._parseRadius(corner);

                        position[ side ] = Math.max(-self.border, i ? bc : (userOffset + (br > b ? br : -b)));
                    }
                });

                // Adjust for tip size
                position[ corner[precedance] ] -= size[ precedance === X ? 0 : 1 ];

                // Set and return new position
                tip.css({ margin: '', top: '', bottom: '', left: '', right: '' }).css(position);
                return position;
            },

            reposition: function(event, api, pos, viewport) {
                if(!this.enabled) { return; }

                var cache = api.cache,
                    newCorner = this.corner.clone(),
                    adjust = pos.adjusted,
                    method = api.options.position.adjust.method.split(' '),
                    horizontal = method[0],
                    vertical = method[1] || method[0],
                    shift = { left: FALSE, top: FALSE, x: 0, y: 0 },
                    offset, css = {}, props;

                function shiftflip(direction, precedance, popposite, side, opposite) {
                    // Horizontal - Shift or flip method
                    if(direction === SHIFT && newCorner.precedance === precedance && adjust[side] && newCorner[popposite] !== CENTER) {
                        newCorner.precedance = newCorner.precedance === X ? Y : X;
                    }
                    else if(direction !== SHIFT && adjust[side]){
                        newCorner[precedance] = newCorner[precedance] === CENTER ?
                            (adjust[side] > 0 ? side : opposite) : (newCorner[precedance] === side ? opposite : side);
                    }
                }

                function shiftonly(xy, side, opposite) {
                    if(newCorner[xy] === CENTER) {
                        css[MARGIN+'-'+side] = shift[xy] = offset[MARGIN+'-'+side] - adjust[side];
                    }
                    else {
                        props = offset[opposite] !== undefined ?
                            [ adjust[side], -offset[side] ] : [ -adjust[side], offset[side] ];

                        if( (shift[xy] = Math.max(props[0], props[1])) > props[0] ) {
                            pos[side] -= adjust[side];
                            shift[side] = FALSE;
                        }

                        css[ offset[opposite] !== undefined ? opposite : side ] = shift[xy];
                    }
                }

                // If our tip position isn't fixed e.g. doesn't adjust with viewport...
                if(this.corner.fixed !== TRUE) {
                    // Perform shift/flip adjustments
                    shiftflip(horizontal, X, Y, LEFT, RIGHT);
                    shiftflip(vertical, Y, X, TOP, BOTTOM);

                    // Update and redraw the tip if needed (check cached details of last drawn tip)
                    if(newCorner.string() !== cache.corner.string() || cache.cornerTop !== adjust.top || cache.cornerLeft !== adjust.left) {
                        this.update(newCorner, FALSE);
                    }
                }

                // Setup tip offset properties
                offset = this.calculate(newCorner);

                // Readjust offset object to make it left/top
                if(offset.right !== undefined) { offset.left = -offset.right; }
                if(offset.bottom !== undefined) { offset.top = -offset.bottom; }
                offset.user = this.offset;

                // Perform shift adjustments
                if(shift.left = (horizontal === SHIFT && !!adjust.left)) { shiftonly(X, LEFT, RIGHT); }
                if(shift.top = (vertical === SHIFT && !!adjust.top)) { shiftonly(Y, TOP, BOTTOM); }

                /*
                 * If the tip is adjusted in both dimensions, or in a
                 * direction that would cause it to be anywhere but the
                 * outer border, hide it!
                 */
                this.element.css(css).toggle(
                    !((shift.x && shift.y) || (newCorner.x === CENTER && shift.y) || (newCorner.y === CENTER && shift.x))
                );

                // Adjust position to accomodate tip dimensions
                pos.left -= offset.left.charAt ? offset.user :
                    horizontal !== SHIFT || shift.top || !shift.left && !shift.top ? offset.left + this.border : 0;
                pos.top -= offset.top.charAt ? offset.user :
                    vertical !== SHIFT || shift.left || !shift.left && !shift.top ? offset.top + this.border : 0;

                // Cache details
                cache.cornerLeft = adjust.left; cache.cornerTop = adjust.top;
                cache.corner = newCorner.clone();
            },

            destroy: function() {
                // Unbind events
                this.qtip._unbind(this.qtip.tooltip, this._ns);

                // Remove the tip element(s)
                if(this.qtip.elements.tip) {
                    this.qtip.elements.tip.find('*')
                        .remove().end().remove();
                }
            }
        });

        TIP = PLUGINS.tip = function(api) {
            return new Tip(api, api.options.style.tip);
        };

        // Initialize tip on render
        TIP.initialize = 'render';

        // Setup plugin sanitization options
        TIP.sanitize = function(options) {
            if(options.style && 'tip' in options.style) {
                var opts = options.style.tip;
                if(typeof opts !== 'object') { opts = options.style.tip = { corner: opts }; }
                if(!(/string|boolean/i).test(typeof opts.corner)) { opts.corner = TRUE; }
            }
        };

        // Add new option checks for the plugin
        CHECKS.tip = {
            '^position.my|style.tip.(corner|mimic|border)$': function() {
                // Make sure a tip can be drawn
                this.create();

                // Reposition the tooltip
                this.qtip.reposition();
            },
            '^style.tip.(height|width)$': function(obj) {
                // Re-set dimensions and redraw the tip
                this.size = [ obj.width, obj.height ];
                this.update();

                // Reposition the tooltip
                this.qtip.reposition();
            },
            '^content.title|style.(classes|widget)$': function() {
                this.update();
            }
        };

        // Extend original qTip defaults
        $.extend(TRUE, QTIP.defaults, {
            style: {
                tip: {
                    corner: TRUE,
                    mimic: FALSE,
                    width: 6,
                    height: 6,
                    border: TRUE,
                    offset: 0
                }
            }
        });
        ;var MODAL, OVERLAY,
            MODALCLASS = 'qtip-modal',
            MODALSELECTOR = '.'+MODALCLASS;

        OVERLAY = function()
        {
            var self = this,
                focusableElems = {},
                current, onLast,
                prevState, elem;

            // Modified code from jQuery UI 1.10.0 source
            // http://code.jquery.com/ui/1.10.0/jquery-ui.js
            function focusable(element) {
                // Use the defined focusable checker when possible
                if($.expr[':'].focusable) { return $.expr[':'].focusable; }

                var isTabIndexNotNaN = !isNaN($.attr(element, 'tabindex')),
                    nodeName = element.nodeName && element.nodeName.toLowerCase(),
                    map, mapName, img;

                if('area' === nodeName) {
                    map = element.parentNode;
                    mapName = map.name;
                    if(!element.href || !mapName || map.nodeName.toLowerCase() !== 'map') {
                        return false;
                    }
                    img = $('img[usemap=#' + mapName + ']')[0];
                    return !!img && img.is(':visible');
                }
                return (/input|select|textarea|button|object/.test( nodeName ) ?
                        !element.disabled :
                        'a' === nodeName ?
                        element.href || isTabIndexNotNaN :
                            isTabIndexNotNaN
                );
            }

            // Focus inputs using cached focusable elements (see update())
            function focusInputs(blurElems) {
                // Blurring body element in IE causes window.open windows to unfocus!
                if(focusableElems.length < 1 && blurElems.length) { blurElems.not('body').blur(); }

                // Focus the inputs
                else { focusableElems.first().focus(); }
            }

            // Steal focus from elements outside tooltip
            function stealFocus(event) {
                if(!elem.is(':visible')) { return; }

                var target = $(event.target),
                    tooltip = current.tooltip,
                    container = target.closest(SELECTOR),
                    targetOnTop;

                // Determine if input container target is above this
                targetOnTop = container.length < 1 ? FALSE :
                    (parseInt(container[0].style.zIndex, 10) > parseInt(tooltip[0].style.zIndex, 10));

                // If we're showing a modal, but focus has landed on an input below
                // this modal, divert focus to the first visible input in this modal
                // or if we can't find one... the tooltip itself
                if(!targetOnTop && target.closest(SELECTOR)[0] !== tooltip[0]) {
                    focusInputs(target);
                }

                // Detect when we leave the last focusable element...
                onLast = event.target === focusableElems[focusableElems.length - 1];
            }

            $.extend(self, {
                init: function() {
                    // Create document overlay
                    elem = self.elem = $('<div />', {
                        id: 'qtip-overlay',
                        html: '<div></div>',
                        mousedown: function() { return FALSE; }
                    })
                        .hide();

                    // Make sure we can't focus anything outside the tooltip
                    $(document.body).bind('focusin'+MODALSELECTOR, stealFocus);

                    // Apply keyboard "Escape key" close handler
                    $(document).bind('keydown'+MODALSELECTOR, function(event) {
                        if(current && current.options.show.modal.escape && event.keyCode === 27) {
                            current.hide(event);
                        }
                    });

                    // Apply click handler for blur option
                    elem.bind('click'+MODALSELECTOR, function(event) {
                        if(current && current.options.show.modal.blur) {
                            current.hide(event);
                        }
                    });

                    return self;
                },

                update: function(api) {
                    // Update current API reference
                    current = api;

                    // Update focusable elements if enabled
                    if(api.options.show.modal.stealfocus !== FALSE) {
                        focusableElems = api.tooltip.find('*').filter(function() {
                            return focusable(this);
                        });
                    }
                    else { focusableElems = []; }
                },

                toggle: function(api, state, duration) {
                    var docBody = $(document.body),
                        tooltip = api.tooltip,
                        options = api.options.show.modal,
                        effect = options.effect,
                        type = state ? 'show': 'hide',
                        visible = elem.is(':visible'),
                        visibleModals = $(MODALSELECTOR).filter(':visible:not(:animated)').not(tooltip),
                        zindex;

                    // Set active tooltip API reference
                    self.update(api);

                    // If the modal can steal the focus...
                    // Blur the current item and focus anything in the modal we an
                    if(state && options.stealfocus !== FALSE) {
                        focusInputs( $(':focus') );
                    }

                    // Toggle backdrop cursor style on show
                    elem.toggleClass('blurs', options.blur);

                    // Append to body on show
                    if(state) {
                        elem.appendTo(document.body);
                    }

                    // Prevent modal from conflicting with show.solo, and don't hide backdrop is other modals are visible
                    if((elem.is(':animated') && visible === state && prevState !== FALSE) || (!state && visibleModals.length)) {
                        return self;
                    }

                    // Stop all animations
                    elem.stop(TRUE, FALSE);

                    // Use custom function if provided
                    if($.isFunction(effect)) {
                        effect.call(elem, state);
                    }

                    // If no effect type is supplied, use a simple toggle
                    else if(effect === FALSE) {
                        elem[ type ]();
                    }

                    // Use basic fade function
                    else {
                        elem.fadeTo( parseInt(duration, 10) || 90, state ? 1 : 0, function() {
                            if(!state) { elem.hide(); }
                        });
                    }

                    // Reset position and detach from body on hide
                    if(!state) {
                        elem.queue(function(next) {
                            elem.css({ left: '', top: '' });
                            if(!$(MODALSELECTOR).length) { elem.detach(); }
                            next();
                        });
                    }

                    // Cache the state
                    prevState = state;

                    // If the tooltip is destroyed, set reference to null
                    if(current.destroyed) { current = NULL; }

                    return self;
                }
            });

            self.init();
        };
        OVERLAY = new OVERLAY();

        function Modal(api, options) {
            this.options = options;
            this._ns = '-modal';

            this.init( (this.qtip = api) );
        }

        $.extend(Modal.prototype, {
            init: function(qtip) {
                var tooltip = qtip.tooltip;

                // If modal is disabled... return
                if(!this.options.on) { return this; }

                // Set overlay reference
                qtip.elements.overlay = OVERLAY.elem;

                // Add unique attribute so we can grab modal tooltips easily via a SELECTOR, and set z-index
                tooltip.addClass(MODALCLASS).css('z-index', QTIP.modal_zindex + $(MODALSELECTOR).length);

                // Apply our show/hide/focus modal events
                qtip._bind(tooltip, ['tooltipshow', 'tooltiphide'], function(event, api, duration) {
                    var oEvent = event.originalEvent;

                    // Make sure mouseout doesn't trigger a hide when showing the modal and mousing onto backdrop
                    if(event.target === tooltip[0]) {
                        if(oEvent && event.type === 'tooltiphide' && /mouse(leave|enter)/.test(oEvent.type) && $(oEvent.relatedTarget).closest(OVERLAY.elem[0]).length) {
                            try { event.preventDefault(); } catch(e) {}
                        }
                        else if(!oEvent || (oEvent && oEvent.type !== 'tooltipsolo')) {
                            this.toggle(event, event.type === 'tooltipshow', duration);
                        }
                    }
                }, this._ns, this);

                // Adjust modal z-index on tooltip focus
                qtip._bind(tooltip, 'tooltipfocus', function(event, api) {
                    // If focus was cancelled before it reached us, don't do anything
                    if(event.isDefaultPrevented() || event.target !== tooltip[0]) { return; }

                    var qtips = $(MODALSELECTOR),

                    // Keep the modal's lower than other, regular qtips
                        newIndex = QTIP.modal_zindex + qtips.length,
                        curIndex = parseInt(tooltip[0].style.zIndex, 10);

                    // Set overlay z-index
                    OVERLAY.elem[0].style.zIndex = newIndex - 1;

                    // Reduce modal z-index's and keep them properly ordered
                    qtips.each(function() {
                        if(this.style.zIndex > curIndex) {
                            this.style.zIndex -= 1;
                        }
                    });

                    // Fire blur event for focused tooltip
                    qtips.filter('.' + CLASS_FOCUS).qtip('blur', event.originalEvent);

                    // Set the new z-index
                    tooltip.addClass(CLASS_FOCUS)[0].style.zIndex = newIndex;

                    // Set current
                    OVERLAY.update(api);

                    // Prevent default handling
                    try { event.preventDefault(); } catch(e) {}
                }, this._ns, this);

                // Focus any other visible modals when this one hides
                qtip._bind(tooltip, 'tooltiphide', function(event) {
                    if(event.target === tooltip[0]) {
                        $(MODALSELECTOR).filter(':visible').not(tooltip).last().qtip('focus', event);
                    }
                }, this._ns, this);
            },

            toggle: function(event, state, duration) {
                // Make sure default event hasn't been prevented
                if(event && event.isDefaultPrevented()) { return this; }

                // Toggle it
                OVERLAY.toggle(this.qtip, !!state, duration);
            },

            destroy: function() {
                // Remove modal class
                this.qtip.tooltip.removeClass(MODALCLASS);

                // Remove bound events
                this.qtip._unbind(this.qtip.tooltip, this._ns);

                // Delete element reference
                OVERLAY.toggle(this.qtip, FALSE);
                delete this.qtip.elements.overlay;
            }
        });


        MODAL = PLUGINS.modal = function(api) {
            return new Modal(api, api.options.show.modal);
        };

        // Setup sanitiztion rules
        MODAL.sanitize = function(opts) {
            if(opts.show) {
                if(typeof opts.show.modal !== 'object') { opts.show.modal = { on: !!opts.show.modal }; }
                else if(typeof opts.show.modal.on === 'undefined') { opts.show.modal.on = TRUE; }
            }
        };

        // Base z-index for all modal tooltips (use qTip core z-index as a base)
        QTIP.modal_zindex = QTIP.zindex - 200;

        // Plugin needs to be initialized on render
        MODAL.initialize = 'render';

        // Setup option set checks
        CHECKS.modal = {
            '^show.modal.(on|blur)$': function() {
                // Initialise
                this.destroy();
                this.init();

                // Show the modal if not visible already and tooltip is visible
                this.qtip.elems.overlay.toggle(
                    this.qtip.tooltip[0].offsetWidth > 0
                );
            }
        };

        // Extend original api defaults
        $.extend(TRUE, QTIP.defaults, {
            show: {
                modal: {
                    on: FALSE,
                    effect: TRUE,
                    blur: TRUE,
                    stealfocus: TRUE,
                    escape: TRUE
                }
            }
        });
        ;PLUGINS.viewport = function(api, position, posOptions, targetWidth, targetHeight, elemWidth, elemHeight)
        {
            var target = posOptions.target,
                tooltip = api.elements.tooltip,
                my = posOptions.my,
                at = posOptions.at,
                adjust = posOptions.adjust,
                method = adjust.method.split(' '),
                methodX = method[0],
                methodY = method[1] || method[0],
                viewport = posOptions.viewport,
                container = posOptions.container,
                cache = api.cache,
                adjusted = { left: 0, top: 0 },
                fixed, newMy, containerOffset, containerStatic,
                viewportWidth, viewportHeight, viewportScroll, viewportOffset;

            // If viewport is not a jQuery element, or it's the window/document, or no adjustment method is used... return
            if(!viewport.jquery || target[0] === window || target[0] === document.body || adjust.method === 'none') {
                return adjusted;
            }

            // Cach container details
            containerOffset = container.offset() || adjusted;
            containerStatic = container.css('position') === 'static';

            // Cache our viewport details
            fixed = tooltip.css('position') === 'fixed';
            viewportWidth = viewport[0] === window ? viewport.width() : viewport.outerWidth(FALSE);
            viewportHeight = viewport[0] === window ? viewport.height() : viewport.outerHeight(FALSE);
            viewportScroll = { left: fixed ? 0 : viewport.scrollLeft(), top: fixed ? 0 : viewport.scrollTop() };
            viewportOffset = viewport.offset() || adjusted;

            // Generic calculation method
            function calculate(side, otherSide, type, adjust, side1, side2, lengthName, targetLength, elemLength) {
                var initialPos = position[side1],
                    mySide = my[side],
                    atSide = at[side],
                    isShift = type === SHIFT,
                    myLength = mySide === side1 ? elemLength : mySide === side2 ? -elemLength : -elemLength / 2,
                    atLength = atSide === side1 ? targetLength : atSide === side2 ? -targetLength : -targetLength / 2,
                    sideOffset = viewportScroll[side1] + viewportOffset[side1] - (containerStatic ? 0 : containerOffset[side1]),
                    overflow1 = sideOffset - initialPos,
                    overflow2 = initialPos + elemLength - (lengthName === WIDTH ? viewportWidth : viewportHeight) - sideOffset,
                    offset = myLength - (my.precedance === side || mySide === my[otherSide] ? atLength : 0) - (atSide === CENTER ? targetLength / 2 : 0);

                // shift
                if(isShift) {
                    offset = (mySide === side1 ? 1 : -1) * myLength;

                    // Adjust position but keep it within viewport dimensions
                    position[side1] += overflow1 > 0 ? overflow1 : overflow2 > 0 ? -overflow2 : 0;
                    position[side1] = Math.max(
                        -containerOffset[side1] + viewportOffset[side1],
                        initialPos - offset,
                        Math.min(
                            Math.max(
                                -containerOffset[side1] + viewportOffset[side1] + (lengthName === WIDTH ? viewportWidth : viewportHeight),
                                initialPos + offset
                            ),
                            position[side1],

                            // Make sure we don't adjust complete off the element when using 'center'
                            mySide === 'center' ? initialPos - myLength : 1E9
                        )
                    );

                }

                // flip/flipinvert
                else {
                    // Update adjustment amount depending on if using flipinvert or flip
                    adjust *= (type === FLIPINVERT ? 2 : 0);

                    // Check for overflow on the left/top
                    if(overflow1 > 0 && (mySide !== side1 || overflow2 > 0)) {
                        position[side1] -= offset + adjust;
                        newMy.invert(side, side1);
                    }

                    // Check for overflow on the bottom/right
                    else if(overflow2 > 0 && (mySide !== side2 || overflow1 > 0)  ) {
                        position[side1] -= (mySide === CENTER ? -offset : offset) + adjust;
                        newMy.invert(side, side2);
                    }

                    // Make sure we haven't made things worse with the adjustment and reset if so
                    if(position[side1] < viewportScroll && -position[side1] > overflow2) {
                        position[side1] = initialPos; newMy = my.clone();
                    }
                }

                return position[side1] - initialPos;
            }

            // Set newMy if using flip or flipinvert methods
            if(methodX !== 'shift' || methodY !== 'shift') { newMy = my.clone(); }

            // Adjust position based onviewport and adjustment options
            adjusted = {
                left: methodX !== 'none' ? calculate( X, Y, methodX, adjust.x, LEFT, RIGHT, WIDTH, targetWidth, elemWidth ) : 0,
                top: methodY !== 'none' ? calculate( Y, X, methodY, adjust.y, TOP, BOTTOM, HEIGHT, targetHeight, elemHeight ) : 0,
                my: newMy
            };

            return adjusted;
        };
        ;PLUGINS.polys = {
            // POLY area coordinate calculator
            //	Special thanks to Ed Cradock for helping out with this.
            //	Uses a binary search algorithm to find suitable coordinates.
            polygon: function(baseCoords, corner) {
                var result = {
                        width: 0, height: 0,
                        position: {
                            top: 1e10, right: 0,
                            bottom: 0, left: 1e10
                        },
                        adjustable: FALSE
                    },
                    i = 0, next,
                    coords = [],
                    compareX = 1, compareY = 1,
                    realX = 0, realY = 0,
                    newWidth, newHeight;

                // First pass, sanitize coords and determine outer edges
                i = baseCoords.length; while(i--) {
                    next = [ parseInt(baseCoords[--i], 10), parseInt(baseCoords[i+1], 10) ];

                    if(next[0] > result.position.right){ result.position.right = next[0]; }
                    if(next[0] < result.position.left){ result.position.left = next[0]; }
                    if(next[1] > result.position.bottom){ result.position.bottom = next[1]; }
                    if(next[1] < result.position.top){ result.position.top = next[1]; }

                    coords.push(next);
                }

                // Calculate height and width from outer edges
                newWidth = result.width = Math.abs(result.position.right - result.position.left);
                newHeight = result.height = Math.abs(result.position.bottom - result.position.top);

                // If it's the center corner...
                if(corner.abbrev() === 'c') {
                    result.position = {
                        left: result.position.left + (result.width / 2),
                        top: result.position.top + (result.height / 2)
                    };
                }
                else {
                    // Second pass, use a binary search algorithm to locate most suitable coordinate
                    while(newWidth > 0 && newHeight > 0 && compareX > 0 && compareY > 0)
                    {
                        newWidth = Math.floor(newWidth / 2);
                        newHeight = Math.floor(newHeight / 2);

                        if(corner.x === LEFT){ compareX = newWidth; }
                        else if(corner.x === RIGHT){ compareX = result.width - newWidth; }
                        else{ compareX += Math.floor(newWidth / 2); }

                        if(corner.y === TOP){ compareY = newHeight; }
                        else if(corner.y === BOTTOM){ compareY = result.height - newHeight; }
                        else{ compareY += Math.floor(newHeight / 2); }

                        i = coords.length; while(i--)
                    {
                        if(coords.length < 2){ break; }

                        realX = coords[i][0] - result.position.left;
                        realY = coords[i][1] - result.position.top;

                        if((corner.x === LEFT && realX >= compareX) ||
                            (corner.x === RIGHT && realX <= compareX) ||
                            (corner.x === CENTER && (realX < compareX || realX > (result.width - compareX))) ||
                            (corner.y === TOP && realY >= compareY) ||
                            (corner.y === BOTTOM && realY <= compareY) ||
                            (corner.y === CENTER && (realY < compareY || realY > (result.height - compareY)))) {
                            coords.splice(i, 1);
                        }
                    }
                    }
                    result.position = { left: coords[0][0], top: coords[0][1] };
                }

                return result;
            },

            rect: function(ax, ay, bx, by) {
                return {
                    width: Math.abs(bx - ax),
                    height: Math.abs(by - ay),
                    position: {
                        left: Math.min(ax, bx),
                        top: Math.min(ay, by)
                    }
                };
            },

            _angles: {
                tc: 3 / 2, tr: 7 / 4, tl: 5 / 4,
                bc: 1 / 2, br: 1 / 4, bl: 3 / 4,
                rc: 2, lc: 1, c: 0
            },
            ellipse: function(cx, cy, rx, ry, corner) {
                var c = PLUGINS.polys._angles[ corner.abbrev() ],
                    rxc = c === 0 ? 0 : rx * Math.cos( c * Math.PI ),
                    rys = ry * Math.sin( c * Math.PI );

                return {
                    width: (rx * 2) - Math.abs(rxc),
                    height: (ry * 2) - Math.abs(rys),
                    position: {
                        left: cx + rxc,
                        top: cy + rys
                    },
                    adjustable: FALSE
                };
            },
            circle: function(cx, cy, r, corner) {
                return PLUGINS.polys.ellipse(cx, cy, r, r, corner);
            }
        };
        ;PLUGINS.svg = function(api, svg, corner)
        {
            var doc = $(document),
                elem = svg[0],
                root = $(elem.ownerSVGElement),
                ownerDocument = elem.ownerDocument,
                strokeWidth2 = (parseInt(svg.css('stroke-width'), 10) || 0) / 2,
                frameOffset, mtx, transformed, viewBox,
                len, next, i, points,
                result, position, dimensions;

            // Ascend the parentNode chain until we find an element with getBBox()
            while(!elem.getBBox) { elem = elem.parentNode; }
            if(!elem.getBBox || !elem.parentNode) { return FALSE; }

            // Determine which shape calculation to use
            switch(elem.nodeName) {
                case 'ellipse':
                case 'circle':
                    result = PLUGINS.polys.ellipse(
                        elem.cx.baseVal.value,
                        elem.cy.baseVal.value,
                        (elem.rx || elem.r).baseVal.value + strokeWidth2,
                        (elem.ry || elem.r).baseVal.value + strokeWidth2,
                        corner
                    );
                    break;

                case 'line':
                case 'polygon':
                case 'polyline':
                    // Determine points object (line has none, so mimic using array)
                    points = elem.points || [
                            { x: elem.x1.baseVal.value, y: elem.y1.baseVal.value },
                            { x: elem.x2.baseVal.value, y: elem.y2.baseVal.value }
                        ];

                    for(result = [], i = -1, len = points.numberOfItems || points.length; ++i < len;) {
                        next = points.getItem ? points.getItem(i) : points[i];
                        result.push.apply(result, [next.x, next.y]);
                    }

                    result = PLUGINS.polys.polygon(result, corner);
                    break;

                // Unknown shape or rectangle? Use bounding box
                default:
                    result = elem.getBBox();
                    result = {
                        width: result.width,
                        height: result.height,
                        position: {
                            left: result.x,
                            top: result.y
                        }
                    };
                    break;
            }

            // Shortcut assignments
            position = result.position;
            root = root[0];

            // Convert position into a pixel value
            if(root.createSVGPoint) {
                mtx = elem.getScreenCTM();
                points = root.createSVGPoint();

                points.x = position.left;
                points.y = position.top;
                transformed = points.matrixTransform( mtx );
                position.left = transformed.x;
                position.top = transformed.y;
            }

            // Check the element is not in a child document, and if so, adjust for frame elements offset
            if(ownerDocument !== document && api.position.target !== 'mouse') {
                frameOffset = $((ownerDocument.defaultView || ownerDocument.parentWindow).frameElement).offset();
                if(frameOffset) {
                    position.left += frameOffset.left;
                    position.top += frameOffset.top;
                }
            }

            // Adjust by scroll offset of owner document
            ownerDocument = $(ownerDocument);
            position.left += ownerDocument.scrollLeft();
            position.top += ownerDocument.scrollTop();

            return result;
        };
        ;PLUGINS.imagemap = function(api, area, corner, adjustMethod)
        {
            if(!area.jquery) { area = $(area); }

            var shape = (area.attr('shape') || 'rect').toLowerCase().replace('poly', 'polygon'),
                image = $('img[usemap="#'+area.parent('map').attr('name')+'"]'),
                coordsString = $.trim(area.attr('coords')),
                coordsArray = coordsString.replace(/,$/, '').split(','),
                imageOffset, coords, i, next, result, len;

            // If we can't find the image using the map...
            if(!image.length) { return FALSE; }

            // Pass coordinates string if polygon
            if(shape === 'polygon') {
                result = PLUGINS.polys.polygon(coordsArray, corner);
            }

            // Otherwise parse the coordinates and pass them as arguments
            else if(PLUGINS.polys[shape]) {
                for(i = -1, len = coordsArray.length, coords = []; ++i < len;) {
                    coords.push( parseInt(coordsArray[i], 10) );
                }

                result = PLUGINS.polys[shape].apply(
                    this, coords.concat(corner)
                );
            }

            // If no shapre calculation method was found, return false
            else { return FALSE; }

            // Make sure we account for padding and borders on the image
            imageOffset = image.offset();
            imageOffset.left += Math.ceil((image.outerWidth(FALSE) - image.width()) / 2);
            imageOffset.top += Math.ceil((image.outerHeight(FALSE) - image.height()) / 2);

            // Add image position to offset coordinates
            result.position.left += imageOffset.left;
            result.position.top += imageOffset.top;

            return result;
        };
        ;var IE6,

        /*
         * BGIFrame adaption (http://plugins.jquery.com/project/bgiframe)
         * Special thanks to Brandon Aaron
         */
            BGIFRAME = '<iframe class="qtip-bgiframe" frameborder="0" tabindex="-1" src="javascript:\'\';" ' +
                ' style="display:block; position:absolute; z-index:-1; filter:alpha(opacity=0); ' +
                '-ms-filter:"progid:DXImageTransform.Microsoft.Alpha(Opacity=0)";"></iframe>';

        function Ie6(api, qtip) {
            this._ns = 'ie6';
            this.init( (this.qtip = api) );
        }

        $.extend(Ie6.prototype, {
            _scroll : function() {
                var overlay = this.qtip.elements.overlay;
                overlay && (overlay[0].style.top = $(window).scrollTop() + 'px');
            },

            init: function(qtip) {
                var tooltip = qtip.tooltip,
                    scroll;

                // Create the BGIFrame element if needed
                if($('select, object').length < 1) {
                    this.bgiframe = qtip.elements.bgiframe = $(BGIFRAME).appendTo(tooltip);

                    // Update BGIFrame on tooltip move
                    qtip._bind(tooltip, 'tooltipmove', this.adjustBGIFrame, this._ns, this);
                }

                // redraw() container for width/height calculations
                this.redrawContainer = $('<div/>', { id: NAMESPACE+'-rcontainer' })
                    .appendTo(document.body);

                // Fixup modal plugin if present too
                if( qtip.elements.overlay && qtip.elements.overlay.addClass('qtipmodal-ie6fix') ) {
                    qtip._bind(window, ['scroll', 'resize'], this._scroll, this._ns, this);
                    qtip._bind(tooltip, ['tooltipshow'], this._scroll, this._ns, this);
                }

                // Set dimensions
                this.redraw();
            },

            adjustBGIFrame: function() {
                var tooltip = this.qtip.tooltip,
                    dimensions = {
                        height: tooltip.outerHeight(FALSE),
                        width: tooltip.outerWidth(FALSE)
                    },
                    plugin = this.qtip.plugins.tip,
                    tip = this.qtip.elements.tip,
                    tipAdjust, offset;

                // Adjust border offset
                offset = parseInt(tooltip.css('borderLeftWidth'), 10) || 0;
                offset = { left: -offset, top: -offset };

                // Adjust for tips plugin
                if(plugin && tip) {
                    tipAdjust = (plugin.corner.precedance === 'x') ? [WIDTH, LEFT] : [HEIGHT, TOP];
                    offset[ tipAdjust[1] ] -= tip[ tipAdjust[0] ]();
                }

                // Update bgiframe
                this.bgiframe.css(offset).css(dimensions);
            },

            // Max/min width simulator function
            redraw: function() {
                if(this.qtip.rendered < 1 || this.drawing) { return this; }

                var tooltip = this.qtip.tooltip,
                    style = this.qtip.options.style,
                    container = this.qtip.options.position.container,
                    perc, width, max, min;

                // Set drawing flag
                this.qtip.drawing = 1;

                // If tooltip has a set height/width, just set it... like a boss!
                if(style.height) { tooltip.css(HEIGHT, style.height); }
                if(style.width) { tooltip.css(WIDTH, style.width); }

                // Simulate max/min width if not set width present...
                else {
                    // Reset width and add fluid class
                    tooltip.css(WIDTH, '').appendTo(this.redrawContainer);

                    // Grab our tooltip width (add 1 if odd so we don't get wrapping problems.. huzzah!)
                    width = tooltip.width();
                    if(width % 2 < 1) { width += 1; }

                    // Grab our max/min properties
                    max = tooltip.css('maxWidth') || '';
                    min = tooltip.css('minWidth') || '';

                    // Parse into proper pixel values
                    perc = (max + min).indexOf('%') > -1 ? container.width() / 100 : 0;
                    max = ((max.indexOf('%') > -1 ? perc : 1) * parseInt(max, 10)) || width;
                    min = ((min.indexOf('%') > -1 ? perc : 1) * parseInt(min, 10)) || 0;

                    // Determine new dimension size based on max/min/current values
                    width = max + min ? Math.min(Math.max(width, min), max) : width;

                    // Set the newly calculated width and remvoe fluid class
                    tooltip.css(WIDTH, Math.round(width)).appendTo(container);
                }

                // Set drawing flag
                this.drawing = 0;

                return this;
            },

            destroy: function() {
                // Remove iframe
                this.bgiframe && this.bgiframe.remove();

                // Remove bound events
                this.qtip._unbind([window, this.qtip.tooltip], this._ns);
            }
        });

        IE6 = PLUGINS.ie6 = function(api) {
            // Proceed only if the browser is IE6
            return BROWSER.ie === 6 ? new Ie6(api) : FALSE;
        };

        IE6.initialize = 'render';

        CHECKS.ie6 = {
            '^content|style$': function() {
                this.redraw();
            }
        };
        ;}));
}( window, document ));