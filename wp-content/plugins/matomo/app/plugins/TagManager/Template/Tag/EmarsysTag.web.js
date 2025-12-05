(function () {
    var libLoaded = false;
    
    function loadEmarsysCode(merchantId)
    {
        if (libLoaded || typeof window.ScarabQueue === 'object') {
            libLoaded = true;
            return;
        }
        if (!libLoaded) {
            
            libLoaded = true;
        
            window.ScarabQueue = window.ScarabQueue || [];
            
            (function(id) {
              if (document.getElementById(id)) return;
              var js = document.createElement('script');
              js.id = id;
              js.src = 'https://cdn.scarabresearch.com/js/' + merchantId + '/scarab-v2.js';
              var fs = document.getElementsByTagName('script')[0];
              fs.parentNode.insertBefore(js, fs);
            })('scarab-js-api');

        }
    }

    return function (parameters, TagManager) {
        this.fire = function () {
            
            loadEmarsysCode(parameters.get('merchantId'));
            
            var category = parameters.get('commandCategory');
            if (category) {
                window.ScarabQueue.push(['category', category]);
            }
            
            var view = parameters.get('commandView');
            if (view) {
                window.ScarabQueue.push(['view', view]);
            }
            
            var tag = parameters.get('commandTag');
            if (tag) {
                window.ScarabQueue.push(['tag', tag]);
            }
            
            if (parameters.get('commandGo')) {
                window.ScarabQueue.push(['go']);
            }
        };
    };
})();