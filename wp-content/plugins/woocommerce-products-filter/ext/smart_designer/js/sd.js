'use strict';
import Helper from './helper.js';
import Table from './table/table.js';
import Visor from './visor.js';
import Panel from './panel.js';

//13-12-2022
export default class SD {
    constructor(data) {
        this.data = data;
        this.el_templates = {};
        this.selected_el_template = 0;//default
        this.demo_taxonomies = {};
        this.selected_demo_taxonomy = 0;//default
        this.selected_row_id = 0;
        this.selected_type = 'checkbox';//default        
        this.prepare();
        this.visor = null;//system content 1
        this.panel = null;//system content 2
        this.title_container = document.getElementById('woof-sd-title').querySelector('span');
        this.init();
        this.scene(0);
    }

    prepare() {
        this.wrapper = document.getElementsByClassName('sd')[0];
        this.out = document.getElementById('sd-scene');
        this.container = this.out.querySelector('div');
        this.out.appendChild(this.container);
    }

    init() {
        document.addEventListener('woof-sd-data-table-row-edit', e => {
            this.selected_row = e.detail.row;
            this.selected_row_id = e.detail.id;
            this.selected_type = this.get_row_data('type');
            this.reload_scene();
        });

        document.addEventListener('woof-sd-data-table-cell-data-changed', e => {
            this.save(e.detail);
        });

        document.addEventListener('woof-sd-data-table-row-delete', e => {
            this.delete(e.detail);
        });

        //declared here to avoid many inits in Class Visor
        document.addEventListener('woof-sd-change-visor', e => {
            //console.log('woof-sd-change-visor', e.detail);
            this.visor.draw(e.detail.value);
        });

        document.addEventListener('woof-sd-set-visor-data', e => {
            this.visor.set(e.detail.key, e.detail.value, e.detail.measure, e.detail.before, e.detail.after);
        });

        document.addEventListener('woof-sd-change-template', e => {
            this.change_template(e.detail.selected_el_template);
        });

        document.addEventListener('woof-sd-change-demo-taxonomy', e => {
            this.change_demo_taxonomy(e.detail.selected_el_template);
        });

        document.addEventListener('woof-sd-set-scene', e => {
            this.scene(e.detail.scene);
        });

        document.addEventListener('woof-sd-change-type', e => {
            this.change_type(e.detail.type);
        });

        document.addEventListener('woof-sd-create-new-row', e => {
            this.create_new_row(e.detail);
        });
    }

    scene(index, change_type = 0) {
        this.visor?.prepare();
        this.wrapper.dataset.scene = index;//!! for css styling
        this.container.innerHTML = '';

        switch (index) {
            case 0:
                this.selected_row_id = 0;
                this.table = new Table(this.data, this.container, 1, true);
                this.visor = null;
                this.title_container.innerHTML = '';
                this.set_panel(index);
                break;
            case 1:
                Helper.message(woof_sd.lang.loading + ' ...', 'warning', -1);
                this.title_container.innerHTML = ' [' + this.selected_row.container.querySelector('data-table-cell:first-child').innerText + ']';

                Helper.ajax('woof_sd_get_options', {
                    type: this.selected_type,
                    template: this.selected_el_template,
                    id: this.selected_row_id,
                    change_type: change_type ? 1 : 0
                }, data => {
                    if (data === -1) {
                        Helper.message(woof_sd.lang.error1, 'error', 7777);
                        Helper.cast('woof-sd-set-scene', {scene: 0});
                        return;
                    }

                    this.container.innerHTML = '';
                    Helper.message(woof_sd.lang.loaded, 'notice', 111);
                    this.selected_el_prefix = data.prefix;
                    this.el_templates = data.templates;
                    this.selected_el_template = parseInt(data.template);

                    this.demo_taxonomies = data.demo_taxonomies;
                    this.demo_taxonomies_terms = data.demo_taxonomies_terms;
                    this.selected_demo_taxonomy = data.selected_demo_taxonomy;
                    this.types = data.types;

                    if (Object.values(data.sections).length) {
                        for (let section of data.sections) {
                            let title = Helper.create_element('h3', {}, section.title);
                            this.container.appendChild(title);
                            new Table(section.table, this.container, this.selected_el_template);
                        }
                    }

                    this.set_panel(1);//!!must be here as its data loading here
                    this.visor = new Visor(this);

                    //color special words on admin panel for more readability
                    setTimeout(function () {
                        let cells = document.querySelectorAll('data-table-row data-table-cell:last-child');

                        if (cells.length) {
                            cells.forEach(function (cell) {
                                let text = cell.innerHTML;
                                text = text.replace(/color/g, '<span class="woof-sd-syntax-color">color</span>');
                                text = text.replace(/Color/g, '<span class="woof-sd-syntax-color">Color</span>');
                                text = text.replace(/background/g, '<span class="woof-sd-syntax-background">background</span>');
                                text = text.replace(/Background/g, '<span class="woof-sd-syntax-background">Background</span>');
                                text = text.replace(/Text/g, '<span class="woof-sd-syntax-text">Text</span>');
                                text = text.replace("Border", '<span class="woof-sd-syntax-brdr">Border</span>');
                                text = text.replace("border", '<span class="woof-sd-syntax-brdr">border</span>');

                                cell.innerHTML = text;
                            });
                        }

                    }, 777);

                });

                break;
    }
    }

