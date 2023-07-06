'use strict';
import Helper from '../helper.js';
import Row from './row.js';
//17-12-2022
export default class Table {
    constructor(data, wrapper, selected_template = 1, sort_by_title = false) {
        this.rows = [];
        this.data = data;
        this.wrapper = wrapper;
        this.selected_template = selected_template;
        this.sort_by_title = sort_by_title;
        this.create();
        this.draw();
        this.instance_key = 'woof-sd-table';//!! for attaching single document events

        Helper.addSingleEventListener('woof-sd-data-table-row-delete', this, e => {
            if (this.rows.length > 0) {
                let tmp = [];
                for (let i = 0; i < this.rows.length; i++) {
                    if (this.rows[i].container.isConnected) {
                        tmp.push(this.rows[i]);
                    }
                }

                this.rows = tmp;
            }
        });
    }

    create() {
        this.container = Helper.create_element('data-table');
        if (this.data.class) {
            this.container.className = this.data.class;
        }
    }

    header() {
        if (this.data.header) {
            new Row(this, this.data.header, 0, this.container, true);
            this.wrapper.appendChild(this.container);
        }
    }

    footer() {
        //this.header();
    }

    draw() {
        this.header();

        if (Object.values(this.data.rows).length > 0) {

            let rows = this.data.rows;

            if (this.sort_by_title) {
                let tmp = {};
                for (const [id, value] of Object.entries(rows)) {
                    tmp[value.title.value] = id;
                }

                let titles = Object.keys(tmp).sort();
                for (let i = 0; i < titles.length; i++) {
                    this.rows.push(new Row(this, rows[tmp[titles[i]]], tmp[titles[i]], this.container));
                }

            } else {
                for (const [id, value] of Object.entries(rows)) {
                    this.rows.push(new Row(this, value, id, this.container));
                }
            }


        }

        this.footer();
    }

    createRow(data, id, append = true) {
        let row = new Row(this, data, id, this.container, false, append);
        let res = {};

        for (let i = 0; i < row.data.length; i++) {
            res[row.data[i].key] = row.data[i];
        }

        this.rows.push(row);
        return res;
    }

}



