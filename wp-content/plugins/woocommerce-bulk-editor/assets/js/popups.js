"use strict";

function init_woobe_popups() {
    (function ($) {

        $.woobe_popup_prepare = function (el, options) {
            this.el = el;
            this.options = $.extend({}, $.woobe_popup_prepare.DEFAULTS, options);
            this.init();
        };

        $.woobe_popup_prepare.DEFAULTS = {};
        $.woobe_popup_prepare.openInstance = [];

        $.woobe_popup_prepare.prototype = {
            init: function () {

                $.woobe_popup_prepare.openInstance.unshift(this);

                var base = this;
                base.scope = false;
                base.body = $('body');
                base.wrap = $('#wpwrap');
                base.modal = $('<div class="woobe-modal woobe-style"></div>');
                base.overlay = $('<div class="woobe-modal-backdrop"></div>');
                base.container = $('td');
                base.instance = $.woobe_popup_prepare.openInstance.length;
                base.namespace = '.popup_modal_' + base.instance;
                base.terms_ids = [];
                base.product_id = 0;

                base.support = {
                    touch: Modernizr.touch
                };
                base.eventtype = base.support.touch ? 'touchstart' : 'click';
                base.loadPopup();
            },
            loadPopup: function () {
                this.container.on(this.eventtype, this.el, $.proxy(function (e) {
                    if (!this.scope) {
                        this.body.addClass('woobe-noscroll');
                        this.openPopup(e);
                    }
                    this.scope = true;
                }, this));
            },
            openPopup: function (e) {
                e.preventDefault();
                var base = this
                var el = $(e.target);

                var data = el.data();


                //***

                var key = data['key'],
                        name = data['name'] + ' [' + data['key'] + ']',
                        type = false,
                        info = $("#woobe-modal-content-" + key);
                var content = info.html();
                base.create_html(key, name, content, info, type);
                base.add_behavior(key, name, content, info, type);
            },
            create_html: function (key, name, content, info, type) {

                var base = this,
                        title = name ? '<h3 class="woobe-modal-title"> ' + name + '</h3>' : '',
                        loading = ' preloading ',
                        output = '<div class="woobe-modal-inner">';
                output += '<div class="woobe-modal-inner-header">' + title + '<a href="javascript:void(0)" class="woobe-modal-close"></a></div>';
                output += '<div class="woobe-modal-inner-content ' + loading + '">' + content + '</div>';
                output += '<div class="woobe-modal-inner-footer">';
                output += '<a href="javascript:void(0)" class="woobe-modal-close1 button button-primary button-large button-large-2">' + lang.cancel + '</a>';
                output += '<a href="javascript:void(0)" class="woobe-modal-save button button-primary button-large button-large-1">' + lang.apply + '</a>';
                output += '</div>';
                output += '</div>';

                base.wrap.append(base.modal).append(base.overlay);
                base.modal.html(output);
                base.modal.find('.woobe-modal-inner-content').removeClass('preloading');

                //***

                var multiplier = base.instance - 1,
                        old = parseInt(base.modal.css('zIndex'), 10);
                base.modal.css({margin: (30 * multiplier), zIndex: (old + multiplier + 1)});
                base.overlay.css({zIndex: (old + multiplier)});

                base.on_load_callback(key, name, content, info, type);
            },
            closeModal: function () {
                var base = this;

                $.woobe_popup_prepare.openInstance.shift();

                base.modal.remove();
                base.overlay.remove();

                base.body.removeClass('woobe-noscroll');
                base.scope = false;
            },
            add_behavior: function (key, name, content, info, type) {
                var base = this;

                base.modal.on(base.eventtype + base.namespace, '.woobe-modal-save', function (e) {
                    e.preventDefault();
                    base.on_close_callback(key, name, content, info, type);
                    base.closeModal();
                });

                base.modal.on(base.eventtype + base.namespace, '.woobe-modal-close', function (e) {
                    e.preventDefault();
                    base.closeModal();
                });

                base.overlay.on(base.eventtype + base.namespace, function (e) {
                    e.preventDefault();
                    base.closeModal();
                });

            },
            on_load_callback: function (key, name, content, info, type) {

            },
            on_close_callback: function (key, name, content, info, type) {
                //***
            }
        };

    })(jQuery);
}