    reload_scene(num = 1, change_type = false) {
        this.scene(num, change_type);
    }

    save(data) {
        //console.log('woof-sd-data-table-cell-data-changed', data);
        Helper.message(woof_sd.lang.saving, 'warning', -1);

        if (this.visor) {

            //if data.id is influencer to another fields maybe we should back fields data back from 'backup'
            let called_table = data.table;

            for (const [k, v] of Object.entries(called_table.data.rows)) {
                if (v[0]?.value?.conditions?.forced_change
                        && v[0]?.value?.conditions?.forced_change[data.id]
                        && v[0]?.value?.conditions?.forced_change[data.id].value != data.value) {
                    if (called_table.data.rows[k][0].backup) {
                        called_table.data.rows[k][0].measure = called_table.data.rows[k][0].backup.measure;
                        called_table.data.rows[k][0].value.value = called_table.data.rows[k][0].backup.value;

                        Helper.cast('woof-sd-redraw-cell', {
                            key: k
                        });
                    }
                }
            }

            //+++

            Helper.cast('woof-sd-visor-value-changed', {
                key: data.id,
                value: data.value
            });

            //+++

            let measure = '';
            if (data.measure) {
                measure = data.measure;
            }

            let before = '';
            if (data.before) {
                before = data.before;
            }

            let after = '';
            if (data.after) {
                after = data.after;
            }

            this.visor.set(data.id, data.value, measure, before, after);

            //+++

            if (this.fetch_timer_flag) {
                clearInterval(this.fetch_timer_flag);
            }

            //avoid server ajax ddos
            this.fetch_timer_flag = setTimeout(() => {
                if (this.fetch_controller) {
                    //cancel ajax request if user go through too quick
                    this.fetch_controller.abort();
                }

                this.fetch_controller = new AbortController();
                Helper.ajax('woof_sd_update_option', {
                    key: this.selected_el_prefix + data.id,
                    value: data.value,
                    id: this.selected_row_id
                }, data => {
                    Helper.message(woof_sd.lang.saved);
                }, false, null, this.fetch_controller.signal);

            }, 333);

        } else {
            if (data.action) {
                switch (data.action) {
                    case 'woof_sd_change_title':

                        Helper.ajax('woof_sd_change_title', {
                            id: data.id,
                            title: data.value
                        }, data => {
                            Helper.message(woof_sd.lang.saved);
                            this.redraw_html_types_selects(data.html_types);
                        });

                        break;
                }
            }
        }
    }

    delete(data) {
        Helper.ajax('woof_sd_delete_row', {
            id: data.id
        }, res => false, false);
    }

    set_panel(num) {
        this.panel = new Panel(num, this);
    }

    create_new_row(data) {
        this.data.rows[data.id] = this.table.createRow(
                [
                    {value: woof_sd.lang.new_el},
                    {value: 'checkbox'},
                    {value: '', draw_content: 'draw_row_actions', classes: 'woof-sd-edit-row'}
                ], data.id, false);

        this.redraw_html_types_selects(data.html_types);
    }

    get_row_data(field) {
        return this.data.rows[this.selected_row_id][field].value;
    }

    set_row_data(field, value) {
        this.data.rows[this.selected_row_id][field].value = value;
    }

    change_template(selected_el_template) {
        this.selected_el_template = selected_el_template;

        Helper.ajax('woof_sd_change_template', {
            template: this.selected_el_template,
            id: this.selected_row_id
        }, data => this.reload_scene());
    }

    change_demo_taxonomy(selected_demo_taxonomy) {
        this.selected_demo_taxonomy = selected_demo_taxonomy;

        Helper.ajax('woof_sd_change_demo_taxonomy', {
            taxonomy: this.selected_demo_taxonomy,
            id: this.selected_row_id
        }, terms => {
            this.demo_taxonomies_terms = terms;
            this.visor.redraw();
        });
    }

    change_type(type) {
        this.selected_type = type;
        this.set_row_data('type', this.selected_type);
        this.reload_scene(1, true);
    }

    //redraws drop-downs in WOOF tab 'Structure'
    redraw_html_types_selects(html_types) {
        let selects = document.querySelectorAll('select.woof_select_tax_type');

        for (let select of selects) {
            let selected_id = select.options[select.selectedIndex].value;
            select.innerHTML = '';
            for (const [id, title] of Object.entries(html_types)) {
                let option = Helper.create_element('option', {value: id}, title);
                select.appendChild(option);

                if (selected_id === id) {
                    option.setAttribute('selected', true);
                }
            }
        }

    }

}

