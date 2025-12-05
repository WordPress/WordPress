(function () {
  return function (parameters, TagManager) {
    this.fire = function () {

      var googleTagId = parameters.get("googleTagId");

      var gtmScript = document.createElement("script");
      gtmScript.src = `https://www.googletagmanager.com/gtag/js?id=${googleTagId}`;
      gtmScript.async = true;

      var gaScript = document.createElement('script');
      gaScript.textContent = `
              window.dataLayer = window.dataLayer || [];
              function gtag(){dataLayer.push(arguments);}
              gtag('js', new Date());
              gtag('config', '${googleTagId}');
            `;

      document.head.appendChild(gtmScript);
      document.head.appendChild(gaScript);

    };
  };
})();
