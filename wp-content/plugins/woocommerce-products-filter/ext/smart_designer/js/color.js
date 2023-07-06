'use strict';
import Helper from './helper.js';
import Popup23 from './lib/popup-23.js';
import Table from './table/table.js';
import ColorNavigation from './color-navigation.js';

//16-12-2022
export default class Color {
    constructor(panel) {
        this.panel = panel;
        this.popup = new Popup23({right: 5, left: 5, top: 10, bottom: 10, title: woof_sd.lang.terms_color}, null, false);
        this.popup.set_content(woof_sd.lang.loading + ' ...');
        this.navigation = new ColorNavigation(this);
        this.terms = null;
        this.instance_key = 'woof-sd-color';//!! for attaching document events
        this.load();

        Helper.addSingleEventListener('woof_sd_change_term_color', this, e => {
            this.save('woof_sd_change_term_color', parseInt(e.detail.id), e.detail.value);
        });

        Helper.addSingleEventListener('woof_sd_change_term_color_image', this, e => {
            this.save('woof_sd_change_term_color_image', parseInt(e.detail.id), e.detail.value);
        });

        Helper.addSingleEventListener('woof_sd_childs_term_color', this, e => this.reload(parseInt(e.detail.id)));
    }

    save(action, term_id, value) {

        if (this.fetch_timer_flag) {
            clearInterval(this.fetch_timer_flag);
        }

        if (this.fetch_controller) {
            //cancel ajax request if user go through too quick
            this.fetch_controller.abort();
        }

        this.fetch_controller = new AbortController();

        this.fetch_timer_flag = setTimeout(() => {
            Helper.ajax(action, {
                term_id: parseInt(term_id),
                value: value
            }, data => this.redraw_visor_terms(), false, null, this.fetch_controller.signal);
        }, 777);
    }

    reload(term_id) {
        if (this.terms[term_id]?.title?.value) {
            this.navigation.selected_term = {
                title: this.terms[term_id]?.title?.value.value,
                id: parseInt(this.terms[term_id]?.title?.value['data-id'])
            };
        }

        this.popup.set_content(woof_sd.lang.loading + ' ...');

        Helper.ajax('woof_sd_load_color_terms', {
            term_id: term_id,
            taxonomy: this.panel.sd.selected_demo_taxonomy
        }, data => this.draw_table(data));
    }

    load() {
        Helper.ajax('woof_sd_load_color_terms', {
            taxonomy: this.panel.sd.selected_demo_taxonomy
        }, data => this.draw_table(data));
    }

    draw_table(data) {
        this.popup.set_content('');
        this.terms = data.rows;
        this.navigation.draw();

        if (data.rows) {
            this.table = new Table({...data}, this.popup.container);
        } else {
            this.popup.set_content(woof_sd.lang.no_items);
        }
    }

    redraw_visor_terms() {
        this.panel.sd.change_demo_taxonomy(this.panel.sd.selected_demo_taxonomy);
    }
}

