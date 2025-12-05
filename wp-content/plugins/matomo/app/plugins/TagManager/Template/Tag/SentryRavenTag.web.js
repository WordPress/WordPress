(function () {
    return function (parameters, TagManager) {
        this.fire = function () {
            var dsn = parameters.get('sentryDSN');
            if (dsn) {
                TagManager.dom.loadScriptUrl('https://cdn.ravenjs.com/3.26.4/raven.min.js', {
                    onload: function () {
                        Raven.config(dsn).install();
                    }
                });
            }
        };
    };
})();
