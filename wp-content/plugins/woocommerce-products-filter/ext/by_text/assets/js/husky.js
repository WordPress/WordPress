'use strict';

class Husky {
    constructor(input, data = {}) {
        this.searched_value = '';
        this.current_page = 0;        
        this.data = Object.assign({}, data);
        this.input = input;
        this.init_input();
        this.container = document.createElement('div');
        this.container.className = 'woof_husky_txt';
        this.input.insertAdjacentElement('afterend', this.container);

        this.fetch_timer = null;
        this.fetch_controller = null;

        document.addEventListener('click', ev => {
            if (ev.target !== this.input) {
                this._show(false);
            }
        });
    }

    init_input() {

        Object.keys(this.data).forEach((marker) => {
            if (this.input.hasAttribute(`data-${marker}`)) {
                this.data[marker] = this.input.getAttribute(`data-${marker}`);
            }
        });

        this.input.value = this.data.s;
        if (!this.input.classList.contains('woof_husky_txt-input')) {
            this.input.classList.add('woof_husky_txt-input');
        }

        this.input.setAttribute('autocomplete', 'off');

        //+++

        let _this = this;

	if (this.input.closest('div').querySelector('.woof_text_search_go') ) { 
	    this.input.closest('div').querySelector('.woof_text_search_go').addEventListener('click', function (e) {
		e.stopPropagation();
		e.preventDefault();
		let search_text = _this.input.value;
		woof_current_values['woof_text'] = search_text;
		woof_ajax_page_num = 1;
		woof_submit_link(woof_get_submit_link(), 0);

		return false;
	    });
	}
        if (this.input.closest('form[role=search]')) {
            this.input.closest('form[role=search]').addEventListener('submit', function (e) {
                e.stopPropagation();
                e.preventDefault();

                let search_text = _this.input.value;
                let min_symbols = (typeof _this.data.min_symbols) ? _this.data.min_symbols : 3;
                if (search_text.length >= min_symbols) {		    
		    woof_current_values['woof_text'] = search_text;
		    woof_ajax_page_num = 1;
			// woof_text_do_submit = false;
			woof_submit_link(woof_get_submit_link(), 0);

                }

                return false;
            });
        }

        this.input.addEventListener('keyup', function (e) {
            e.stopPropagation();
            e.preventDefault();
            _this.searched_value = this.value;
            _this._search(1, e.key);

            return false;
        });

        this.input.addEventListener('focus', (e) => {
            if (this._get_options_container()) {
                this._get_options_container().style.display = 'block';
            }
        });
    }

