(function () {
    return function (parameters, TagManager) {

        this.get = function () {
            var elements = TagManager.dom.bySelector('link[rel="canonical"]');
            if (elements && elements[0]) {
                return elements[0].href;
            }
        };
    };
})();