(function () {
    return function (parameters, TagManager) {
        var setup = {};
        var isLibLoaded = false;
        this.fire = function () {
            if (!isLibLoaded) {
                isLibLoaded = true;
                (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
                    (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
                    m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
                })(parameters.window,parameters.document,'script','https://www.google-analytics.com/analytics.js','ga');
            }

            var propertyId = parameters.get('propertyId');
            if (!(propertyId in setup)) {
                setup[propertyId] = true;
                ga('create', parameters.get('propertyId'), 'auto');
            }

            ga('set', 'anonymizeIp', true);
            ga('send', 'pageview');
        };
    };
})();