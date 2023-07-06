'use strict';
import Helper from './helper.js';

//18-11-2022
export default class ColorNavigation {
    constructor(color) {
        this.color = color;
        this.path = [];
        this.container = Helper.create_element('div', {class: 'woof-sd-color-navigation'});
        this.index = 0;
        this.selected_term = null;
    }

    draw() {
        if (this.selected_term) {
            this.container.innerHTML = '';
            this.color.popup.container.appendChild(this.container);

            let home = Helper.create_element('a', {
                href: '#'
            }, 'Top &raquo; ', {
                name: 'click',
                callback: e => {
                    this.index = 0;
                    this.path = [];
                    this.selected_term = null;
                    this.color.load();
                }
            });

            this.container.appendChild(home);

            if (this.path) {
                this.path.forEach((term, index) => {
                    let a = Helper.create_element('a', {
                        href: '#'
                    }, `${term.title} &raquo; `, {
                        name: 'click',
                        callback: e => {
                            this.index = index + 1;
                            this.path = this.path.slice(0, this.index - 1);
                            this.selected_term = {
                                title: term.title,
                                id: term.id
                            };
                            this.color.reload(parseInt(term.id));
                        }
                    });

                    this.container.appendChild(a);
                });
            }

            let title = Helper.create_element('span', {}, this.selected_term.title);
            this.index++;
            this.path.push(this.selected_term);
            this.container.appendChild(title);
        } else {
            this.selected_term = null;
            this.index = 0;
            this.path = [];
            this.container.innerHTML = '';
        }
    }
}
