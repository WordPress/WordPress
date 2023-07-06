'use strict';
import Helper from '../../helper.js';
import Element from './element.js';
//18-11-2022
export default class Checkbox extends Element {
    draw() {
        this.input = Helper.create_element('input', {type: 'checkbox'});

        if (Boolean(this.value)) {
            this.input.setAttribute('checked', true);
        }

        this.wrapper.appendChild(this.input);
        return this.input;
    }
}