    _search(current_page = 1, ekey = null) {
	
        let _this = this;
        let search_text = encodeURIComponent(this.searched_value);


        let min_symbols = (typeof this.data.min_symbols) ? this.data.min_symbols : 3;
        this._loader(false);
        if (this.fetch_timer) {
            clearTimeout(this.fetch_timer);
        }

        if (search_text.length === 0) {
            this._cross(false);
        }
	if (search_text.length >= min_symbols) {
	    jQuery('.woof_text_search_go').show(222);
	} else {
	    jQuery('.woof_text_search_go').hide();
	}

	woof_current_values['woof_text'] = search_text;    
	if (ekey === 'Enter') {
	    this._loader();
	   // woof_current_values['woof_text'] = search_text;
	    woof_ajax_page_num = 1;
	    woof_submit_link(woof_get_submit_link(), 0);
	    this._loader(false);
	    return true;
	}


      
	if (parseInt(_this.data.autocomplete)== 0) {
	    return false;
	}

        if (this.searched_value === search_text && current_page === this.current_page) {
            //return true; - TODO for pagination clicking
        }

        this.current_page = current_page;

        //+++

        let delay_time = 777;
        if (ekey === 'Enter' || ekey === 'Paged') {
            delay_time = 1;
        }

        this.fetch_timer = setTimeout(() => {

            let do_fetch = true;

            if (search_text.length < min_symbols) {
                do_fetch = false;
		
                this._reset();
            }
            this._loader();
            if (do_fetch) {
		    //check current tax
		    var cur_tax = false;
		    if (Object.keys(woof_really_curr_tax).length > 0) {
			cur_tax  = woof_really_curr_tax.term_id + '-' + woof_really_curr_tax.taxonomy;
		    }

                let request_data = {
                    action: 'woof_text_search',
                    value: search_text,
		    link: woof_get_submit_link(),
		    cur_tax: cur_tax,
                    page: current_page - 1,
                    ...this.data
                };

                this._cross();

                if (this.fetch_controller) {
                    //cancel ajax request if user is too quick
                    this.fetch_controller.abort();
                }

                this.fetch_controller = new AbortController();
                const signal = this.fetch_controller.signal;

                fetch(woof_husky_txt.ajax_url, {...{
                            method: 'POST',
                            credentials: 'same-origin',
                            body: (function (data) {
                                    const formData = new FormData();

                                    Object.keys(data).forEach(function (k) {
                                            formData.append(k, data[k]);
                                    });

                                    return formData;
                            })(request_data)
                    }, signal}).then(response => response.json()).then(response => {

                    this._reset();
                    this.searched_value = encodeURIComponent(search_text);
                    this._loader(false);
                    let answer = document.createElement('div');
                    this.answer = answer;
                    this.answer.className = 'woof_husky_txt-container';
                    this.container.appendChild(answer);

                    if (response.options.length > 0) {
                        response.options.forEach(function (row) {
                            let option = document.createElement('div');
                            option.className = 'woof_husky_txt-option';
                            option.innerHTML = row;

                            //+++

                            let title = option.querySelector('.woof_husky_txt-option-title');
                            let title_link = title.querySelector('a');


                            if (parseInt(_this.data.title_light)) {
                                let pattern = new RegExp('(' + search_text + ')', 'ig');
                                title_link.innerHTML = title.innerText.replace(pattern, `<span class='woof_husky_txt-highlight'>$1</span>`);
                            }


                          //  if (parseInt(_this.data.how_to_open_links)) {
//                                option.classList.add('husky-option-clickable');
//                                option.addEventListener('click', (e) => {
//                                    e.stopPropagation();
//                                    window.open(title_link.href, _this.data.click_target);
//                                    return false;
//                                });
                         //   }

                            answer.appendChild(option);
                        });

                        if (response.pagination.pages > 1) {
                            this._draw_pagination(answer, response);
                        }
                    } else {
                        let option = document.createElement('div');
                        option.className = 'woof_husky_txt-option';

                        let content_container = document.createElement('div');

                        let title = document.createElement('div');
                        title.className = 'woof_husky_txt-option-title';
                        title.textContent = woof_husky_txt.not_found;
                        content_container.appendChild(title);
                        option.appendChild(content_container);
                        answer.appendChild(option);
                    }

                    this._show();

                }).catch((err) => {
                    console.log(err);
                });

            } else {
                this._loader(false);
            }
        }, delay_time);

        return true;
    }

    _show(is = true) {
        if (is) {
            //animation
            let counter = 1;
            let container = this._get_options_container();
            let timer = setInterval(() => {
                let max_height = 0;

                if (typeof this.data.max_open_height !== 'undefined') {
                    max_height = parseInt(this.data.max_open_height);
                } else {
                    container.querySelectorAll('.woof_husky_txt-option').forEach(function (item) {
                        max_height += item.offsetHeight;
                    });
                }

                //growing
                container.style.maxHeight = parseFloat(0.05 * counter) * max_height + 'px';

                if (parseInt(container.style.maxHeight) >= max_height) {
                    clearInterval(timer);
                    if (typeof this.data.max_open_height !== 'undefined') {
                        container.style.maxHeight = max_height + 'px';
                        container.style.overflow = 'auto';
                    } else {
                        container.style.maxHeight = '100vh';
                    }
                }
                counter++;
            }, 10);
        } else {

            if (this._get_options_container()) {
                let container = this._get_options_container();
                container.style.display = 'none';

                if (typeof container.style.overflow !== 'undefined') {
                    //container.scrollTop = 0;//!!
                    //container.style.overflow = null;
                }
            }

    }
    }

