(function () {
    return function (parameters, TagManager) {
        this.fire = function () {
            var imageUrl = parameters.get('customImageSrc');
            if (!imageUrl) {
                return;
            }
            var cacheBuster = parameters.get('cacheBusterEnabled', false);

            if (cacheBuster) {
                if (imageUrl.indexOf('?') === -1) {
                    imageUrl += '?';
                } else {
                    imageUrl += '&';
                }
                imageUrl += 'mtmcb=' + parseInt(Math.random() * 90000000, 10);
            }

            var doc = parameters.document;
            var image = doc.createElement('img');
            image.style.display = 'none';
            image.src = imageUrl;
            doc.body.appendChild(image);
        };
    };
})();