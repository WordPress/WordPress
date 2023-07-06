'use strict';

import Checkbox from './ie/checkbox.js';
import Radio from './ie/radio.js';
import Switcher from './ie/switcher.js';
import Color from './ie/color.js';
import * as OuterElements from './import-outer.js?nocache=1';

//16-12-2022
//works with interactive elements
export default class Visor {
    constructor(sd) {
        this.sd = sd;
        this.wrapper = document.getElementById('sd-visor');
        this.selected_type = this.sd.selected_type;//checkbox is by default
        this.template_num = this.sd.selected_el_template;
        this.container = document.createElement('div');

        this.prepare();
        this.draw();
    }

    prepare() {
        this.wrapper.innerHTML = '';
        this.wrapper.removeAttribute('style');//!!another way we have blink from previous settings
    }

    draw(template_num = null) {
        if (template_num !== null) {
            this.template_num = template_num;
        }

        switch (this.selected_type) {
            case 'checkbox':

                let checkbox = new Checkbox(this);
                checkbox.draw();

                break;

            case 'radio':

                let radio = new Radio(this);
                radio.draw();

                break;

            case 'switcher':

                let switcher = new Switcher(this);
                switcher.draw();

                break;

            case 'color':

                let color = new Color(this);
                color.draw();

                break;

            default:

                if (OuterElements.modules[this.selected_type]) {
                    //this.selected_type is the same as folder name
                    let module = new OuterElements.modules[this.selected_type](this, this.selected_type);
                    module.draw();
                } else {
                    console.log(`Type ${this.selected_type} doesn exists!`);
                }

                break;
    }

    }

    redraw() {
        this.wrapper.innerHTML = '';
        this.draw();
    }

    set(key, value, measure = '', before = '', after = '') {
        this.wrapper.style.setProperty(`--woof-sd-ie-${this.sd.selected_el_prefix}${key}`, before + value + measure + after);
    }
}


