'use strict';
import Helper from '../../helper.js';
import Element from './element.js';
//07-12-2022
export default class Color extends Element {
    draw() {
        let container = Helper.create_element('div', {class: 'color23-container'});

        this.input = Helper.create_element('input', {
            type: 'color',
            value: this.value
        });

        container.appendChild(this.input);

        //+++

        this.code_input = Helper.create_element('input', {
            type: 'text',
            value: this.value
        });

        container.appendChild(this.code_input);

        //+++

        this.wrapper.appendChild(container);
        return this.input;
    }

    setEvent(event_type, callback) {
        super.setEvent(event_type, callback);
        this.code_input.addEventListener(event_type, ev => callback(ev, this.code_input));

        this.code_input.addEventListener(event_type, v => {
            this.input.value = v.target.value;
        });

        this.input.addEventListener(event_type, v => {
            this.code_input.value = v.target.value;
        });
    }
}

