'use strict';
export default class Helper {
    //using like broadcasting and system hook
    static cast(event, data) {
        document.dispatchEvent(new CustomEvent(event, {detail: data}));
    }

    static ajax(action, data, callback = null, json = true, custom_ajaxurl = null, signal = null) {
        fetch(custom_ajaxurl ? custom_ajaxurl : ajaxurl, {
            signal: signal,
            method: 'POST',
            credentials: 'same-origin',
            body: Helper.prepare_ajax_form_data({...{action}, ...data})
        }).then(response => json ? response.json() : response.text()).then(data => callback ? callback(data) : null);
    }

    static prepare_ajax_form_data(data) {
        const formData = new FormData();

        for (let key in data) {
            formData.append(key, data[key]);
        }

        return formData;
    }

    static create_element(type, data = {}, content = '', event = null) {
        let item = document.createElement(type);
        if (Object.values(data)) {
            for (const [key, value] of Object.entries(data)) {
                item.setAttribute(key, value);
            }
        }

        if (content) {
            if (typeof content === 'string') {
                item.innerHTML = content;
            }

            if (typeof content === 'object') {
                item.appendChild(content);
            }
        }

        if (event) {
            item.addEventListener(event.name, e => {
                e.preventDefault();//!!
                e.stopPropagation();
                event.callback(e);
                return false;
            });
        }

        return item;
    }

    static create_html_select(options, selected = null, data = {}, event = null) {
        let select = Helper.create_element('select', data, '', event);

        for (const [k, v] of Object.entries(options)) {
            let option = Helper.create_element('option', {
                value: k
            }, typeof v === 'object' ? v.title : v);
            if (k == selected) {//!! ==, no ===
                option.selected = true;
            }

            select.appendChild(option);
        }

        return select;
    }

    static message(message_txt, type = 'notice', duration = 0) {
        if (duration === 0) {
            duration = 444;
        }

        //***

        let container = null;

        if (!document.querySelectorAll('#growls').length) {
            container = Helper.create_element('div', {
                id: 'growls',
                class: 'tr'
            });
            document.querySelector('body').appendChild(container);
        } else {
            container = document.getElementById('growls');
        }

        //***

        let wrapper = Helper.create_element('div', {
            id: Helper.generate_key('m-'),
            class: 'growl growl-large growl-' + type
        });

        let title = Helper.create_element('div', {
            class: 'growl-title'
        });

        let title_text = '';

        switch (type) {
            case 'warning':
                title_text = 'Warning';
                break;

            case 'error':
                title_text = 'Error';
                break;

            default:
                title_text = 'Notice';
                break;
        }

        title.innerHTML = title_text;

        let message = Helper.create_element('div', {
            class: 'growl-message'
        }, message_txt);

        //***

        //wrapper.appendChild(close);
        wrapper.appendChild(title);
        wrapper.appendChild(message);

        container.innerHTML = '';
        container.appendChild(wrapper);

        wrapper.addEventListener('click', function (e) {
            e.stopPropagation();
            this.remove();
            return false;
        });

        if (duration !== -1) {
            setTimeout(function () {
                wrapper.style.opacity = 0;
                setTimeout(function () {
                    wrapper.remove();
                }, 777);
            }, duration);
    }

    }

    static generate_key(prefix = 'k') {
        return prefix + '-' + Math.random().toString(36).substring(7);
    }

    //avoid multiple reactions for reinited objects which uses document attached events
    static events = [];
    static addSingleEventListener(event_name, instance, callback) {

        if (!instance.instance_key) {
            console.error(`Instance ${instance.constructor.name} not has instance_key field!`);
            return;
        }

        if (!Helper.events[instance.instance_key]) {
            Helper.events[instance.instance_key] = [];
        }

        if (!Helper.events[instance.instance_key][event_name]) {
            Helper.events[instance.instance_key][event_name] = callback.bind(instance);
            document.addEventListener(event_name, e => Helper.events[instance.instance_key][event_name](e));
        } else {
            Helper.events[instance.instance_key][event_name] = callback.bind(instance);
        }
    }

}

