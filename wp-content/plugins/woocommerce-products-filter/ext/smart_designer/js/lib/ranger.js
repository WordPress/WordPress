/**
 * @summary     Ranger23
 * @description range slider
 * @version     1.0.5
 * @file        ranger-23
 * @author      Rostislav Sofronov <realmag777>
 * @contact     https://pluginus.net/contact-us/
 * @github      https://github.com/realmag777/ranger-23
 * @copyright   Copyright 2022 PluginUs.Net
 *
 * This source file is free software, available under the following license:
 * MIT license - https://en.wikipedia.org/wiki/MIT_License .Basically that
 * means you are free to use Ranger23 as long as this header is left intact.
 */
'use strict';
//17-10-2022
export default class Ranger23 {
    constructor(track, cast_id = null, handler_width = 30, additional_options = {}) {
        this.cast_id = cast_id;
        if (!this.cast_id) {
            this.cast_id = 'slider-' + (new Date()).getTime() + Math.floor(Math.random() * 100);
        }

        this.dragged = null;
        this.track = track;
        this.handler_width = handler_width;
        this.additional_options = additional_options;
        this.set_styles();
        this.min = parseInt(this.track.dataset.min);
        this.max = parseInt(this.track.dataset.max);
        this.prev_selected_left_val = this.selected_left_val = parseInt(this.track.dataset.selectedMin);
        this.prev_selected_right_val = this.selected_right_val = parseInt(this.track.dataset.selectedMax);

        this.value = [this.selected_left_val, this.selected_right_val];

        this.is_mobile = 'ontouchstart' in document.documentElement,
                this.event_click = this.is_mobile ? 'click' : 'click',
                this.event_mousedown = this.is_mobile ? 'touchstart' : 'mousedown',
                this.event_mouseup = this.is_mobile ? 'touchend' : 'mouseup',
                this.event_mouseout = this.is_mobile ? 'touchcancel' : 'mouseout',
                this.event_mousemove = this.is_mobile ? 'touchmove' : 'mousemove';

        this.container = document.createElement('div');
        this.container.className = 'ranger23-container';

        this.handler_left = document.createElement('div');
        this.handler_left.className = 'ranger23-handler-left';
        this.handler_left.innerHTML = '<span></span>';

        this.disable_handler_left = false;
        if ('disable_handler_left' in this.additional_options) {
            if (this.additional_options.disable_handler_left) {
                this.disable_handler_left = true;
            }
        }

        if (this.disable_handler_left) {
            this.handler_left.classList.add('ranger23-handler-left-disabled');
        }


        this.handler_right = document.createElement('div');
        this.handler_right.className = 'ranger23-handler-right';
        this.handler_right.innerHTML = '<span></span>';

        this.handler_min = document.createElement('div');
        this.handler_min.className = 'ranger23-min';

        this.handler_max = document.createElement('div');
        this.handler_max.className = 'ranger23-max';

        this.bar = document.createElement('div');
        this.bar.className = 'ranger23-bar';

        this.container.appendChild(this.handler_min);
        this.container.appendChild(this.handler_left);
        this.container.appendChild(this.bar);
        this.container.appendChild(this.handler_right);
        this.container.appendChild(this.handler_max);
        this.track.appendChild(this.container);

        this.container_x = (this.container.getBoundingClientRect()).x;
        this.container_width = (this.container.getBoundingClientRect()).width;
        this.max_track = parseInt(this.container_width) - 2 * this.handler_width;

        this.init_math();
        this.init_events();
        this.redraw_bar();

        window.addEventListener('resize', (e) => {
            this.resize();
        });

        //fix for tabs, 13-05-2021
        setTimeout(() => {
            this.resize();
        }, 123);

        //if hidden, then shown
        (new IntersectionObserver((entries) => {
            if (entries[0].isIntersecting) {
                this.resize();
            }
        })).observe(this.track);

    }

    resize() {
        this.container_x = (this.container.getBoundingClientRect()).x;
        this.container_width = (this.container.getBoundingClientRect()).width;
        this.max_track = parseInt(this.container_width) - 2 * this.handler_width;

        this.init_math();
        this.redraw_bar();
    }

