(function () {
    return function (parameters, TagManager) {
        this.fire = function () {
            var pingdomId = parameters.get("pingdomROMId");
            var _prum = [['id', pingdomId],
                ['mark', 'firstbyte', (new Date()).getTime()]];
            (function() {
                var s = document.getElementsByTagName('script')[0]
                    , p = document.createElement('script');
                p.async = 'async';
                p.src = '//rum-static.pingdom.net/prum.min.js';
                s.parentNode.insertBefore(p, s);
            })();
        };
    };
})();
