'use strict';
import Helper from '../helper.js';
import Table from './table.js';
import Input from './ae/input.js';
import Checkbox from './ae/checkbox.js';
import Switcher from './ae/switcher.js';
import Color from './ae/color.js';
import Image from './ae/image.js';
import Ranger23 from './ae/ranger.js';
import Select from './ae/select.js';
import Link from './ae/link.js';

//30-11-2022
export default class Cell {
    constructor(row, data, id, wrapper, index) {
        this.row = row;
        this.data = data;
        this.id = id;
        this.wrapper = wrapper;
        this.index = index;
        this.columns_count = this.row.data.length;
        this.header_data = this.row.table.data?.header;
        this.is_in_header = this.row.is_in_header;
        this.instance_key = 'woof-sd-table-cell';//!! for attaching document events
        this.draw();

        if (index === 0) {
            Helper.addSingleEventListener('woof-sd-redraw-cell', this, e => {
                if (e.detail.key === this.id) {
                    this.data.measure = this.row.table.data.rows[this.id][0].measure;
                    this.data.value = this.row.table.data.rows[this.id][0].value;
                    this.redraw();
                }
            });
        }
    }

    redraw() {
        this.container.innerHTML = '';
        this.draw_elements();
    }

    draw() {
        this.container = Helper.create_element('data-table-cell');
        let header_data = this.header_data;

        if (!this.data.role) {
            this.data.role = 'cell';
        }

        if (!this.id) {
            this.id = 0;
        }

        //+++

        if (!this.is_in_header && header_data && header_data[this.index]) {
            if (header_data[this.index].width) {
                this.data.width = header_data[this.index].width;
            }

            if (header_data[this.index].action) {
                this.data.action = header_data[this.index].action;
            }

            if (header_data[this.index].key) {
                this.data.key = header_data[this.index].key;
            }

            if (header_data[this.index].classes) {
                this.container.className = header_data[this.index].classes;
            }

            if (header_data[this.index].editable) {
                this.editable = true;
                this.container.classList.add('text-cell-editable');
            }

            if (this.data && this.data.classes) {
                this.container.classList.add(this.data.classes);
            }

            //for data transfered by with action
            if (this.data.data) {
                if (this.data.data.classes) {
                    this.container.classList.add(this.data.data.classes);
                }
            }

        }

        if (!this.data.width) {
            this.data.width = parseFloat(100 / this.columns_count) + '%';
        }

        if (this.data.css_classes) {
            for (let val of this.data.css_classes) {
                this.container.classList.add(val);
            }
        }


        this.container.style.setProperty('--width', this.data.width);

        this.draw_elements();

        this.wrapper.appendChild(this.container);

        if (!this.is_in_header) {
            this.events();
        }
    }

    draw_elements() {
        switch (typeof this.data.value) {
            case 'function':
                this.data.value(this.container);//custom items drawned by callback
                break;

            case 'object':
                this.draw_active_element(this.data.value.element, this.data.value);//active element
                if (this.id) {
                    setTimeout(() => {
                        //!setTimeout is important because visor should be loaded, another way is null
                        Helper.cast('woof-sd-set-visor-data', {
                            key: this.id,
                            value: this.data.value.value,
                            measure: this.data.measure ? this.data.measure : '',
                            before: this.data.before ? this.data.before : '',
                            after: this.data.after ? this.data.after : ''
                        });
                    }, 333);
                }
                break;

            default:
                let content = this.data.value;
                if (this.data.help) {
                    let link = Helper.create_element('a', {
                        href: this.data.help,
                        target: '_blank',
                        class: 'data-table-cell-help'
                    }, Helper.create_element('img', {
                        src: woof_sd.url + 'img/info.svg',
                        alt: ''
                    }));

                    content += link.outerHTML;
                }
                this.draw_content(content);//simple string
                break;
        }
    }

    events() {
        if (this.editable) {
            this.container.addEventListener('click', e => {
                if (e.target === this.container) {
                    this.draw_content('');
                    this.draw_active_element('input');
                }
            });
        }
    }

    draw_content(value) {
        this.container.innerHTML = value;
    }

