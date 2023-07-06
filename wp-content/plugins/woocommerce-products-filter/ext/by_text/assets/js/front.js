'use strict';

function woof_init_text(){
    (function ($) {
        let sparams = (new URL(window.location.href)).searchParams;

        let data = {
           // s: typeof sparams.get('woof_text') !== 'undefined' ? sparams.get('woof_text') : ''
        };

        data = {...data, ...woof_husky_txt.default_data};
        delete data.page;//fix to avoid pagination breaking

        [].forEach.call($.querySelectorAll('input.woof_husky_txt-input'), function (input) {
	    var txt = jQuery(input).val();
	    data.s = txt;
            new Husky(input, data);
        });

        //init default wp search as Husky - to options - TODO
        if (false) {
            if ($.querySelectorAll('form[role=search] input[type=search]').length) {

                [].forEach.call($.querySelectorAll('form[role=search] input[type=search]'), function (input) {

                    if (input.classList.contains('husky-input')) {
                        return;//already defined
                    }

                    if (input.closest('form[role=search]').querySelector('input[type=submit]')) {
                        input.closest('form[role=search]').querySelector('input[type=submit]').remove();
                    }

                    var clone = input.cloneNode(true);//trick - reset theme actions
                    input.insertAdjacentElement('afterend', clone);
                    input.remove();

                    new Husky(clone, data);
                });

            }
        }
    })(document);
}