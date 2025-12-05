(function () {
    return function (parameters, TagManager) {
        this.setUp = function (triggerEvent) {
            var triggered = false;
            TagManager.dom.addEventListener(parameters.window, 'beforeunload', function () {
                if (triggered) {
                    return;
                }
                triggered = true;
                triggerEvent({event: 'WindowUnload'});
            });
        };
    };
})();
