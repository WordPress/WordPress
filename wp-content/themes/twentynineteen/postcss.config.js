var postcssFocusWithin = require('postcss-focus-within');
var autoprefixer = require('autoprefixer');

module.exports = {
    plugins: [
        postcssFocusWithin({
            disablePolyfillReadyClass: true
        }),
        autoprefixer()
    ]
};
