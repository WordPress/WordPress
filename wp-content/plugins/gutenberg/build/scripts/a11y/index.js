"use strict";
var wp;
(wp ||= {}).a11y = (() => {
  var __create = Object.create;
  var __defProp = Object.defineProperty;
  var __getOwnPropDesc = Object.getOwnPropertyDescriptor;
  var __getOwnPropNames = Object.getOwnPropertyNames;
  var __getProtoOf = Object.getPrototypeOf;
  var __hasOwnProp = Object.prototype.hasOwnProperty;
  var __commonJS = (cb, mod) => function __require() {
    return mod || (0, cb[__getOwnPropNames(cb)[0]])((mod = { exports: {} }).exports, mod), mod.exports;
  };
  var __export = (target, all) => {
    for (var name in all)
      __defProp(target, name, { get: all[name], enumerable: true });
  };
  var __copyProps = (to, from, except, desc) => {
    if (from && typeof from === "object" || typeof from === "function") {
      for (let key of __getOwnPropNames(from))
        if (!__hasOwnProp.call(to, key) && key !== except)
          __defProp(to, key, { get: () => from[key], enumerable: !(desc = __getOwnPropDesc(from, key)) || desc.enumerable });
    }
    return to;
  };
  var __toESM = (mod, isNodeMode, target) => (target = mod != null ? __create(__getProtoOf(mod)) : {}, __copyProps(
    // If the importer is in node compatibility mode or this is not an ESM
    // file that has been converted to a CommonJS file using a Babel-
    // compatible transform (i.e. "__esModule" has not been set), then set
    // "default" to the CommonJS "module.exports" for node compatibility.
    isNodeMode || !mod || !mod.__esModule ? __defProp(target, "default", { value: mod, enumerable: true }) : target,
    mod
  ));
  var __toCommonJS = (mod) => __copyProps(__defProp({}, "__esModule", { value: true }), mod);

  // package-external:@wordpress/dom-ready
  var require_dom_ready = __commonJS({
    "package-external:@wordpress/dom-ready"(exports, module) {
      module.exports = window.wp.domReady;
    }
  });

  // package-external:@wordpress/i18n
  var require_i18n = __commonJS({
    "package-external:@wordpress/i18n"(exports, module) {
      module.exports = window.wp.i18n;
    }
  });

  // packages/a11y/build-module/index.js
  var index_exports = {};
  __export(index_exports, {
    setup: () => setup,
    speak: () => speak
  });
  var import_dom_ready = __toESM(require_dom_ready());

  // packages/a11y/build-module/script/add-container.js
  function addContainer(ariaLive = "polite") {
    const container = document.createElement("div");
    container.id = `a11y-speak-${ariaLive}`;
    container.className = "a11y-speak-region";
    container.setAttribute(
      "style",
      "position:absolute;margin:-1px;padding:0;height:1px;width:1px;overflow:hidden;clip-path:inset(50%);border:0;word-wrap:normal !important;"
    );
    container.setAttribute("aria-live", ariaLive);
    container.setAttribute("aria-relevant", "additions text");
    container.setAttribute("aria-atomic", "true");
    const { body } = document;
    if (body) {
      body.appendChild(container);
    }
    return container;
  }

  // packages/a11y/build-module/script/add-intro-text.js
  var import_i18n = __toESM(require_i18n());
  function addIntroText() {
    const introText = document.createElement("p");
    introText.id = "a11y-speak-intro-text";
    introText.className = "a11y-speak-intro-text";
    introText.textContent = (0, import_i18n.__)("Notifications");
    introText.setAttribute(
      "style",
      "position:absolute;margin:-1px;padding:0;height:1px;width:1px;overflow:hidden;clip-path:inset(50%);border:0;word-wrap:normal !important;"
    );
    introText.setAttribute("hidden", "");
    const { body } = document;
    if (body) {
      body.appendChild(introText);
    }
    return introText;
  }

  // packages/a11y/build-module/shared/clear.js
  function clear() {
    const regions = document.getElementsByClassName("a11y-speak-region");
    const introText = document.getElementById("a11y-speak-intro-text");
    for (let i = 0; i < regions.length; i++) {
      regions[i].textContent = "";
    }
    if (introText) {
      introText.setAttribute("hidden", "hidden");
    }
  }

  // packages/a11y/build-module/shared/filter-message.js
  var previousMessage = "";
  function filterMessage(message) {
    message = message.replace(/<[^<>]+>/g, " ");
    if (previousMessage === message) {
      message += "\xA0";
    }
    previousMessage = message;
    return message;
  }

  // packages/a11y/build-module/shared/index.js
  function speak(message, ariaLive) {
    clear();
    message = filterMessage(message);
    const introText = document.getElementById("a11y-speak-intro-text");
    const containerAssertive = document.getElementById(
      "a11y-speak-assertive"
    );
    const containerPolite = document.getElementById("a11y-speak-polite");
    if (containerAssertive && ariaLive === "assertive") {
      containerAssertive.textContent = message;
    } else if (containerPolite) {
      containerPolite.textContent = message;
    }
    if (introText) {
      introText.removeAttribute("hidden");
    }
  }

  // packages/a11y/build-module/index.js
  function setup() {
    const introText = document.getElementById("a11y-speak-intro-text");
    const containerAssertive = document.getElementById(
      "a11y-speak-assertive"
    );
    const containerPolite = document.getElementById("a11y-speak-polite");
    if (introText === null) {
      addIntroText();
    }
    if (containerAssertive === null) {
      addContainer("assertive");
    }
    if (containerPolite === null) {
      addContainer("polite");
    }
  }
  (0, import_dom_ready.default)(setup);
  return __toCommonJS(index_exports);
})();
//# sourceMappingURL=index.js.map
