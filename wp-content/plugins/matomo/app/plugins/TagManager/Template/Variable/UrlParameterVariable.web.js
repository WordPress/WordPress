(function () {
    return function (parameters, TagManager) {
        this.get = function () {
            var name = parameters.get('parameterName');
            return TagManager.url.getQueryParameter(name, parameters.window.location.search);
        };
    };
})();