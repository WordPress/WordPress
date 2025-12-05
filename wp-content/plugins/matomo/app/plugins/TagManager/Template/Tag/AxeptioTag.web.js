(function () {
  return function (parameters, TagManager) {
    this.fire = function () {
      const clientId = parameters.get('projectId');

      window.axeptioSettings = {
        clientId,
      };

      (function (d, s) {
        const t = d.getElementsByTagName(s)[0]; const
          e = d.createElement(s);
        e.async = true;
        e.src = '//static.axept.io/sdk.js';
        t.parentNode.insertBefore(e, t);
      }(document, 'script'));

      // Support Matomo Tag Manager
      // https://support.axeptio.eu/hc/fr/articles/8610881942545-Int%C3%A9gration-Matomo-Tag-Manager
      window._axcb = window._axcb || [];
      window._mtm = window._mtm || [];
      window._axcb.push((sdk) => {
        sdk.on('cookies:complete', (choices) => {
          const axeptio_Matomo = [];
          for (const vendor in choices) {
            if (vendor != '$$completed' && choices[vendor] == true) {
              _mtm.push({ event: `axeptio_activate_${vendor}` });
              axeptio_Matomo.push(vendor);
            }
          }
          _mtm.push({ axeptio_Matomo });
        });
      });
    };
  };
}());
