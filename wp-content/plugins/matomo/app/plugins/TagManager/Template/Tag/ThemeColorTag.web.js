(function () {
    return function (parameters, TagManager) {
        this.fire = function () {
            var themeColor = parameters.get('themeColor');
            if (themeColor) {
                var metaTag = document.querySelector('meta[name="theme-color"]');
                if (!metaTag) {
                    metaTag = document.createElement('meta');
                    metaTag.name = "theme-color";
                    document.getElementsByTagName('head')[0].appendChild(metaTag);
                }
                metaTag.content = themeColor;
            }
        };
    };
})();