    _loader(show = true) {
        if (show) {
            this.loader = document.createElement('div');
            this.loader.className = 'woof_husky_txt-loader';
            this.loader.style.width = this.loader.style.height = this.input.offsetHeight + 'px';
            this.loader.style.top = '-' + (this.input.offsetHeight - 1) + 'px';
            this.container.appendChild(this.loader);
        } else {
            if (this.loader) {
                this.loader.remove();
            }
    }
    }

    _cross(show = true) {

        if (!this.cross) {
            this.cross = document.createElement('span');
            this.cross.className = 'woof_husky_txt-cross';
            this.cross.innerText = 'x';
            this.cross.style.width = this.cross.style.height = (this.input.offsetHeight / 2) + 'px';
            this.cross.style.top = '-' + (this.input.offsetHeight / 2) + 'px';
	    this.cross.style.right = '-23px';

            this.container.appendChild(this.cross);

            let _this = this;
            this.cross.addEventListener('click', function (e) {
                e.stopPropagation();
                _this.input.value = '';
                _this._cross(false);
                if (_this.answer) {
                    _this.answer.remove();
                }
                if (_this.fetch_controller) {
                    //cancel ajax request if user is too quick
                    _this.fetch_controller.abort();
                }
                if (_this.fetch_timer) {
                    clearTimeout(_this.fetch_timer);
                }

                return true;
            });
        }


        if (show) {
            this.cross.style.display = 'inline';
        } else {
            this.cross.style.display = 'none';
    }
    }

    _reset() {
        if (this._get_options_container()) {
            this._get_options_container().remove();
        }
    }

    _get_options_container() {
        return this.container.querySelector('.woof_husky_txt-container');
    }

    _draw_pagination(answer, response) {
        let _this = this;
        let option = document.createElement('div');
        option.className = 'woof_husky_txt-option woof_husky_txt-option-pagination';
        let p_container = document.createElement('div');
        p_container.className = 'husky-pagination';
        option.appendChild(p_container);
        answer.appendChild(option);

        //+++
        let a = null;

        //algo
        //1 2 !3! 4 5
        for (let p = 1; p <= response.pagination.pages; p++) {

            if (response.pagination.pages > 12) {

                if (p === 1 || p === response.pagination.pages) {
                    this._draw_pagination_btn(response.pagination.page, p, p_container);
                    continue;
                }

                if (p === response.pagination.page ||
                        p === response.pagination.page - 1 ||
                        p === response.pagination.page - 2 ||
                        p === response.pagination.page - 3 ||
                        p === response.pagination.page + 1 ||
                        p === response.pagination.page + 2 ||
                        p === response.pagination.page + 3) {
                    this._draw_pagination_btn(response.pagination.page, p, p_container);
                }

                if (p === response.pagination.page - 4 || p === response.pagination.page + 4) {
                    a = document.createElement('i');
                    a.innerText = ' ... ';
                    p_container.appendChild(a);
                }

            } else {
                this._draw_pagination_btn(response.pagination.page, p, p_container);
            }

        }

        //+++

        if (response.pagination.page > 1) {
            a = document.createElement('a');
            a.href = '#';
            a.innerText = woof_husky_txt.prev;

            a.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                _this._search(response.pagination.page - 1, 'Paged');
                return false;
            });

            p_container.prepend(a);
        }

        if (response.pagination.page < response.pagination.pages) {
            a = document.createElement('a');
            a.href = '#';
            a.innerText = woof_husky_txt.next;

            a.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                _this._search(response.pagination.page + 1, 'Paged');
                return false;
            });

            p_container.appendChild(a);
        }
    }

    _draw_pagination_btn(page, p, p_container) {
        let a = null;
        let _this = this;

        if (parseInt(page) === p) {
            a = document.createElement('b');
            a.innerText = p;
        } else {

            a = document.createElement('a');
            a.href = '#';
            a.innerText = a.dataset.page = p;

            a.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();

                _this._search(this.dataset.page, 'Paged');

                return false;
            });

        }

        p_container.appendChild(a);
    }
}

