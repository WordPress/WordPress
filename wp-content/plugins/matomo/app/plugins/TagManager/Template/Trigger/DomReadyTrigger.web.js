(function () {
    return function (parameters, TagManager) {
        this.setUp = function (triggerEvent) {
            TagManager.dom.onReady(function () {
                triggerEvent({event: 'DOMReady'});
            });
        };
    };
})();