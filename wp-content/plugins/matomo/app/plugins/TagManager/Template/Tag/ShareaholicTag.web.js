(function () {
    return function (parameters, TagManager) {
        this.fire = function () {
            var siteId = parameters.get('shareaholicSiteId');
            if (siteId) {
                // should result in a <script> like this:
                // &lt;script type="text/javascript" data-cfasync="false" src="//apps.shareaholic.com/assets/pub/shareaholic.js" data-shr-siteid="[SITEID]" async="async"&gt;&lt;/script&gt;

                var script = document.createElement('script');
                var s = document.getElementsByTagName('script')[0];
                script.type = 'text/javascript';
                script.async = true;
                script.setAttribute('data-cfasync', 'false');
                script.setAttribute('data-shr-siteid', siteId);
                script.src = '//apps.shareaholic.com/assets/pub/shareaholic.js';
                s.parentNode.insertBefore(script, s);
                var inPageApp = parameters.get('shareaholicInPageApp');
                if (inPageApp) {
                    var AppID = parameters.get('shareaholicAppId');
                    var parentSelector = parameters.get('shareaholicParentSelector');
                    if (inPageApp !== 'total_share_count' && !AppID) {
                        TagManager.debug.error('this In-Page App requires an AppID');
                        return false;
                    }
                    if (!parentSelector) {
                        TagManager.debug.error('you have to specify a Parent Selector to place the In-Page App onto your website');
                        return false;
                    }
                    var parent = TagManager.dom.bySelector(parentSelector);
                    if (parent) {
                        var div = document.createElement('div');
                        div.className = 'shareaholic-canvas';
                        div.setAttribute('data-app', inPageApp);
                        if (AppID) {
                            div.setAttribute('data-app-id', AppID);
                        }
                        parent[0].appendChild(div)
                    }
                }
            }
        };
    };
})();
