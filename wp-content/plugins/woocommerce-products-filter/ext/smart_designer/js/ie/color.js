'use strict';
import IE from './ie.js';
import Helper from '../helper.js';
//22-11-2022
export default class Color extends IE {
    draw() {
        super.draw();

        Helper.ajax(null, {}, template => {
            let terms = this.visor.sd.demo_taxonomies_terms;
            let block = this.assemble_terms(terms, template);

            this.container.insertAdjacentHTML('afterbegin', block);
            if (this.container.querySelectorAll('input')) {
                this.container.querySelectorAll('input')[0].setAttribute('checked', true);//for visual test
            }

            this.container.className = `woof_list_color_sd_${this.template_num}`;
            this.wrapper.appendChild(this.container);
        }, false, this.template_url);

    }

    assemble_terms_hook(html, term) {
        if (term.color.length > 0) {
            html = html.replace(/__COLOR__/gi, term.color);
        } else {
            html = html.replace(/__COLOR__/gi, 'inherit');
        }
        
        html = html.replace(/__IMAGE__/gi, term.image);
        html = html.replace(/__TOOLTIP_TEXT__/gi, `<span class='woof-sd-tooltiptext'>${term.title} <b>(${term.count})</b></span>`);

        if (term.image.length > 4) {
            html = html.replace(/__CLASS_HAS_IMAGE__/gi, 'woof-sd-color-has-image');
        }

        return html;
    }
}
