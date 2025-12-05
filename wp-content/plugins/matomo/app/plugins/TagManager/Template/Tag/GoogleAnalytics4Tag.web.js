(function () {
    return function (parameters, TagManager) {
        this.fire = function () {

            var measurementId = parameters.get("measurementId");

            var gtmScript = document.createElement("script");
            gtmScript.src = `https://www.googletagmanager.com/gtag/js?id=${measurementId}`;
            gtmScript.async = true;

            var gaScript = document.createElement('script');
            gaScript.textContent = `
              window.dataLayer = window.dataLayer || [];
              function gtag(){dataLayer.push(arguments);}
              gtag('js', new Date());
              gtag('config', '${measurementId}');
            `;

          document.head.appendChild(gtmScript);
          document.head.appendChild(gaScript);

        };
    };
})();
