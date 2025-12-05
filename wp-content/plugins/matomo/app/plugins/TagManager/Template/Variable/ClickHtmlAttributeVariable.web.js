(function () {
  return function (parameters, TagManager) {

    this.get = function () {
      var htmlAttribute = parameters.get("htmlAttribute");

      var event = TagManager.dataLayer.events.at(-1);

      if (event["mtm.clickElement"] && htmlAttribute && event["mtm.clickElement"].hasAttribute(htmlAttribute)) {
        return event["mtm.clickElement"].getAttribute(htmlAttribute);
      }

    };
  };
})();