    set_value(value, cast = true) {

        let prev_value = null;

        if (typeof value !== 'undefined'/* !! */) {
            if (typeof this.data.value === 'object') {
                prev_value = this.data.value.value;
                this.data.value.value = value;
            } else {
                prev_value = this.data.value;
                this.data.value = value;
            }
        }

        if (cast) {

            Helper.cast(this.data.value.action ? this.data.value.action : 'woof-sd-data-table-cell-data-changed', {
                action: this.data.value.action ? this.data.value.action : this.data.action,
                value: value,
                prev_value: prev_value,
                id: this.id,
                key: this.data.key,
                index: this.index,
                measure: this.data.measure ? this.data.measure : '',
                before: this.data.before ? this.data.before : '',
                after: this.data.after ? this.data.after : '',
                table: this.row.table
            });

    }


    }

    draw_active_element(element_type, data = null) {

        this.container.innerHTML = '';
        let value = this.data.value;

        if (typeof value === 'object') {
            value = value.value;
        }

        switch (element_type) {
            case 'input':
                {
                    let input = new Input(this.id, value, this, {type: data?.type});

                    input.setEvent('keyup', (e, input) => {
                        e.preventDefault();
                        e.stopPropagation();
                        e.stopImmediatePropagation();

                        //enter
                        if (e.keyCode === 13) {
                            this.set_value(input.value);
                            if (Boolean(this.header_data[this.index].editable)) {
                                this.draw_content(input.value);
                            }
                        }

                        //escape
                        if (e.keyCode === 27) {
                            if (Boolean(this.header_data[this.index].editable)) {
                                this.draw_content(this.data.value);
                            } else {
                                input.value = this.data.value;
                            }
                        }

                        return false;
                    });
                }
                break;

            case 'text':
                {
                    let input = new Input(this.id, value, this, {type: 'text'});
                    input.setEvent('change', (e, input) => {
                        this.set_value(input.value);
                    });
                }
                break;

            case 'number':
                {
                    let input = new Input(this.id, value, this, {type: 'number'});
                    input.setEvent('input', (e, input) => {
                        this.set_value(input.value);
                    });
                }
                break;

            case 'checkbox':

                (new Checkbox(this.id, value, this)).setEvent('click', (e, input) => {
                    this.set_value(input.checked ? 1 : 0);
                });

                break;

            case 'switcher':
                let yes = 1;
                let no = 0;
                let action = '';
                let type = '';
                let value_is_special = false;

                if ('yes' in this.data.value) {
                    yes = this.data.value.yes;
                    value_is_special = true;
                }

                if ('no' in this.data.value) {
                    no = this.data.value.no;
                }

                if ('action' in this.data.value) {
                    action = this.data.value.action;
                }

                if ('type' in this.data.value) {
                    type = this.data.value.type;
                }

                if (!value_is_special) {
                    value = parseInt(value);
                } else {
                    if (value === yes) {
                        value = 1;
                    } else {
                        value = 0;
                    }
                }

                (new Switcher(this.id, value, this, type, action)).setEvent('click', (e, input) => {
                    this.set_value(input.checked ? yes : no);
                });

                break;


            case 'color':

                (new Color(this.id, value, this)).setEvent('input', (e, input) => {
                    this.set_value(input.value);
                });

                break;

            case 'ranger':
                {
                    let el = Helper.create_element('div', {class: 'ranger23-track woof-sd-sd-slider'});

                    for (const [kk, vv] of Object.entries({
                        //'data-key': key,
                        'data-min': this.data.value.min,
                        'data-max': this.data.value.max,
                        'data-selected-min': this.data.value.min,
                        'data-selected-max': value
                    })) {
                        el.setAttribute(kk, vv);
                    }

                    let slider = new Ranger23(el, null, 30, {
                        instant_cast: true,
                        disable_handler_left: true
                    });

                    slider.draw_inputs(this.container);//num inputs

                    let slider_timer = null;
                    slider.onSelect = () => {
                        if (slider_timer) {
                            clearTimeout(slider_timer);
                        }

                        this.set_value(slider.value[1]);
                    }

                    this.container.appendChild(el);
                }
                break;

            case 'select':

                if (Object.values(this.data.value.options).length > 0) {

                    let select = new Select(this.id, value, this, this.data.value);
                    select.setEvent('change', (e, input) => {
                        this.set_value(input.value);
                    });

                } else {
                    console.log('Select should has options!');
                }

                break;

            case 'link':

                (new Link(this.id, this.data.value, this)).setEvent('click', (e, input) => {
                    this.set_value(input.value);
                });

                break;


            case 'image':
                {
                    let image = new Image(this.id, value, this);
                    image.title = this.row?.data[1]?.value;
                    image.setEvent('change', (e, input) => {
                        this.set_value(input.value);
                    });
                }
                break;

            default:
                console.warn(`Type ${element_type} doesn exists!`);
                break;
    }
    }
}

