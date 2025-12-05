(function () {
    return function (parameters, TagManager) {
        this.get = function () {
            return parameters.get('constantValue');
        };
    };
})();