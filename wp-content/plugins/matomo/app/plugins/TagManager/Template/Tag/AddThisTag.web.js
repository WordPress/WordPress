(function () {
    return function (parameters, TagManager) {
        this.fire = function () {
            var parentSelector = parameters.get('AddThisParentSelector');
            if (parentSelector) {
                var parent = TagManager.dom.bySelector(parentSelector);
                if (parent) {
                    var div = document.createElement('div');
                    div.className = 'addthis_inline_share_toolbox';
                    parent[0].appendChild(div)
                }
            }

            var siteId = parameters.get('AddThisPubId');
            if (siteId) {
                var script = document.createElement('script');
                var s = document.getElementsByTagName('script')[0];
                script.async = true;
                script.defer = true;
                script.type = 'text/javascript';
                script.src = '//s7.addthis.com/js/300/addthis_widget.js#pubid=' + siteId;
                s.parentNode.insertBefore(script, s);
            }
        };
    };
})();
