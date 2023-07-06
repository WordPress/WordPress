'use strict';
import Helper from './helper.js';
import Popup23 from './lib/popup-23.js';

//06-12-2022
export default class Presets {
    constructor(panel) {
        this.panel = panel;
        this.popup = new Popup23({right: 5, left: 5, top: 10, bottom: 10, title: woof_sd.lang.presets}, null, false);
        this.popup.set_content(woof_sd.lang.loading + ' ...');
        this.list = Helper.create_element('ul', {class: 'woof-sd-presets'});
        this.load();
    }

    load() {
        this.popup.set_content('');

        let notice = Helper.create_element('div', {class: 'woof-notice woof-sd-presets-notice'}, woof_sd.lang.about_presets);
        this.popup.append_content(notice);

        let input = Helper.create_element('input', {
            type: 'text',
            placeholder: woof_sd.lang.preset_placeholder
        }, '', {
            name: 'keyup',
            callback: e => {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();

                //enter
                if (e.keyCode === 13) {
                    this.create(input.value);
                    input.value = '';
                }

                //escape
                if (e.keyCode === 27) {
                    input.value = '';
                }

                return false;
            }
        });

        this.popup.append_content(input);
        this.popup.append_content(this.list);
        this.draw_loader(woof_sd.lang.loading + ' ...');

        //+++

        Helper.ajax('woof_sd_load_presets', {
            type: this.panel.sd.selected_type
        }, data => {
            if (data) {

                this.remove_loader();

                for (let preset of data) {
                    this.draw(preset.id, preset.title);
                }

            } else {
                this.popup.set_content(woof_sd.lang.no_items);
            }
        });
    }

    draw_loader(title) {
        let li = Helper.create_element('li', {class: 'woof-sd-preset-loader'});
        let item = Helper.create_element('span', {}, title ? title : woof_sd.lang.creating + ' ...');
        li.appendChild(item);
        this.list.appendChild(li);
    }

    remove_loader() {
        this.list.querySelector('.woof-sd-preset-loader').remove();
    }

    draw(id, title) {
        let li = Helper.create_element('li', {}, `<div class="woof-sd-preset-cell"><b></b></div><div class="woof-sd-preset-cell"></div>`);

        let add = Helper.create_element('a', {
            href: '#',
            'data-id': id
        }, title, {
            name: 'click',
            callback: e => this.apply(add.dataset.id)
        });

        let del = Helper.create_element('a', {
            href: '#',
            class: 'woof-sd-presets-actions',
            style: 'transform: rotate(45deg);',
            'data-id': id
        }, ' <span class="icon-plus-circle"></span>', {
            name: 'click',
            callback: e => this.delete(add.dataset.id, li)
        });

        li.querySelector('b').appendChild(add);
        li.querySelector('.woof-sd-preset-cell').appendChild(del);

        let view = Helper.create_element('a', {
            href: '#',
            class: 'woof-sd-presets-actions',
            style: 'right: 45px;',
            'data-id': id
        }, ' <span class="icon-eye"></span>', {
            name: 'click',
            callback: e => this.view(add.dataset.id)
        });

        li.querySelector('.woof-sd-preset-cell').appendChild(view);

        let edit = Helper.create_element('a', {
            href: '#',
            class: 'woof-sd-presets-actions',
            style: 'right: 75px;',
            'data-id': id
        }, ' <span class="icon-edit"></span>', {
            name: 'click',
            callback: e => this.edit(add.dataset.id)
        });

        li.querySelector('.woof-sd-preset-cell').appendChild(edit);

        this.list.appendChild(li);
    }

    create(title) {
        if (title.length > 0) {
            this.draw_loader(title);
            Helper.ajax('woof_sd_create_preset', {
                title,
                type: this.panel.sd.selected_type,
                element_id: this.panel.sd.selected_row_id
            }, id => {
                this.remove_loader();
                this.draw(id, title);
            });
        }
    }

    apply(option_id) {
        if (confirm(woof_sd.lang.sure_apply_preset)) {
            Helper.message(woof_sd.lang.loading + ' ...');
            Helper.ajax('woof_sd_apply_preset', {
                option_id,
                element_id: this.panel.sd.selected_row_id
            }, options => {
                Helper.message(woof_sd.lang.loaded);
                this.popup.close();
                this.panel.sd.reload_scene(1, true);
            });
        }
    }

    delete(option_id, li) {
        if (confirm(woof_sd.lang.sure_delete_preset)) {
            li.remove();
            Helper.ajax('woof_sd_delete_preset', {option_id});
        }
    }

    view(option_id) {
        Helper.message(woof_sd.lang.loading + ' ...', 'warning');
        let popup = new Popup23({right: 15, left: 15, top: 20, bottom: 20, title: woof_sd.lang.preset_code}, null, false);
        popup.set_content(woof_sd.lang.loading + ' ...');

        Helper.ajax('woof_sd_get_preset', {
            option_id
        }, code => {
            Helper.message(woof_sd.lang.loaded);
            popup.set_content('');
            let textarea = Helper.create_element('textarea', {rows: 10, disabled: 'disabled'}, JSON.stringify(code));
            popup.append_content(textarea);
        });
    }

    edit(option_id) {
        Helper.message(woof_sd.lang.loading + ' ...', 'warning');
        let popup = new Popup23({right: 15, left: 15, top: 20, bottom: 40, title: woof_sd.lang.preset_import}, null, false);
        let textarea = Helper.create_element('textarea', {rows: 7, style:'margin-bottom: 9px;'});
        popup.append_content(textarea);

        let button = Helper.create_element('a', {href: '#', class: 'button woof-button-outline-secondary'}, woof_sd.lang.import, {
            name: 'click',
            callback: e => {
                popup.close();
                Helper.message(woof_sd.lang.saving + ' ...');
                Helper.ajax('woof_sd_import_preset', {
                    option_id,
                    value: textarea.value
                }, data => Helper.message(woof_sd.lang.saved));
            }
        });

        popup.append_content(button);
    }
}

