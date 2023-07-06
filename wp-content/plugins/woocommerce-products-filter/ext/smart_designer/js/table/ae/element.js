'use strict';
//24-10-2022
export default class Element {
    static elements = [];

    constructor(key, value, wrapper, params = null) {
        if (new .target === Element) {
            throw new TypeError('Cannot construct Element instances directly');
        }

        this.cell = null;//we can communicate with cell, so with row using this.cell.row
        if (wrapper && typeof wrapper === 'object' && wrapper.container) {
            this.cell = wrapper;
            wrapper = this.cell.container;
        }

        this.key = key;
        this.value = value;
        this.wrapper = wrapper;

        if (params) {
            for (const [k, v] of Object.entries(params)) {
                this[k] = v;
            }
        }

        this.draw();
    }

    //for outside actions
    setEvent(event_type, callback) {
        this.event_type = event_type;
        this.input.addEventListener(event_type, ev => callback(ev, this.input));
    }

    set(value) {

        if (typeof this.cell.data.value === 'object') {
            this.cell.data.value.value = value;
        } else {
            this.cell.data.value = value;
        }

        this.value = value;//!!
        this.trigger();
    }

    trigger() {
        if (this.event_type) {
            this.input.dispatchEvent(new Event(this.event_type));
        }
    }

    generate_id(prefix = '') {
        return prefix + Math.random().toString(36).substring(7);
    }

    draw() {
        console.error('Method draw() should be overloaded');
    }
}

