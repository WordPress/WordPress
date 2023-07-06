'use strict';
import Helper from './helper.js';
import Presets from './presets.js';
import Color from './color.js';
//17-12-2022
//SD satellite
export default class Panel {
    constructor(num, sd) {
        this.num = parseInt(num);
        this.sd = sd;
        this.el_types = [];
        this.panel = document.getElementById('sd-panel');
        this.panel.innerHTML = '';
        this.el_types = sd.types;
        this.draw();
    }

    draw() {
        this.panel.innerHTML = '';

        switch (this.num) {
            case 0:
                {
                    let lock = false;
                    let button = Helper.create_element('button', {
                        class: 'button woof-button-outline-secondary ',
                        style: 'text-transform: capitalize;'
                    }, woof_sd.lang.create_new_el, {
                        name: 'click',
                        callback: e => {

                            if (lock) {
                                return false;
                            }

                            lock = true;

                            if (woof_show_notes && this.sd.table.rows.length >= 1) {
                                if (confirm('Hi! In the free version of WOOF you can operate with 1 element! If you want to create more elements you can make upgrade to the premium version of the plugin. Would you like to visit the plugin page on Codecanyon?')) {
                                    window.location.href = 'https://products-filter.com/a/buy';
                                }

                                lock = false;
                                return false;
                            }

                            button.innerText = woof_sd.lang.creating + ' ...';

                            Helper.ajax('woof_sd_create_element', {
                                title: woof_sd.lang.new_el
                            }, data => {
                                Helper.cast('woof-sd-create-new-row', {id: data.id, html_types: data.html_types});
                                lock = false;
                                button.innerText = woof_sd.lang.create_new_el;
                            });
                        }
                    })

                    this.panel.appendChild(button);
                }
                break;
            case 1:
                {
                    {
                        let button = Helper.create_element('button', {
                            class: 'button woof-button-outline-secondary '
                        }, woof_sd.lang.back, {
                            name: 'click',
                            callback: e => {
                                Helper.cast('woof-sd-set-scene', {scene: 0});
                            }
                        });

                        this.append(button);
                    }

                    {
                        let button = Helper.create_element('button', {
                            class: 'button woof-button-outline-secondary '
                        }, woof_sd.lang.presets, {
                            name: 'click',
                            callback: e => new Presets(this)
                        });

                        this.append(button);
                    }

                    {
                        let select = Helper.create_html_select(this.el_types, this.sd.selected_type, {}, {
                            name: 'change',
                            callback: e => Helper.cast('woof-sd-change-type', {type: e.currentTarget.value})
                        })

                        this.append(select);
                    }

                    {
                        if (this.sd.selected_type === 'color') {
                            let button = Helper.create_element('button', {
                                class: 'button woof-button-outline-secondary' + ' '
                            }, woof_sd.lang.set_terms_color, {
                                name: 'click',
                                callback: e => {
                                    if (this.sd.selected_demo_taxonomy != '0') {
                                        new Color(this);
                                    } else {
                                        Helper.message(woof_sd.lang.assign_terms_color, 'warning', 7777);
                                    }
                                }
                            });

                            this.append(button);
                        }
                    }

                    {
                        if (Object.values(this.sd.el_templates).length > 1) {
                            let select = Helper.create_html_select(this.sd.el_templates, this.sd.selected_el_template, {}, {
                                name: 'change',
                                callback: e => Helper.cast('woof-sd-change-template', {selected_el_template: e.currentTarget.value})
                            })

                            this.append(select);
                        }
                    }

                    {
                        let select = Helper.create_html_select(this.sd.demo_taxonomies, this.sd.selected_demo_taxonomy, {}, {
                            name: 'change',
                            callback: e => Helper.cast('woof-sd-change-demo-taxonomy', {selected_el_template: e.currentTarget.value})
                        })

                        this.append(select);
                    }

                    {
                        let button = Helper.create_element('button', {
                            class: 'button woof-button-outline-secondary '
                        }, woof_sd.lang.reset, {
                            name: 'click',
                            callback: e => {
                                if (confirm(woof_sd.lang.sure)) {
                                    Helper.ajax('woof_sd_reset', {
                                        id: this.sd.selected_row_id
                                    }, data => Helper.cast('woof-sd-set-scene', {scene: 1}), false);
                                }
                            }
                        });

                        this.append(button);
                    }
                }
                break;
        }
    }

    append(item) {
        let wrapper = Helper.create_element('div');
        wrapper.appendChild(item);
        this.panel.appendChild(wrapper);

        if (item.tagName.toLowerCase() === 'select') {
            //jQuery(item).select2();
        }
    }
}

