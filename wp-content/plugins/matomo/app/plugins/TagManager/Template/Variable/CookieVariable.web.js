(function () {
    return function (parameters, TagManager) {

        this.get = function () {
            var cookieName = parameters.get('cookieName');
            if (!cookieName) {
                return;
            }
            var cookie = parameters.get('document.cookie');
            if (!cookie) {
                return;
            }

            var cookiePattern = new RegExp('(^|;)[ ]*' + cookieName + '=([^;]*)');
            var cookieMatch = cookiePattern.exec(cookie);

            if (parameters.get('uriDecode', false) && cookieMatch) {
                return TagManager.url.decodeSafe(cookieMatch[2]);
            } else if (cookieMatch) {
                return cookieMatch[2];
            }
        };
    };
})();