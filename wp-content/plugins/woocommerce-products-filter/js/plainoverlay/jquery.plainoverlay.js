/*
 * jQuery.plainOverlay
 * https://anseki.github.io/jquery-plainoverlay/
 *
 * Copyright (c) anseki
 * Licensed under the MIT license.
 */

'use strict';
(function ($, undefined) {

    var APP_NAME = 'plainOverlay',
            APP_PREFIX = APP_NAME.toLowerCase(),
            EVENT_TYPE_SHOW = APP_PREFIX + 'show',
            EVENT_TYPE_HIDE = APP_PREFIX + 'hide',
            // builtin progress element
            newProgress = (function () {
                function experimental(props, supports, prefix, sep) { // similar to Compass
                    sep = sep === undefined ? ';' : sep;
                    return $.map(props, function (prop) {
                        return $.map(supports, function (support) {
                            return (prefix || '') + support + prop;
                        }).join(sep);
                    }).join(sep);
                }

                var isLegacy, domId = 'jQuery-' + APP_NAME,
                        supports = ['-webkit-', '-moz-', '-ms-', '-o-', ''], prefix = domId + '-progress',
                        cssText = '.' + prefix + '{' + experimental(['box-sizing:border-box'], ['-webkit-', '-moz-', '']) + ';width:100%;height:100%;border-top:3px solid #17f29b;' + experimental(['border-radius:50%'], supports) + ';-webkit-tap-highlight-color:rgba(0,0,0,0);transform:translateZ(0);box-shadow:0 0 1px rgba(0,0,0,0);' + experimental(['animation-name:' + domId + '-spin', 'animation-duration:1s', 'animation-timing-function:linear', 'animation-iteration-count:infinite'], supports) + '}' + experimental(['keyframes ' + domId + '-spin{from{' + experimental(['transform:rotate(0deg)'], supports) + '}to{' + experimental(['transform:rotate(360deg)'], supports) + '}}'], supports, '@', '') + '.' + prefix + '-legacy{width:100%;height:50%;padding-top:25%;text-align:center;white-space:nowrap;*zoom:1}.' + prefix + '-legacy:after,.' + prefix + '-legacy:before{content:" ";display:table}.' + prefix + '-legacy:after{clear:both}.' + prefix + '-legacy div{width:18%;height:100%;margin:0 1%;background-color:#17f29b;float:left;visibility:hidden}.' + prefix + '-1 div.' + prefix + '-1,.' + prefix + '-2 div.' + prefix + '-1,.' + prefix + '-2 div.' + prefix + '-2,.' + prefix + '-3 div.' + prefix + '-1,.' + prefix + '-3 div.' + prefix + '-2,.' + prefix + '-3 div.' + prefix + '-3{visibility:visible}',
                        adjustProgress = function () {
                            var progressWH = Math.min(300, // max w/h
                                    (this.isBody ?
                                            Math.min(this.jqWin.width(), this.jqWin.height()) :
                                            Math.min(this.jqTarget.innerWidth(), this.jqTarget.innerHeight())) * 0.9);
                            this.jqProgress.width(progressWH).height(progressWH);
                            if (!this.showProgress) { // CSS Animations
                                this.jqProgress.children('.' + prefix).css('borderTopWidth',
                                        Math.max(3, progressWH / 30)); // min width
                            }
                        },
                        showProgressLegacy = function (start) {
                            var that = this;
                            if (that.timer) {
                                clearTimeout(that.timer);
                            }
                            if (that.progressCnt) {
                                that.jqProgress.removeClass(prefix + '-' + that.progressCnt);
                            }
                            if (that.isShown) {
                                that.progressCnt = !start && that.progressCnt < 3 ? that.progressCnt + 1 : 0;
                                if (that.progressCnt) {
                                    that.jqProgress.addClass(prefix + '-' + that.progressCnt);
                                }
                                that.timer = setTimeout(function () {
                                    that.showProgress();
                                }, 500);
                            }
                        };

                return function (overlay) {
                    var jqProgress, sheet;

                    // Graceful Degradation
                    if (typeof isLegacy !== 'boolean') {
                        isLegacy = (function () { // similar to Modernizr
                            function contains(str, substr) {
                                return !!~('' + str).indexOf(substr);
                            }
                            var res, feature,
                                    modElem = document.createElement('modernizr'),
                                    mStyle = modElem.style,
                                    omPrefixes = 'Webkit Moz O ms',
                                    cssomPrefixes = omPrefixes.split(' '),
                                    tests = {},
                                    _hasOwnProperty = ({}).hasOwnProperty,
                                    hasOwnProp = _hasOwnProperty !== undefined &&
                                    _hasOwnProperty.call !== undefined ?
                                    function (object, property) {
                                        return _hasOwnProperty.call(object, property);
                                    } :
                                    function (object, property) {
                                        return (property in object) &&
                                                object.constructor.prototype[property] === undefined;
                                    };

                            function testProps(props) {
                                var i;
                                for (i in props) {
                                    if (!contains(props[i], '-') && mStyle[props[i]] !== undefined) {
                                        return true;
                                    }
                                }
                                return false;
                            }
                            function testPropsAll(prop) {
                                var ucProp = prop.charAt(0).toUpperCase() + prop.slice(1),
                                        props = (prop + ' ' + cssomPrefixes.join(ucProp + ' ') + ucProp).split(' ');
                                return testProps(props);
                            }

                            tests.borderradius = function () {
                                return testPropsAll('borderRadius');
                            };
                            tests.cssanimations = function () {
                                return testPropsAll('animationName');
                            };
                            tests.csstransforms = function () {
                                return !!testPropsAll('transform');
                            };

                            res = false;
                            for (feature in tests) {
                                if (hasOwnProp(tests, feature) && !tests[feature]()) {
                                    res = true;
                                    break;
                                }
                            }
                            mStyle.cssText = '';
                            modElem = null;
                            return res;
                        })();
                    }

                    if (!overlay.elmDoc.getElementById(domId)) { // Add style rules
                        if (overlay.elmDoc.createStyleSheet) { // IE
                            sheet = overlay.elmDoc.createStyleSheet();
                            sheet.owningElement.id = domId;
                            sheet.cssText = cssText;
                        } else {
                            sheet = (overlay.elmDoc.getElementsByTagName('head')[0] || overlay.elmDoc.documentElement)
                                    .appendChild(overlay.elmDoc.createElement('style'));
                            sheet.type = 'text/css';
                            sheet.id = domId;
                            sheet.textContent = cssText;
                        }
                    }

                    if (isLegacy) {
                        jqProgress = $('<div><div class="' + prefix + '-legacy">' +
                                '<div class="' + prefix + '-3" /><div class="' + prefix + '-2" /><div class="' + prefix + '-1" /><div class="' + prefix + '-2" /><div class="' + prefix + '-3" /></div></div>');
                        overlay.showProgress = showProgressLegacy;
                    } else {
                        jqProgress = $('<div><div class="' + prefix + '" /></div>');
                    }
                    overlay.adjustProgress = adjustProgress;
                    return jqProgress;
                };
            })();

    function Overlay(jqTarget, options, curObject) {
        var that = this, elmTarget = jqTarget.get(0);
        that.duration = options.duration;
        that.opacity = options.opacity;
        that.isShown = false;

        that.jqTargetOrg = jqTarget;
        if ((elmTarget != null && elmTarget === elmTarget.window) || elmTarget.nodeType === 9) { // window or document -> body
            that.jqTarget = $('body');
        } else if (elmTarget.nodeName.toLowerCase() === 'iframe' ||
                elmTarget.nodeName.toLowerCase() === 'frame') { // iframe or frame -> body of it
            // contentDocument not supported by IE
            that.jqWin = $(elmTarget.contentWindow);
            that.elmDoc = elmTarget.contentWindow.document;
            that.jqTarget = $('body', that.elmDoc);
            that.isFrame = true;
        } else {
            that.jqTarget = jqTarget;
        }
        that.jqWin = that.jqWin || $(window);
        that.elmDoc = that.elmDoc || document;
        that.isBody = that.jqTarget.get(0).nodeName.toLowerCase() === 'body';
        that.jqView = that.isBody ? that.jqWin : that.jqTarget;

        if (curObject) {
            // Remove jqProgress that exists always, because it may be replaced.
            if (curObject.jqProgress) {
                if (curObject.timer) {
                    clearTimeout(curObject.timer);
                }
                curObject.jqProgress.remove();
                delete curObject.jqProgress;
            }
            curObject.reset(true); // Restore styles
            curObject.jqOverlay.stop();
        }

        that.jqOverlay = (curObject && curObject.jqOverlay ||
                $('<div class="' + APP_PREFIX + '" />').css({
            position: that.isBody ? 'fixed' : 'absolute',
            left: 0,
            top: 0,
            display: 'none',
            cursor: 'wait'
        }).appendTo(that.jqTarget)
                .on('touchmove', function () {
                    return false;
                }) // avoid scroll on touch devices
                ).css({backgroundColor: options.fillColor, zIndex: options.zIndex});

        if (that.jqProgress = options.progress === false ? undefined :
                (typeof options.progress === 'function' ?
                        options.progress.call(that.jqTarget, options) : newProgress(that))) {
            that.jqProgress.css({
                position: that.isBody ? 'fixed' : 'absolute',
                display: 'none',
                zIndex: options.zIndex + 1,
                cursor: 'wait'
            }).appendTo(that.jqTarget)
                    .on('touchmove', function () {
                        return false;
                    }); // avoid scroll on touch devices;
        }

        // Not shared methods for calling per object in event of one element.
        that.callAdjust = (function (that) {
            return that.adjustProgress ? function () {
                that.adjustProgress();
                that.adjust();
            } : function () {
                that.adjust();
            };
        })(that);
        that.avoidFocus = (function (that) {
            return function (e) {
                $(that.elmDoc.activeElement).blur();
                e.preventDefault();
                return false;
            };
        })(that);
        that.avoidScroll = (function (that) {
            return function (e) {
                that.jqView.scrollLeft(that.scrLeft).scrollTop(that.scrTop);
                e.preventDefault();
                return false;
            };
        })(that);

        if (curObject) {
            if (curObject.timer) {
                clearTimeout(curObject.timer);
            }
            curObject = undefined; // Erase
        }
    }

    Overlay.prototype.show = function () {
        var that = this, inlineStyles, position, calMarginR, calMarginB, jqActive;
        that.reset(true); // Restore styles
        inlineStyles = that.jqTarget.get(0).style;

        that.orgPosition = inlineStyles.position;
        position = that.jqTarget.css('position');
        if (position !== 'relative' && position !== 'absolute' && position !== 'fixed') {
            that.jqTarget.css('position', 'relative');
        }

        that.orgOverflow = inlineStyles.overflow;
        calMarginR = that.jqTarget.prop('clientWidth');
        calMarginB = that.jqTarget.prop('clientHeight');
        that.jqTarget.css('overflow', 'hidden');
        calMarginR -= that.jqTarget.prop('clientWidth');
        calMarginB -= that.jqTarget.prop('clientHeight');
        that.addMarginR = that.addMarginB = 0;
        if (calMarginR < 0) {
            that.addMarginR = -calMarginR;
        }
        if (calMarginB < 0) {
            that.addMarginB = -calMarginB;
        }
        if (that.isBody) {
            if (that.addMarginR) {
                that.orgMarginR = inlineStyles.marginRight;
                that.jqTarget.css('marginRight', '+=' + that.addMarginR);
            }
            if (that.addMarginB) {
                that.orgMarginB = inlineStyles.marginBottom;
                that.jqTarget.css('marginBottom', '+=' + that.addMarginB);
            }
        } else { // change these in adjust()
            if (that.addMarginR) {
                that.orgMarginR = inlineStyles.paddingRight;
                that.orgWidth = inlineStyles.width;
            }
            if (that.addMarginB) {
                that.orgMarginB = inlineStyles.paddingBottom;
                that.orgHeight = inlineStyles.height;
            }
        }

        that.jqActive = undefined;
        jqActive = $(that.elmDoc.activeElement);
        if (that.isBody && !that.isFrame) {
            that.jqActive = jqActive.blur();
        } // Save activeElement
        else if (that.jqTarget.has(jqActive.get(0)).length) {
            jqActive.blur();
        }
        that.jqTarget.focusin(that.avoidFocus);
        that.scrLeft = that.jqView.scrollLeft();
        that.scrTop = that.jqView.scrollTop();
        that.jqView.scroll(that.avoidScroll);
        that.jqWin.resize(that.callAdjust);
        that.callAdjust();
        that.isShown = true;

        that.jqOverlay.stop().fadeTo(that.duration, that.opacity,
                function () {
                    that.jqTargetOrg.trigger(EVENT_TYPE_SHOW);
                });
        if (that.jqProgress) {
            if (that.showProgress) {
                that.showProgress(true);
            }
            that.jqProgress.stop().fadeIn(that.duration);
        }
    };

    Overlay.prototype.hide = function (ignoreComplete) {
        var that = this;

        function finish() {
            that.reset();
            that.jqTargetOrg.trigger(EVENT_TYPE_HIDE);
        }

        if (!that.isShown) {
            return;
        }
        if (ignoreComplete) {
            finish();
            that.jqOverlay.stop().fadeOut(that.duration);
        } else {
            that.jqOverlay.stop().fadeOut(that.duration, finish);
        }
        if (that.jqProgress) {
            that.jqProgress.stop().fadeOut(that.duration);
        }
    };

    Overlay.prototype.adjust = function () {
        var calW, calH;
        if (this.isBody) {
            // base of overlay size and progress position is window.
            calW = this.jqWin.width();
            calH = this.jqWin.height();
            this.jqOverlay.width(calW).height(calH);
            if (this.jqProgress) {
                this.jqProgress.css({
                    left: (calW - this.jqProgress.outerWidth()) / 2,
                    top: (calH - this.jqProgress.outerHeight()) / 2
                });
            }
        } else {
            if (this.addMarginR) {
                calW = this.jqTarget.css({paddingRight: this.orgMarginR, width: this.orgWidth})
                        .width(); // original size
                this.jqTarget.css('paddingRight', '+=' + this.addMarginR).width(calW - this.addMarginR);
            }
            if (this.addMarginB) {
                calH = this.jqTarget.css({paddingBottom: this.orgMarginB, height: this.orgHeight})
                        .height(); // original size
                this.jqTarget.css('paddingBottom', '+=' + this.addMarginB).height(calH - this.addMarginB);
            }

            // base of overlay size is element size that includes hidden area.
            calW = Math.max(this.jqTarget.prop('scrollWidth'), this.jqTarget.innerWidth()); // for IE bug
            calH = Math.max(this.jqTarget.prop('scrollHeight'), this.jqTarget.innerHeight());
            this.jqOverlay.width(calW).height(calH);
            if (this.jqProgress) {
                // base of progress position is element size that doesn't include hidden area.
                calW = this.jqTarget.innerWidth();
                calH = this.jqTarget.innerHeight();
                this.jqProgress.css({
                    left: (calW - this.jqProgress.outerWidth()) / 2 + this.scrLeft,
                    top: (calH - this.jqProgress.outerHeight()) / 2 + this.scrTop
                });
            }
        }
    };

    Overlay.prototype.reset = function (forceHide) {
        // default: display of jqOverlay and jqProgress is kept
        if (forceHide) {
            this.jqOverlay.css('display', 'none');
            if (this.jqProgress) {
                this.jqProgress.css('display', 'none');
            }
        }
        if (!this.isShown) {
            return;
        }
        this.jqTarget.css({position: this.orgPosition, overflow: this.orgOverflow});
        if (this.isBody) {
            if (this.addMarginR) {
                this.jqTarget.css('marginRight', this.orgMarginR);
            }
            if (this.addMarginB) {
                this.jqTarget.css('marginBottom', this.orgMarginB);
            }
        } else {
            if (this.addMarginR) {
                this.jqTarget.css({paddingRight: this.orgMarginR, width: this.orgWidth});
            }
            if (this.addMarginB) {
                this.jqTarget.css({paddingBottom: this.orgMarginB, height: this.orgHeight});
            }
        }
        this.jqTarget.off('focusin', this.avoidFocus);
        if (this.jqActive && this.jqActive.length) {
            this.jqActive.focus();
        } // Restore activeElement
        this.jqView.off('scroll', this.avoidScroll).scrollLeft(this.scrLeft).scrollTop(this.scrTop);
        this.jqWin.off('resize', this.callAdjust);
        this.isShown = false;
    };

    Overlay.prototype.scroll = function (scrollLen, top) {
        var dir = top ? 'Top' : 'Left';
        this.jqView['scroll' + dir]((this['scr' + dir] = scrollLen));
        this['scr' + dir] = this.jqView['scroll' + dir](); // Get real value.
    };

    function init(jq, options) {
        var opt = $.extend({
            duration: 200,
            opacity: 0.6,
            zIndex: 9000
                    // Optional: progress, show, hide
        }, options);
        opt.fillColor = opt.fillColor || opt.color /* alias */ || '#888';
        return jq.each(function () {
            var that = $(this);
            that.data(APP_NAME, new Overlay(that, opt, that.data(APP_NAME)));
            if (typeof opt.show === 'function')
            {
                that.off(EVENT_TYPE_SHOW, opt.show).on(EVENT_TYPE_SHOW, opt.show);
            }
            if (typeof opt.hide === 'function')
            {
                that.off(EVENT_TYPE_HIDE, opt.hide).on(EVENT_TYPE_HIDE, opt.hide);
            }
        });
    }

    function overlayShow(jq, options) {
        return jq.each(function () {
            var that = $(this), overlay;
            if (options || !(overlay = that.data(APP_NAME))) {
                overlay = init(that, options).data(APP_NAME);
            }
            overlay.show();
        });
    }

    function overlayHide(jq, ignoreComplete) {
        return jq.each(function () {
            var overlay = $(this).data(APP_NAME);
            if (overlay) {
                overlay.hide(ignoreComplete);
            }
        });
    }

    function overlayScroll(jq, scrollLen, top) {
        return jq.each(function () {
            var overlay = $(this).data(APP_NAME);
            if (overlay) {
                overlay.scroll(scrollLen, top);
            }
        });
    }

    function overlaySetOption(jq, name, newValue) {
        var jqTarget = jq.length ? jq.eq(0) : undefined, // only 1st
                overlay;
        if (!jqTarget) {
            return;
        }
        overlay = jqTarget.data(APP_NAME) || init(jqTarget).data(APP_NAME);
        if (!overlay.hasOwnProperty(name)) {
            return;
        }
        /* jshint eqnull:true */
        if (newValue != null) {
            overlay[name] = newValue;
        }
        /* jshint eqnull:false */
        return overlay[name];
    }

    $.fn[APP_NAME] = function (action, arg1, arg2) {
        return (
                action === 'show' ? overlayShow(this, arg1) :
                action === 'hide' ? overlayHide(this, arg1) :
                action === 'scrollLeft' ? overlayScroll(this, arg1) :
                action === 'scrollTop' ? overlayScroll(this, arg1, true) :
                action === 'option' ? overlaySetOption(this, arg1, arg2) :
                init(this, action)); // action = options.
    };

})(jQuery);
