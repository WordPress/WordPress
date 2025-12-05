(function () {
    return function (parameters, TagManager) {
        var setup = {};
        var isLibLoaded = false;
        this.fire = function () {
            if (!isLibLoaded) {
                isLibLoaded = true;
                !function(f,b,e,v,n,t,s)
                {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
                n.callMethod.apply(n,arguments):n.queue.push(arguments)};
                if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
                n.queue=[];t=b.createElement(e);t.async=!0;
                t.src=v;s=b.getElementsByTagName(e)[0];
                s.parentNode.insertBefore(t,s)}(parameters.window,parameters.document,'script',
                'https://connect.facebook.net/en_US/fbevents.js');
            }

            var pixelId = parameters.get('pixelId');
            if (!(pixelId in setup)) {
                setup[pixelId] = true;
                fbq('init', pixelId);
            }

            fbq('track', 'PageView');
        };
    };
})();