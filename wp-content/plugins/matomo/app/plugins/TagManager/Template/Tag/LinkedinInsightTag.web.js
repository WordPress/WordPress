(function () {
    return function (parameters, TagManager) {
        this.fire = function () {
            var partnerId = parameters.get('partnerId');
            var conversionId = parameters.get('conversionId');
            if (partnerId) {
                window._linkedin_partner_id = partnerId;
                window._linkedin_data_partner_ids = window._linkedin_data_partner_ids || [];
                window._linkedin_data_partner_ids.push(window._linkedin_partner_id);
                (function(){
                    var s = document.getElementsByTagName("script")[0];
                    var b = document.createElement("script");
                    b.type = "text/javascript";b.async = true;
                    b.src = "https://snap.licdn.com/li.lms-analytics/insight.min.js";
                    if (conversionId) {
                        b.onload = function() {
                            window.lintrk('track', {conversion_id: conversionId});
                        }
                    }
                    s.parentNode.insertBefore(b, s);
                })();
            }
        };
    };
})();
