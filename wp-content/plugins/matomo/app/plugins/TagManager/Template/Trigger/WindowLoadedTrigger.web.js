(function () {
    return function (parameters, TagManager) {
        this.setUp = function (triggerEvent) {
            TagManager.dom.onLoad(function () {
                triggerEvent({event: 'WindowLoad'});
            });
        };
    };
})();