(function () {
    return function (parameters, TagManager) {
        this.get = function () {
            var dataLayerName = parameters.get('dataLayerName');
            if (dataLayerName && parameters.container) {
                return parameters.container.dataLayer.get(dataLayerName);
            }
        };
    };
})();