    init_math(right = false) {
        if (this.selected_left_val > this.min) {
            this.handler_left.style.left = this.calculate_left_distance(this.selected_left_val) + 'px';
        } else {
            this.handler_left.style.left = 0;
        }

        if (this.selected_right_val < this.max) {
            this.handler_right.style.left = this.calculate_right_distance(this.selected_right_val) + 'px';
        } else {
            //commented on 03-03-2022 to avoid blinking while redrawing - uncommeneted as it need for positioning
            if (right) {
                this.handler_right.style.left = this.container_width - this.handler_width + 'px';
            }
        }

        this.__reset_handlers_view_values();
        this.handler_min.innerHTML = this.min;
        this.handler_max.innerHTML = this.max;
    }

    init_events() {

        if (!this.disable_handler_left) {
            this.handler_min.addEventListener(this.event_click, (e) => {
                this.handler_left.style.left = 0;
                this.handler_left.querySelector('span').innerHTML = this.selected_left_val = this.min;
                this.cast();
            });
        }

        this.handler_max.addEventListener(this.event_click, (e) => {
            this.handler_right.style.left = this.container_width - this.handler_width + 'px';
            this.handler_right.querySelector('span').innerHTML = this.selected_right_val = this.max;
            this.cast();
        });


        this.handler_right.addEventListener(this.event_mousedown, (e) => {
            this.dragged = e.target.parentElement;
            this.dragged.style.zIndex = 2;
        });


        if (!this.disable_handler_left) {
            this.handler_left.addEventListener(this.event_mousedown, (e) => {
                this.dragged = e.target.parentElement;
                this.dragged.style.zIndex = 2;
            });

            this.handler_left.addEventListener(this.event_mousemove, (e) => {
                this.__reset_handlers_view_values();
            });
        }

        this.handler_right.addEventListener(this.event_mousemove, (e) => {
            this.__reset_handlers_view_values();
        });





        this.container.addEventListener(this.event_mouseout, (e) => {
            this.__reset_handlers_view_values();
        });


        document.addEventListener(this.event_mousemove, (e) => {
            if (this.dragged && this.container) {

                if (this.additional_options.instant_cast) {
                    this.cast();
                }

                let page_x = e.pageX - this.handler_width / 2;//for on handler mouse pointer centrating

                if (this.is_mobile) {
                    page_x = e.touches[0].clientX - this.handler_width / 2;
                }

                if (page_x >= this.container_x && page_x <= this.container_x + this.container_width - this.handler_width) {

                    let distance = page_x - this.container_x;
                    let can_move = true;

                    if (this.dragged === this.handler_left) {
                        if (distance + this.handler_width - 1 >= this.handler_right.offsetLeft) {
                            can_move = false;
                        }
                    } else {
                        if (distance - this.handler_width <= this.handler_left.offsetLeft) {
                            can_move = false;
                        }
                    }

                    //+++

                    if (can_move) {
                        this.dragged.style.left = distance + 'px';
                        this.calculate();
                        this.redraw_bar();
                    }

                } else {
                    if (this.dragged === this.handler_left) {
                        this.handler_left.style.left = 0;
                        this.handler_left.querySelector('span').innerHTML = this.selected_left_val = this.min;
                    } else {
                        this.handler_right.style.left = this.container_width - this.handler_width + 'px';
                        this.handler_right.querySelector('span').innerHTML = this.selected_right_val = this.max;
                    }

                    this.redraw_bar();
                }
            }

        });

        document.addEventListener(this.event_mouseup, (e) => {
            if (this.dragged && this.container) {
                this.dragged.onmouseup = null;
                this.dragged.style.zIndex = 1;
                this.dragged = null;

                if (this.is_mobile) {
                    if (!this.disable_handler_left) {
                        this.handler_min.innerHTML = this.min;
                    }
                    this.handler_max.innerHTML = this.max;
                }

                this.cast();
            }
        });

        //+++

        this.init_events_container();

    }

