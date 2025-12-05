(function () {
    return function (parameters, TagManager) {

        this.setUp = function (triggerEvent) {
            var windowAlias = parameters.window;
            var onError = windowAlias.onerror;

            TagManager.utils.setMethodWrapIfNeeded(parameters.window, 'onerror', function (message, url, linenumber, column, error) {
                triggerEvent({event: 'mtm.JavaScriptError', 'mtm.errorMessage': message, 'mtm.errorUrl': url, 'mtm.errorLine': linenumber});

                return false;
            });
        };
    };
})();