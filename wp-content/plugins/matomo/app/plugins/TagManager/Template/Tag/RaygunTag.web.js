(function () {
    return function (parameters, TagManager) {
        this.fire = function () {
            var apiKey = parameters.get('raygunApiKey');
            if (apiKey) {
                (function (wind, doc, scriptTag, url, script, raygunJs, firstScript, errorhandler) {
                    window.RaygunObject = 'rg4js';
                    window.rg4js = window.rg4js || function () {
                        (window.rg4js.o = window.rg4js.o || []).push(arguments)
                    };
                    raygunJs = document.createElement('script');
                    firstScript = document.getElementsByTagName('script')[0];
                    raygunJs.async = true;
                    raygunJs.src = '//cdn.raygun.io/raygun4js/raygun.min.js';
                    firstScript.parentNode.insertBefore(raygunJs, firstScript);
                    errorhandler = window.onerror;
                    window.onerror = function (msg, url, line, col, err) {
                        if (errorhandler) {
                            errorhandler(msg, url, line, col, err)
                        }
                        if (!err) {
                            err = new Error(msg)
                        }
                        window.rg4js.q = window.rg4js.q || [];
                        window.rg4js.q.push({e: err})
                    }
                }(window, document, 'script', '//cdn.raygun.io/raygun4js/raygun.min.js', 'rg4js'));

                rg4js('apiKey', apiKey);
                rg4js('enableCrashReporting', true);
                if (parameters.get('raygunEnablePulse')) {
                    rg4js('enablePulse', true);
                }
            }
        };
    };
})();
