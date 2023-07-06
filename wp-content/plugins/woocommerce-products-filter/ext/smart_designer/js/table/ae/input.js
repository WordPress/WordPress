'use strict';
import Helper from '../../helper.js';
import Element from './element.js';
//23-11-2022
export default class Input extends Element {
    constructor(key, value, wrapper, params) {
        super(key, value, wrapper, params);
    }

    draw() {
        this.input = Helper.create_element('input', {
            form: 'fakeForm',
            type: this.type && this.type !== 'undefined' ? this.type : 'text',
            value: this.value
        });

        if (this?.type && this.type === 'number') {
            this.input.value = parseFloat(this.value);
            this.input.setAttribute('min', this.cell.data.value.min);
            this.input.setAttribute('max', this.cell.data.value.max);
            this.input.setAttribute('step', this.cell.data.value.step);
        }
        this.wrapper.appendChild(this.input);
        this.input.focus();
        return this.input;
    }
}
