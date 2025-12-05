(function () {
    return function (parameters, TagManager) {
        this.fire = function () {
            var zendeskChatId = parameters.get("zendeskChatId");
            if (zendeskChatId) {
                window.$zopim || (function () {
                    var z = $zopim = function (c) {
                        z._.push(c)
                    };
                    var script = z.s = document.createElement("script");
                    var e = document.getElementsByTagName("script")[0];
                    z.set = function (o) {
                        z.set._.push(o)
                    };
                    z._ = [];
                    z.set._ = [];
                    script.async = true;
                    script.setAttribute("charset", "utf-8");
                    script.src = "https://v2.zopim.com/?" + zendeskChatId;
                    z.t = +new Date;
                    script.type = "text/javascript";
                    e.parentNode.insertBefore(script, e)
                })();
            }
        };
    };
})();
