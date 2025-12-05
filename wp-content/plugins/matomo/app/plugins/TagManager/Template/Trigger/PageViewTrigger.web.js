(function () {
    return function (parameters, TagManager) {
        this.setUp = function (triggerEvent) {
            triggerEvent({event: 'mtm.PageView'});
        };
    };
})();