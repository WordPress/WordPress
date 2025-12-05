(function () {
    return function (parameters, TagManager) {
        this.fire = function () {
            var bingAdID = parameters.get('bingAdID');
            if (bingAdID) {
                (function () {
                    var init, scriptEl, firstScript;
                    window.uetq = window.uetq || [];
                    init = function () {
                        var obj = {ti: bingAdID};
                        obj.q = window.uetq;
                        window.uetq = new UET(obj);
                        window.uetq.push("pageLoad")
                    };
                    scriptEl = document.createElement("script");
                    scriptEl.src = "//bat.bing.com/bat.js";
                    scriptEl.async = true;
                    scriptEl.onload = scriptEl.onreadystatechange = function () {
                        var state = this.readyState;
                        if (!state || state === "loaded" || state === "complete") {
                            init();
                            scriptEl.onload = scriptEl.onreadystatechange = null
                        }
                    };
                    firstScript = document.getElementsByTagName("script")[0];
                    firstScript.parentNode.insertBefore(scriptEl, firstScript)
                })();
            }
        };
    };
})();
