(function () {
    return function (parameters, TagManager) {
        var Tawk_API = Tawk_API || {}, Tawk_LoadStart = new Date();
        this.fire = function () {
            var tawkToId = encodeURIComponent(parameters.get("tawkToId"));
            var tawkToWidgetId = encodeURIComponent(parameters.get("tawkToWidgetId"));
            var s1 = document.createElement("script"), s0 = document.getElementsByTagName("script")[0];
            s1.async = true;
            s1.src = 'https://embed.tawk.to/' + tawkToId + '/' + (tawkToWidgetId ? tawkToWidgetId : 'default');
            s1.charset = 'UTF-8';
            s1.setAttribute('crossorigin', '*');
            s0.parentNode.insertBefore(s1, s0);
        };
    };
})();
