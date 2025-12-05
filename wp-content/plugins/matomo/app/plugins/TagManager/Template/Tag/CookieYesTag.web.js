(function () {
  return function (parameters, TagManager) {
    this.fire = function () {
      const websiteKey = parameters.get('cookieYesWebsiteKey');

      let src = 'https://cdn-cookieyes.com/client_data/';
      src += websiteKey;
      src += '/script.js';

      (function (d, s) {
        const t = d.getElementsByTagName(s)[0]; const
          e = d.createElement(s);
        e.id = 'cookieyes';
        e.type = 'text/javascript';
        e.src = src;
        t.parentNode.insertBefore(e, t);
      }(document, 'script'));
    };
  };
}());
