'use strict';
import Helper from '../../helper.js';
import Element from './element.js';
//07-12-2022
export default class Image extends Element {
    draw() {
        let container = Helper.create_element('div', {class: 'woof-sd-image-container'});

        this.input = Helper.create_element('input', {
            type: 'text',
            value: this.value
        });

        container.appendChild(this.input);

        this.lib_button = Helper.create_element('a', {
            class: 'woof-button'
        }, woof_sd.lang.select_image);

        container.appendChild(this.lib_button);

        if (!this.title) {
            this.title = woof_sd.lang.term_image;
            if (typeof this.cell === 'object') {
                if (this.cell.row
                        && this.cell.row.cells
                        && this.cell.row.cells[0]
                        && this.cell.row.cells[0].container) {
                    this.title += ' - ' + this.cell.row.cells[0].container.innerText;
                }
            }
        }

        this.lib_button.addEventListener('click', e => {
            let image = wp.media({
                title: this.title,
                multiple: false,
                library: {
                    type: ['image']
                }
            }).open().on('select', e => {
                let uploaded_image = image.state().get('selection').first();
                uploaded_image = uploaded_image.toJSON();

                if (typeof uploaded_image.sizes.thumbnail !== 'undefined') {
                    this.input.value = uploaded_image.sizes.thumbnail.url;
                } else {
                    this.input.value = uploaded_image.url;
                }

                this.trigger();
                return false;
            });


            return false;
        });

        //+++

        this.wrapper.appendChild(container);
        return this.input;
    }
}

