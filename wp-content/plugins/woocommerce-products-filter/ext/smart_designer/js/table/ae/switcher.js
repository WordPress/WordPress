'use strict';
import Helper from '../../helper.js';
import Element from './element.js';
//18-11-2022
export default class Switcher extends Element {
    constructor(key, value, wrapper, type = 'switcher', action = '') {
        super(key, value, wrapper, {type, action});
    }

    draw() {
        let id = this.generate_id('sw-');

        let container = Helper.create_element('div', {
            class: 'switcher23-container'
        });

        this.input = Helper.create_element('input');

        for (const [key, value] of Object.entries({
            type: 'checkbox',
            id: id,
            class: 'switcher23'
        })) {
            this.input.setAttribute(key, value);
        }

        if (Boolean(this.value)) {
            this.input.setAttribute('checked', true);
        }

        let label = Helper.create_element('label', {
            for : id,
            class: 'switcher23-toggle'
        }, '<span></span>');

        if (this.type === 'dirswitcher') {
            label.classList.add('switcher23-toggle-dir');
        }

        container.appendChild(this.input);
        container.appendChild(label);

        this.wrapper.appendChild(container);
    }

    setEvent(event_type, callback) {
        super.setEvent(event_type, callback);

        if (this.action) {
            this.input.addEventListener('change', ev => {

                if (this.input.checked) {
                    this.value = 1;
                } else {
                    this.value = 0;
                }

                Helper.cast(this.action, {value: this.value ? 1 : 0});
            });
        }
    }

    trigger() {

        if (Boolean(this.value)) {
            //this.input.setAttribute('checked', true);
            this.input.checked = true;
        } else {
            //this.input.removeAttribute('checked');
            this.input.checked = false;
        }

        super.trigger();
        if (this.action) {
            this.input.dispatchEvent(new Event('change'));
        }
    }

}
