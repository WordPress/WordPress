(function () {
    return function (parameters, TagManager) {
        this.get = function () {
            var urlPart = parameters.get('urlPart', 'href');
            var urlReferrer = parameters.get('document.referrer');
            if (!urlReferrer || !urlPart) {
                return;
            }
            return TagManager.url.parseUrl(urlReferrer, urlPart);
        };
    };
})();