    init_events_container() {
        //only for PC
        this.container.addEventListener(this.event_mousemove, (e) => {

            if (e.target === this.container && !this.dragged) {
                let left_distance = e.clientX - this.container_x;

                if (left_distance < this.handler_left.offsetLeft && !this.disable_handler_left) {
                    this.handler_left.querySelector('span').innerHTML = this.calculate_left_value(left_distance);
                }

                if (left_distance > this.handler_right.offsetLeft) {
                    left_distance -= this.handler_width;
                    this.handler_right.querySelector('span').innerHTML = this.calculate_right_value(left_distance);
                }

            }

        });

        this.container.addEventListener(this.event_click, (e) => {
            if (e.target === this.container && !this.dragged) {
                let left_distance = e.clientX - this.container_x;

                if (left_distance < this.handler_left.offsetLeft && !this.disable_handler_left) {
                    this.handler_left.querySelector('span').innerHTML = this.selected_left_val = this.calculate_left_value(left_distance);
                    this.handler_left.style.left = left_distance + 'px';
                }

                if (left_distance > this.handler_right.offsetLeft) {
                    left_distance -= this.handler_width;
                    this.handler_right.querySelector('span').innerHTML = this.selected_right_val = this.calculate_right_value(left_distance);
                    this.handler_right.style.left = left_distance + 'px';
                }

                this.cast();
            }
        });

        //+++

        //only for PC
        this.bar.addEventListener(this.event_mousemove, (e) => {

            if (e.target === this.bar && !this.dragged) {
                let left_distance = e.clientX - this.container_x;

                if (!this.disable_handler_left) {
                    if (left_distance > this.handler_left.offsetLeft && left_distance < this.handler_right.offsetLeft) {

                        if ((left_distance - this.handler_left.offsetLeft - this.handler_width) < (this.handler_right.offsetLeft - left_distance)) {
                            left_distance -= this.handler_width;
                            this.handler_left.querySelector('span').innerHTML = this.calculate_left_value(left_distance);
                        } else {
                            this.handler_right.querySelector('span').innerHTML = this.calculate_right_value(left_distance);
                        }
                    }
                } else {
                    this.handler_right.querySelector('span').innerHTML = this.calculate_right_value(left_distance);
                }

            }

        });

        //reset
        this.container.addEventListener('mouseleave', (e) => {
            this.__reset_handlers_view_values();
        });

        this.bar.addEventListener(this.event_click, (e) => {

            if (e.target === this.bar && !this.dragged) {
                let left_distance = e.clientX - this.container_x;

                if (!this.disable_handler_left) {
                    if (left_distance > this.handler_left.offsetLeft && left_distance < this.handler_right.offsetLeft) {

                        if ((left_distance - this.handler_left.offsetLeft - this.handler_width) < (this.handler_right.offsetLeft - left_distance)) {
                            left_distance -= this.handler_width;
                            this.handler_left.querySelector('span').innerHTML = this.selected_left_val = this.calculate_left_value(left_distance);
                            this.handler_left.style.left = left_distance + 'px';
                        } else {
                            this.handler_right.querySelector('span').innerHTML = this.selected_right_val = this.calculate_right_value(left_distance);
                            this.handler_right.style.left = left_distance + 'px';
                        }

                    }
                } else {
                    this.handler_right.querySelector('span').innerHTML = this.selected_right_val = this.calculate_right_value(left_distance);
                    this.handler_right.style.left = left_distance + 'px';
                }

                this.cast();

            }

        });

    }

    calculate_left_value(left_distance) {
        return Math.ceil(parseFloat(left_distance / this.max_track) * (this.max - this.min) + this.min);
    }

    calculate_left_distance(value) {
        return Math.floor(parseFloat((value - this.min) / (this.max - this.min)) * this.max_track);
    }

    calculate_right_value(left_distance) {
        return Math.ceil(parseFloat(left_distance / this.max_track) * (this.max - this.min) + this.min - parseFloat(this.handler_width / this.max_track) * (this.max - this.min));
    }

    calculate_right_distance(value) {
        return Math.floor(parseFloat((value + parseFloat(this.handler_width / this.max_track) * (this.max - this.min) - this.min) / (this.max - this.min) * this.max_track));
    }

    __reset_handlers_view_values() {
        this.handler_left.querySelector('span').innerHTML = this.selected_left_val;
        this.handler_right.querySelector('span').innerHTML = this.selected_right_val;
    }

