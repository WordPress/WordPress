/**
 * @summary     Popup23
 * @description pure javascript popup window
 * @version     1.0.1
 * @file        popup-23
 * @author      Rostislav Sofronov <realmag777>
 * @contact     https://pluginus.net/contact-us/
 * @github      https://github.com/realmag777/popup-23
 * @copyright   Copyright 2022 PluginUs.NET
 *
 * This source file is free software, available under the following license:
 * MIT license - https://en.wikipedia.org/wiki/MIT_License
 */
'use strict';

//11-03-2022
export default class Popup23 {

    constructor(data = {}, node = null, is_custom_node = true) {
        if (typeof Popup23.z_index === 'undefined') {
            Popup23.z_index = 15000;
        }

        this.container = null;
        this.node = node;
        this.is_custom_node = is_custom_node;

        this.create(data);
    }

    create(data = {}) {
        if (!this.node) {
            this.node = document.createElement('div');
            this.node.innerHTML = this.get_template();
            this.container = this.node.querySelector('.woot-form-element-container');
            document.querySelector('body').appendChild(this.node);
        } else {
            this.node.style.display = 'block';
        }

        this.node.querySelector('.woot-modal').style.zIndex = ++Popup23.z_index;
        this.node.querySelector('.woot-modal-backdrop').style.zIndex = Popup23.z_index - 1;

        this.node.querySelectorAll('.woot-modal-close').forEach(item => {
            item.addEventListener('click', e => {
                e.preventDefault();
                e.stopPropagation();

                this.close();

                return false;
            });
        });

        //***

        if (typeof data.iframe !== 'undefined' && data.iframe.length > 0) {
            let iframe = document.createElement('iframe');
            iframe.className = 'woot-iframe-in-popup';

            if (typeof data.height !== 'undefined') {
                iframe.height = data.height;
            } else {
                iframe.height = this.get_content_area_height();
            }

            iframe.frameborder = 0;
            iframe.allowfullscreen = '';
            iframe.allow = typeof data.allow !== 'undefined' ? data.allow : '';

            iframe.src = data.iframe;
            this.set_content('');
            this.append_content(iframe);
        }

        //***

        if (data.title) {
            this.set_title(data.title);
        }

        if (data.help_title) {
            if (data.help_link) {
                this.set_title_info(`<a href="${data.help_link}" class="woot-btn" target="_blank">${data.help_title}</a>`);
            }
        }

        if (data.width) {
            this.node.querySelector('.woot-modal').style.maxWidth = data.width + 'px';
        }

        if (data.height) {
            this.node.querySelector('.woot-modal').style.maxHeight = data.height + 'px';
        }

        if (data.left) {
            this.node.querySelector('.woot-modal').style.left = data.left + '%';
        }

        if (data.right) {
            this.node.querySelector('.woot-modal').style.right = data.right + '%';
        }

        if (data.top) {
            this.node.querySelector('.woot-modal').style.top = data.top + '%';
        }

        if (data.bottom) {
            this.node.querySelector('.woot-modal').style.bottom = data.bottom + '%';
        }

        if (data.action) {
            document.dispatchEvent(new CustomEvent(data.action, {detail: {...data, ...{popup: this}}}));
        }

        //***

        this.node.querySelector('.woot-modal-inner-content').addEventListener('scroll', (e) => {
            document.dispatchEvent(new CustomEvent('popup23-scrolling', {detail: {
                    top: e.srcElement.scrollTop,
                    self: this
                }}));

        });

        //***

        return this.node;
    }

    set_title(title) {
        this.node.querySelector('.woot-modal-title').innerHTML = title;
    }

    set_title_info(info) {
        this.node.querySelector('.woot-modal-title-info').innerHTML = info;
    }

    set_content(content) {
        this.container.innerHTML = content;
    }

    append_content(node) {
        this.container.appendChild(node);
    }

    get_content_area_height() {
        return this.node.querySelector('.woot-modal-inner-content').offsetHeight - 50;
    }

    get_template() {
        return this.get_template_header() + this.get_template_footer();
    }

    get_template_header() {
        return `
        <div class="woot-modal">
        <div class="woot-modal-inner">
            <div class="woot-modal-inner-header">
                <h3 class="woot-modal-title"></h3>
                <div class="woot-modal-title-info"></div>
                <a href="javascript: void(0);" class="woot-modal-close"></a>
            </div>
            <div class="woot-modal-inner-content">
                <div class="woot-form-element-container">`;
    }

    get_template_footer() {
        return `</div>
            </div>
            <div class="woot-modal-inner-footer">
                <a href="javascript: void(0);" class="button-primary woot-modal-close">Close</a>
            </div>
        </div>
    </div>

    <div class="woot-modal-backdrop"></div>`;
    }

    close() {
        if (this.is_custom_node) {
            this.node.style.display = 'none';
        } else {
            this.node.remove();
        }
    }

}

