(function () {
    return function (parameters, TagManager) {
        this.get = function () {
            var unit = parameters.get('unit', 'ms');
            var now = new Date().getTime();
            var loadTime = TagManager.dataLayer.get('mtm.mtmScriptLoadedTime');
            if (!loadTime) {
                loadTime = TagManager.dataLayer.get('mtm.startTime');
            }
            if (!loadTime) {
                var win = parameters.window;
                if (TagManager.utils.isObject(win.performance) && win.performance.timing && win.performance.timing) {
                    loadTime = win.performance.timing.loadEventStart;
                }
            }
            if (!loadTime) {
                return;
            }
            if (unit === 's') {
                now = now / 1000;
                loadTime = loadTime / 1000;
            } else if (unit === 'm') {
                now = now / 1000 / 60;
                loadTime = loadTime / 1000 / 60;
            }

            return parseInt(Math.round(now - loadTime), 10);
        };
    };
})();