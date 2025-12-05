(function () {
  return function (parameters, TagManager) {
    this.fire = function () {

      var consentAction = parameters.get("consentAction")[0];
      var consentTypes = parameters.get("consentTypes");

      var typesObject = {};
      consentTypes.forEach(function (type) {
        if (type.consent_type && type.consent_state) {
          typesObject[type.consent_type] = type.consent_state;
        }
      });

      window.dataLayer = window.dataLayer || [];
      function gtag() {
        window.dataLayer.push(arguments);
      }
      gtag("consent", consentAction, typesObject);

    };
  };
})();
