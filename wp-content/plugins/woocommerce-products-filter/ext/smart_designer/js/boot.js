'use strict';

import Helper from './helper.js';
import SD from './sd.js';
//02-11-2022
addEventListener('DOMContentLoaded', function (e) {
    Helper.ajax('woof_sd_boot', {}, data => new SD(data));
});

