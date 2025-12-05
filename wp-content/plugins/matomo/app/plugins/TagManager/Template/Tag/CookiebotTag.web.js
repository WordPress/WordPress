(function () {
  return function (parameters, TagManager) {
    this.fire = function () {

      var cookiebotId = parameters.get("cookiebotId");

      (function (d, s) {
        var t = d.getElementsByTagName(s)[0], e = d.createElement(s);
        e.id = "Cookiebot";
        e.src = "https://consent.cookiebot.com/uc.js";
        e.dataset.cbid = cookiebotId;
        e.dataset.blockingmode = "auto";
        t.parentNode.insertBefore(e, t);
      })(document, "script");

    };
  };
})();
