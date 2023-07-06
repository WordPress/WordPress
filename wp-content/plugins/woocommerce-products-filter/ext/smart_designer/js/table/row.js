'use strict';
import Helper from '../helper.js';
import Cell from './cell.js';
import Link from './ae/link.js';

//07-12-2022
export default class Row {
    constructor(table, data, id, wrapper, is_in_header = false, append = true) {

        if (data.length < 2) {
            return null;
        }

        this.table = table;
        this.wrapper = wrapper;
        this.data = data;
        this.is_in_header = is_in_header;
        this.append = append;
        this.id = id;
        this.cells = [];
        this.container = Helper.create_element('data-table-row', {'data-id': id});
        this.instance_key = 'woof-sd-table-row';//!! for attaching document events

        if (this.data[0]?.value?.conditions) {
            let conditions = this.data[0].value.conditions;

            //is this row can be in this current template           
            if (conditions?.templates) {
                if (!conditions.templates.includes(this.table.selected_template)) {
                    return null;
                }
            }

            //relatives
            for (const [k, v] of Object.entries(this.table.data.rows)) {

                if (conditions?.hide && k in conditions.hide && this.can_listen_conditions_of(k)) {
                    this.display(!(v[0].value.value.toString() === conditions.hide[k].toString()));
                }

                if (conditions?.forced_change && conditions.forced_change[k] && this.can_listen_conditions_of(k)) {
                    if (conditions.forced_change[k].value == v[0].value.value) {
                        this.data[0].backup = {};
                        this.data[0].backup.value = this.data[0].value.value;//!!
                        this.data[0].backup.measure = this.data[0].measure;//!!
                        this.data[0].value.value = conditions.forced_change[k].set_to;
                        this.data[0].measure = conditions.forced_change[k].measure;
                    }
                }
            }


            document.addEventListener('woof-sd-visor-value-changed', e => {

                let key = e.detail.key;

                if (conditions.hide) {
                    if (key in conditions.hide && this.can_listen_conditions_of(key)) {
                        this.display(!(e.detail.value.toString() === conditions.hide[key].toString()));
                    }
                }

                if (conditions.forced_change) {
                    if (key in conditions.forced_change && this.can_listen_conditions_of(key)) {
                        if (this.table.data.rows[key][0].value.value == conditions.forced_change[key].value) {
                            this.update_visor(conditions.forced_change[key].set_to, conditions.forced_change[key].measure);
                        } else {
                            this.update_visor(this.data[0].value.value, this.data[0].measure);
                        }
                    }
                }
            });
        }

        this.draw(this.table.data.header);
    }

    //if some elements not represented in the current template their influence should be excluded
    can_listen_conditions_of(key) {
        let can = true;

        if (this.table.data.rows[key][0].value?.conditions?.templates) {
            can = this.table.data.rows[key][0].value.conditions.templates.includes(this.table.selected_template);
        }

        return can;
    }

    draw() {
        if (this.data) {
            Object.values(this.data).forEach((data, index) => {
                if ('draw_content' in data) {
                    if (this.table.data?.header) {
                        //action buttons
                        this.cells[this.cells.length] = new Cell(this, {value: this[data.draw_content].bind(this), data: data}, 0, this.container, this.cells.length);
                    }
                } else {
                    this.cells[index] = new Cell(this, data, this.id, this.container, index);
                }
            });

            if (this.append) {
                this.wrapper.appendChild(this.container);
            } else {
                this.wrapper.querySelector('data-table-row').after(this.container);//after header
            }

        }
    }

    redraw(data) {
        if (data.length > 0) {
            data.forEach((value, index) => {
                this.redraw_cell(index, value);
            });
        }
    }

    redraw_cell(index, value) {
        this.cells[index].set_value(value, false);//no cast
    }

    delete() {
        this.container.remove();

        Helper.cast('woof-sd-data-table-row-delete', {
            id: this.id
        });

    }

    display(state) {
        if (Boolean(state)) {
            //show
            this.container.style.removeProperty('display');
            this.container.removeAttribute('hidden');
        } else {
            //hide
            this.container.style.display = 'none';
            this.container.setAttribute('hidden', '');
        }
    }

    //move to Helper or Sun
    update_visor(value, measure) {
        Helper.cast('woof-sd-set-visor-data', {
            key: this.id,
            value: value,
            measure: measure,
            before: this.data.before ? this.data.before : '',
            after: this.data.after ? this.data.after : ''
        });
    }

    //special function for table cell content
    draw_row_actions(wrapper) {
        (new Link(null, {
            title: '<span class="icon-edit"></span>',
            class: 'button-primary'
        }, wrapper)).setEvent('click', (ev, link) => Helper.cast('woof-sd-data-table-row-edit', {id: this.id, row: this}));

        (new Link(null, {
            title: '<span class="icon-trash"></span>',
            class: 'button-primary'
        }, wrapper)).setEvent('click', (ev, link) => {
            if (confirm(woof_sd.lang.sure)) {
                this.delete();
            }
        });
    }

}

