(function () {
    var libLoaded = false;
    var libAvailable = false;

    function loadVwoCode(accountId)
    {
        if (libAvailable || typeof window._vwo_code === 'object') {
            libAvailable = true;
            libLoaded = true;
            return;
        }
        if (!libLoaded) {
            
            libLoaded = true;
        
            window._vwo_code = (function() {
                var settings_tolerance = 2000,
                    library_tolerance = 2500,
                    isFinished = false,
                    doc = document;
                return {
                    use_existing_jquery: function() {
                        return false;
                    },
                    library_tolerance: function() {
                        return library_tolerance;
                    },
                    finish: function() {
                        if (!isFinished) {
                            isFinished = true;
                            libAvailable = true;
                        }
                    },
                    finished: function() {
                        return isFinished;
                    },
                    load: function(scriptUrl) {
                        var scriptElement = doc.createElement('script');
                        scriptElement.src = scriptUrl;
                        scriptElement.type = 'text/javascript';
                        scriptElement.innerText;
                        scriptElement.onerror = function() {
                            window._vwo_code.finish();
                        };
                        doc.getElementsByTagName('head')[0].appendChild(scriptElement);
                    },
                    init: function() {
                        settings_timer = setTimeout('window._vwo_code.finish()', settings_tolerance);
                        this.load('https://dev.visualwebsiteoptimizer.com/j.php?a=' + accountId + '&u=' + encodeURIComponent(doc.URL) + '&r=' + Math.random());
                        return settings_timer;
                    }
                };
            }());
            
            window._vwo_settings_timer = window._vwo_code.init();
        }
    }

    return function (parameters, TagManager) {
        this.fire = function () {
            var accountId = parameters.get('accountId');
            if (accountId) {
                loadVwoCode(accountId);
            }
        };
    };
})();