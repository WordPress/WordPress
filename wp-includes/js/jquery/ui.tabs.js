/*
 * Tabs 3 - New Wave Tabs
 *
 * Copyright (c) 2007 Klaus Hartl (stilbuero.de)
 * Dual licensed under the MIT (MIT-LICENSE.txt)
 * and GPL (GPL-LICENSE.txt) licenses.
 */

(function($) {

    // if the UI scope is not availalable, add it
    $.ui = $.ui || {};

    // tabs initialization
    $.fn.tabs = function(initial, options) {
        if (initial && initial.constructor == Object) { // shift arguments
            options = initial;
            initial = null;
        }
        options = options || {};

        initial = initial && initial.constructor == Number && --initial || 0;

        return this.each(function() {
            new $.ui.tabs(this, $.extend(options, { initial: initial }));
        });
    };

    // other chainable tabs methods
    $.each(['Add', 'Remove', 'Enable', 'Disable', 'Click', 'Load', 'Href'], function(i, method) {
        $.fn['tabs' + method] = function() {
            var args = arguments;
            return this.each(function() {
                var instance = $.ui.tabs.getInstance(this);
                instance[method.toLowerCase()].apply(instance, args);
            });
        };
    });
    $.fn.tabsSelected = function() {
        var selected = -1;
        if (this[0]) {
            var instance = $.ui.tabs.getInstance(this[0]),
                $lis = $('li', this);
            selected = $lis.index( $lis.filter('.' + instance.options.selectedClass)[0] );
        }
        return selected >= 0 ? ++selected : -1;
    };

    // tabs class
    $.ui.tabs = function(el, options) {

        this.source = el;

        this.options = $.extend({

            // basic setup
            initial: 0,
            event: 'click',
            disabled: [],
            cookie: null, // pass options object as expected by cookie plugin: { expires: 7, path: '/', domain: 'jquery.com', secure: true }
            // TODO bookmarkable: $.ajaxHistory ? true : false,
            unselected: false,
            unselect: options.unselected ? true : false,

            // Ajax
            spinner: 'Loading&#8230;',
            cache: false,
            idPrefix: 'ui-tabs-',
            ajaxOptions: {},

            // animations
            /*fxFade: null,
            fxSlide: null,
            fxShow: null,
            fxHide: null,*/
            fxSpeed: 'normal',
            /*fxShowSpeed: null,
            fxHideSpeed: null,*/

            // callbacks
            add: function() {},
            remove: function() {},
            enable: function() {},
            disable: function() {},
            click: function() {},
            hide: function() {},
            show: function() {},
            load: function() {},
            
            // templates
            tabTemplate: '<li><a href="#{href}"><span>#{text}</span></a></li>',
            panelTemplate: '<div></div>',

            // CSS classes
            navClass: 'ui-tabs-nav',
            selectedClass: 'ui-tabs-selected',
            unselectClass: 'ui-tabs-unselect',
            disabledClass: 'ui-tabs-disabled',
            panelClass: 'ui-tabs-panel',
            hideClass: 'ui-tabs-hide',
            loadingClass: 'ui-tabs-loading'

        }, options);

        this.options.event += '.ui-tabs'; // namespace event
        this.options.cookie = $.cookie && $.cookie.constructor == Function && this.options.cookie;

        // save instance for later
        $.data(el, $.ui.tabs.INSTANCE_KEY, this);
        
        // create tabs
        this.tabify(true);
    };

    // static
    $.ui.tabs.INSTANCE_KEY = 'ui_tabs_instance';
    $.ui.tabs.getInstance = function(el) {
        return $.data(el, $.ui.tabs.INSTANCE_KEY);
    };

    // instance methods
    $.extend($.ui.tabs.prototype, {
        tabId: function(a) {
            return a.title ? a.title.replace(/\s/g, '_')
                : this.options.idPrefix + $.data(a);
        },
        tabify: function(init) {

            this.$lis = $('li:has(a[href])', this.source);
            this.$tabs = this.$lis.map(function() { return $('a', this)[0] });
            this.$panels = $([]);
            
            var self = this, o = this.options;
            
            this.$tabs.each(function(i, a) {
                // inline tab
                if (a.hash && a.hash.replace('#', '')) { // Safari 2 reports '#' for an empty hash
                    self.$panels = self.$panels.add(a.hash);
                }
                // remote tab
                else if ($(a).attr('href') != '#') { // prevent loading the page itself if href is just "#"
                    $.data(a, 'href', a.href);
                    var id = self.tabId(a);
                    a.href = '#' + id;
                    self.$panels = self.$panels.add(
                        $('#' + id)[0] || $(o.panelTemplate).attr('id', id).addClass(o.panelClass)
                            .insertAfter( self.$panels[i - 1] || self.source )
                    );
                }
                // invalid tab href
                else {
                    o.disabled.push(i + 1);
                }
            });

            if (init) {

                // attach necessary classes for styling if not present
                $(this.source).hasClass(o.navClass) || $(this.source).addClass(o.navClass);
                this.$panels.each(function() {
                    var $this = $(this);
                    $this.hasClass(o.panelClass) || $this.addClass(o.panelClass);
                });
                
                // disabled tabs
                for (var i = 0, position; position = o.disabled[i]; i++) {
                    this.disable(position);
                }
                
                // Try to retrieve initial tab:
                // 1. from fragment identifier in url if present
                // 2. from cookie
                // 3. from selected class attribute on <li>
                // 4. otherwise use given initial argument
                // 5. check if tab is disabled
                this.$tabs.each(function(i, a) {
                    if (location.hash) {
                        if (a.hash == location.hash) {
                            o.initial = i;
                            // prevent page scroll to fragment
                            //if (($.browser.msie || $.browser.opera) && !o.remote) {
                            if ($.browser.msie || $.browser.opera) {
                                var $toShow = $(location.hash), toShowId = $toShow.attr('id');
                                $toShow.attr('id', '');
                                setTimeout(function() {
                                    $toShow.attr('id', toShowId); // restore id
                                }, 500);
                            }
                            scrollTo(0, 0);
                            return false; // break
                        }
                    } else if (o.cookie) {
                        o.initial = parseInt($.cookie( $.ui.tabs.INSTANCE_KEY + $.data(self.source) )) || 0;
                        return false; // break
                    } else if ( self.$lis.eq(i).hasClass(o.selectedClass) ) {
                        o.initial = i;
                        return false; // break
                    }
                });
                var n = this.$lis.length;
                while (this.$lis.eq(o.initial).hasClass(o.disabledClass) && n) {
                    o.initial = ++o.initial < this.$lis.length ? o.initial : 0;
                    n--;
                }
                if (!n) { // all tabs disabled, set option unselected to true
                    o.unselected = o.unselect = true;
                }

                // highlight selected tab
                this.$panels.addClass(o.hideClass);
                this.$lis.removeClass(o.selectedClass);
                if (!o.unselected) {
                    this.$panels.eq(o.initial).show().removeClass(o.hideClass); // use show and remove class to show in any case no matter how it has been hidden before
                    this.$lis.eq(o.initial).addClass(o.selectedClass);
                }

                // load if remote tab
                var href = !o.unselected && $.data(this.$tabs[o.initial], 'href');
                if (href) {
                    this.load(o.initial + 1, href);
                }
                
                // disable click if event is configured to something else
                if (!/^click/.test(o.event)) {
                    this.$tabs.bind('click', function(e) { e.preventDefault(); });
                }

            }

            // setup animations
            var showAnim = {}, showSpeed = o.fxShowSpeed || o.fxSpeed,
                hideAnim = {}, hideSpeed = o.fxHideSpeed || o.fxSpeed;
            if (o.fxSlide || o.fxFade) {
                if (o.fxSlide) {
                    showAnim['height'] = 'show';
                    hideAnim['height'] = 'hide';
                }
                if (o.fxFade) {
                    showAnim['opacity'] = 'show';
                    hideAnim['opacity'] = 'hide';
                }
            } else {
                if (o.fxShow) {
                    showAnim = o.fxShow;
                } else { // use some kind of animation to prevent browser scrolling to the tab
                    showAnim['min-width'] = 0; // avoid opacity, causes flicker in Firefox
                    showSpeed = 1; // as little as 1 is sufficient
                }
                if (o.fxHide) {
                    hideAnim = o.fxHide;
                } else { // use some kind of animation to prevent browser scrolling to the tab
                    hideAnim['min-width'] = 0; // avoid opacity, causes flicker in Firefox
                    hideSpeed = 1; // as little as 1 is sufficient
                }
            }

            // reset some styles to maintain print style sheets etc.
            var resetCSS = { display: '', overflow: '', height: '' };
            if (!$.browser.msie) { // not in IE to prevent ClearType font issue
                resetCSS['opacity'] = '';
            }

            // Hide a tab, animation prevents browser scrolling to fragment,
            // $show is optional.
            function hideTab(clicked, $hide, $show) {
                $hide.animate(hideAnim, hideSpeed, function() { //
                    $hide.addClass(o.hideClass).css(resetCSS); // maintain flexible height and accessibility in print etc.
                    if ($.browser.msie && hideAnim['opacity']) {
                        $hide[0].style.filter = '';
                    }
                    o.hide(clicked, $hide[0], $show && $show[0] || null);
                    if ($show) {
                        showTab(clicked, $show, $hide);
                    }
                });
            }

            // Show a tab, animation prevents browser scrolling to fragment,
            // $hide is optional
            function showTab(clicked, $show, $hide) {
                if (!(o.fxSlide || o.fxFade || o.fxShow)) {
                    $show.css('display', 'block'); // prevent occasionally occuring flicker in Firefox cause by gap between showing and hiding the tab panels
                }
                $show.animate(showAnim, showSpeed, function() {
                    $show.removeClass(o.hideClass).css(resetCSS); // maintain flexible height and accessibility in print etc.
                    if ($.browser.msie && showAnim['opacity']) {
                        $show[0].style.filter = '';
                    }
                    o.show(clicked, $show[0], $hide && $hide[0] || null);
                });
            }

            // switch a tab
            function switchTab(clicked, $li, $hide, $show) {
                /*if (o.bookmarkable && trueClick) { // add to history only if true click occured, not a triggered click
                    $.ajaxHistory.update(clicked.hash);
                }*/
                $li.addClass(o.selectedClass)
                    .siblings().removeClass(o.selectedClass);
                hideTab(clicked, $hide, $show);
            }

            // attach tab event handler, unbind to avoid duplicates from former tabifying...
            this.$tabs.unbind(o.event).bind(o.event, function() {

                //var trueClick = e.clientX; // add to history only if true click occured, not a triggered click
                var $li = $(this).parents('li:eq(0)'),
                    $hide = self.$panels.filter(':visible'),
                    $show = $(this.hash);

                // If tab is already selected and not unselectable or tab disabled or click callback returns false stop here.
                // Check if click handler returns false last so that it is not executed for a disabled tab!
                if (($li.hasClass(o.selectedClass) && !o.unselect) || $li.hasClass(o.disabledClass)
                    || o.click(this, $show[0], $hide[0]) === false) {
                    this.blur();
                    return false;
                }
                
                if (o.cookie) {
                    $.cookie($.ui.tabs.INSTANCE_KEY + $.data(self.source), self.$tabs.index(this), o.cookie);
                }
                    
                // if tab may be closed
                if (o.unselect) {
                    if ($li.hasClass(o.selectedClass)) {
                        $li.removeClass(o.selectedClass);
                        self.$panels.stop();
                        hideTab(this, $hide);
                        this.blur();
                        return false;
                    } else if (!$hide.length) {
                        self.$panels.stop();
                        if ($.data(this, 'href')) { // remote tab
                            var a = this;
                            self.load(self.$tabs.index(this) + 1, $.data(this, 'href'), function() {
                                $li.addClass(o.selectedClass).addClass(o.unselectClass);
                                showTab(a, $show);
                            });
                        } else {
                            $li.addClass(o.selectedClass).addClass(o.unselectClass);
                            showTab(this, $show);
                        }
                        this.blur();
                        return false;
                    }
                }

                // stop possibly running animations
                self.$panels.stop();

                // show new tab
                if ($show.length) {

                    // prevent scrollbar scrolling to 0 and than back in IE7, happens only if bookmarking/history is enabled
                    /*if ($.browser.msie && o.bookmarkable) {
                        var showId = this.hash.replace('#', '');
                        $show.attr('id', '');
                        setTimeout(function() {
                            $show.attr('id', showId); // restore id
                        }, 0);
                    }*/

                    if ($.data(this, 'href')) { // remote tab
                        var a = this;
                        self.load(self.$tabs.index(this) + 1, $.data(this, 'href'), function() {
                            switchTab(a, $li, $hide, $show);
                        });
                    } else {
                        switchTab(this, $li, $hide, $show);
                    }

                    // Set scrollbar to saved position - need to use timeout with 0 to prevent browser scroll to target of hash
                    /*var scrollX = window.pageXOffset || document.documentElement && document.documentElement.scrollLeft || document.body.scrollLeft || 0;
                    var scrollY = window.pageYOffset || document.documentElement && document.documentElement.scrollTop || document.body.scrollTop || 0;
                    setTimeout(function() {
                        scrollTo(scrollX, scrollY);
                    }, 0);*/

                } else {
                    throw 'jQuery UI Tabs: Mismatching fragment identifier.';
                }

                // Prevent IE from keeping other link focussed when using the back button
                // and remove dotted border from clicked link. This is controlled in modern
                // browsers via CSS, also blur removes focus from address bar in Firefox
                // which can become a usability and annoying problem with tabsRotate.
                if ($.browser.msie) {
                    this.blur(); 
                }

                //return o.bookmarkable && !!trueClick; // convert trueClick == undefined to Boolean required in IE
                return false;

            });

        },
        add: function(url, text, position) {
            if (url && text) {
                position = position || this.$tabs.length; // append by default  
                
                var o = this.options,
                    $li = $(o.tabTemplate.replace(/#\{href\}/, url).replace(/#\{text\}/, text));
                
                var id = url.indexOf('#') == 0 ? url.replace('#', '') : this.tabId( $('a:first-child', $li)[0] );
                
                // try to find an existing element before creating a new one
                var $panel = $('#' + id);
                $panel = $panel.length && $panel
                    || $(o.panelTemplate).attr('id', id).addClass(o.panelClass).addClass(o.hideClass);
                if (position >= this.$lis.length) {
                    $li.appendTo(this.source);
                    $panel.appendTo(this.source.parentNode);
                } else {
                    $li.insertBefore(this.$lis[position - 1]);
                    $panel.insertBefore(this.$panels[position - 1]);
                }
                
                this.tabify();
                
                if (this.$tabs.length == 1) {
                     $li.addClass(o.selectedClass);
                     $panel.removeClass(o.hideClass);
                     var href = $.data(this.$tabs[0], 'href');
                     if (href) {
                         this.load(position + 1, href);
                     }
                }
                o.add(this.$tabs[position], this.$panels[position]); // callback
            } else {
                throw 'jQuery UI Tabs: Not enough arguments to add tab.';
            }
        },
        remove: function(position) {
            if (position && position.constructor == Number) {                
                var o = this.options, $li = this.$lis.eq(position - 1).remove(),
                    $panel = this.$panels.eq(position - 1).remove();
                    
                // If selected tab was removed focus tab to the right or
                // tab to the left if last tab was removed.
                if ($li.hasClass(o.selectedClass) && this.$tabs.length > 1) {
                    this.click(position + (position < this.$tabs.length ? 1 : -1));
                }
                this.tabify();
                o.remove($li.end()[0], $panel[0]); // callback
            }
        },
        enable: function(position) {
            var o = this.options, $li = this.$lis.eq(position - 1);
            $li.removeClass(o.disabledClass);
            if ($.browser.safari) { // fix disappearing tab (that used opacity indicating disabling) after enabling in Safari 2...
                $li.css('display', 'inline-block');
                setTimeout(function() {
                    $li.css('display', 'block')
                }, 0)
            }
            o.enable(this.$tabs[position - 1], this.$panels[position - 1]); // callback
        },
        disable: function(position) {
            var o = this.options;      
            this.$lis.eq(position - 1).addClass(o.disabledClass);
            o.disable(this.$tabs[position - 1], this.$panels[position - 1]); // callback
        },
        click: function(position) {
            this.$tabs.eq(position - 1).trigger(this.options.event);
        },
        load: function(position, url, callback) {
            var self = this, o = this.options,
                $a = this.$tabs.eq(position - 1), a = $a[0], $span = $('span', a);
            
            // shift arguments
            if (url && url.constructor == Function) {
                callback = url;
                url = null;
            }

            // set new URL or get existing
            if (url) {
                $.data(a, 'href', url);
            } else {
                url = $.data(a, 'href');
            }

            // load
            if (o.spinner) {
                $.data(a, 'title', $span.html());
                $span.html('<em>' + o.spinner + '</em>');
            }
            var finish = function() {
                self.$tabs.filter('.' + o.loadingClass).each(function() {
                    $(this).removeClass(o.loadingClass);
                    if (o.spinner) {
                        $('span', this).html( $.data(this, 'title') );
                    }
                });
                self.xhr = null;
            };
            var ajaxOptions = $.extend(o.ajaxOptions, {
                url: url,
                success: function(r) {
                    $(a.hash).html(r);
                    finish();
                    // This callback is required because the switch has to take 
                    // place after loading has completed.
                    if (callback && callback.constructor == Function) {
                        callback();
                    }
                    if (o.cache) {
                        $.removeData(a, 'href'); // if loaded once do not load them again
                    }
                    o.load(self.$tabs[position - 1], self.$panels[position - 1]); // callback
                }
            });
            if (this.xhr) {
                // terminate pending requests from other tabs and restore title
                this.xhr.abort();
                finish();
            }
            $a.addClass(o.loadingClass);
            setTimeout(function() { // timeout is again required in IE, "wait" for id being restored
                self.xhr = $.ajax(ajaxOptions);
            }, 0);
            
        },
        href: function(position, href) {
            $.data(this.$tabs.eq(position - 1)[0], 'href', href);
        }
    });

})(jQuery);
