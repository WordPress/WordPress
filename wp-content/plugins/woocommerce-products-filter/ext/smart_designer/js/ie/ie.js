'use strict';
//01-12-2022
export default class IE {
    constructor(visor, template_folder = '') {
        this.visor = visor;
        this.template_folder = template_folder;//for outer elements only!!
        this.wrapper = this.visor.wrapper;
        this.container = this.visor.container;
        this.template_num = this.visor.template_num;
    }

    draw() {
        this.container.innerHTML = '';

        this.template_url = woof_sd.url + `templates/${this.visor.selected_type}.php`;
        if (this.template_num > 0) {
            this.template_url = woof_sd.url + `templates/${this.visor.selected_type}-${this.template_num}.php`;
        }

        //for outer elements
        if (this.template_folder) {
            this.template_url = `${woof_sd.outer_elements_url}${this.template_folder}/templates/tpl.php`;

            if (this.template_num > 0) {
                this.template_url = `${woof_sd.outer_elements_url}${this.template_folder}/templates/tpl-${this.template_num}.php`;
            }
        }

        //must be overloaded using this method as parent (super)
    }

    assemble_terms(terms, template, level = 0) {
        let block = '';

        if (Object.values(terms).length > 0) {
            for (const [term_id, term] of Object.entries(terms)) {
                let html = template;
                let id = 'ie-' + Math.random().toString(36).substring(7);
                html = html.replace(/__ID__/gi, id);
                html = html.replace(/__TERM_ID__/gi, term_id);
                html = html.replace(/__CONTENT__/gi, term.title.trim());
                html = html.replace(/__COUNT__/gi, term.count);
                html = html.replace(/__OPENER__/gi, '');
                html = html.replace(/__RESET_RADIO_BTN__/gi, '');

                html = this.assemble_terms_hook(html, term);

                block += html;

                //+++

                if (term.childs && this.visor.sd.el_templates[this.visor.sd.selected_el_template].use_subterms) {
                    block += '<div class="woof-sd-ie-childs">' + this.assemble_terms(term.childs, template, level + 1) + '</div>';
                }
            }
        }

        return block;
    }

    //for overloading
    assemble_terms_hook(html, term) {
        return html;
    }
}