    calculate() {
        let left_distance = parseInt(this.dragged.style.left);

        if (this.dragged === this.handler_left) {
            this.handler_left.querySelector('span').innerHTML = this.selected_left_val = this.calculate_left_value(left_distance);

            if (this.is_mobile && !this.disable_handler_left) {
                this.handler_min.innerHTML = this.selected_left_val;
            }
        }

        if (this.dragged === this.handler_right) {
            this.handler_right.querySelector('span').innerHTML = this.selected_right_val = this.calculate_right_value(left_distance);

            if (this.is_mobile) {
                this.handler_max.innerHTML = this.selected_right_val;
            }
        }

        //fix-checking for values as we use rounding by Math.ceil
        if (this.selected_left_val > this.selected_right_val) {
            this.handler_left.querySelector('span').innerHTML = this.selected_left_val = this.selected_right_val;
        }

        if (this.selected_right_val < this.selected_left_val) {
            this.handler_right.querySelector('span').innerHTML = this.selected_right_val = this.selected_left_val;
        }
    }

    redraw_bar() {
        this.bar.style.left = this.handler_left.offsetLeft + this.handler_width - 1 + 'px';
        this.bar.style.width = this.handler_right.offsetLeft - this.handler_left.offsetLeft - this.handler_width + 1 + 'px';

        //ranger23-bar-var-equal
        if (this.selected_left_val === this.selected_right_val) {
            this.bar.classList.add('ranger23-bar-var-equal');
        } else {
            this.bar.classList.remove('ranger23-bar-var-equal');
        }
    }

    //API: call after creation using slider obj slider.draw_inputs(input, wrapper)
    draw_inputs(wrapper) {

        let _this = this;

        let input = document.createElement('input');
        input.setAttribute('type', 'text');
        input.className = 'ranger23-slider-input';
        input.value = this.selected_left_val;
        input.setAttribute('min', this.min);
        input.setAttribute('max', this.max);
        input.setAttribute('pattern', '[0-9]*');
        input.setAttribute('form', 'fakeForm');

        input.addEventListener('change', function (e) {
            if (this.value < _this.min) {
                this.value = _this.min;
            }
            if (this.value > _this.max) {
                this.value = _this.min;
            }

            _this.set_left(this.value);
        });


        //+++

        wrapper.appendChild(input);
        let input_right = input.cloneNode(true);

        if (this.additional_options.disable_handler_left) {
            input.style.display = 'none';
        }

        input_right.value = this.selected_right_val;
        input_right.classList.add('ranger23-slider-input-right');

        input_right.addEventListener('change', function (e) {
            if (this.value < _this.min) {
                this.value = _this.min;
            }
            if (this.value > _this.max) {
                this.value = _this.max;
            }

            _this.set_right(this.value);
        });

        wrapper.appendChild(input_right);

        //+++

        document.addEventListener('ranger23-update', (e) => {
            if (e.detail.cast_id === _this.cast_id) {
                input.value = parseInt(e.detail.from);
                input_right.value = parseInt(e.detail.to);
            }
        });
    }

    cast() {
        this.redraw_bar();

        if (this.prev_selected_left_val !== this.selected_left_val || this.prev_selected_right_val !== this.selected_right_val) {
            //cast only if range values are changed
            this.prev_selected_left_val = this.selected_left_val;
            this.prev_selected_right_val = this.selected_right_val;

            document.dispatchEvent(new CustomEvent('ranger23-update', {detail: {
                    cast_id: this.cast_id,
                    self: this, //pointer
                    from: this.selected_left_val,
                    to: this.selected_right_val,
                    min: this.min,
                    max: this.max
                }}));

            this.value = [this.selected_left_val, this.selected_right_val];
            this.onSelect();
        }

    }

    onSelect() {
        //for API
    }

    remove() {
        this.container.remove();
    }

    set_right(value) {
        this.selected_right_val = this.track.dataset.selectedMax = parseInt(value);
        this.init_math(true);
        this.cast();
    }

    set_left(value) {
        this.selected_left_val = this.track.dataset.selectedMin = parseInt(value);
        this.init_math();
        this.cast();
    }

    set_styles() {
        if (this.additional_options.styles && this.additional_options.wrapper) {
            let styles = this.additional_options.styles;
            let wrapper = this.additional_options.wrapper;

            for (let index in styles) {
                wrapper.style.setProperty(`--ranger23-${index}`, styles[index]);

                if (index === 'handler_width') {
                    this.handler_width = parseInt(styles[index]);
                }
            }

        }
    }

}

