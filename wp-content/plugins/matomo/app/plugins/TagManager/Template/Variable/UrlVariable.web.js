(function () {
    return function (parameters, TagManager) {
        this.get = function () {
            var urlPart = parameters.get('urlPart', 'href');
            var loc = parameters.window.location;

            return TagManager.url.parseUrl(loc.href, urlPart);
        };
    };
})();