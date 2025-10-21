/******/ var __webpack_modules__ = ({

/***/ 438:
/***/ ((module) => {

module.exports = import("@wordpress/interactivity-router");;

/***/ })

/******/ });
/************************************************************************/
/******/ // The module cache
/******/ var __webpack_module_cache__ = {};
/******/ 
/******/ // The require function
/******/ function __webpack_require__(moduleId) {
/******/ 	// Check if module is in cache
/******/ 	var cachedModule = __webpack_module_cache__[moduleId];
/******/ 	if (cachedModule !== undefined) {
/******/ 		return cachedModule.exports;
/******/ 	}
/******/ 	// Create a new module (and put it into the cache)
/******/ 	var module = __webpack_module_cache__[moduleId] = {
/******/ 		// no module.id needed
/******/ 		// no module.loaded needed
/******/ 		exports: {}
/******/ 	};
/******/ 
/******/ 	// Execute the module function
/******/ 	__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 
/******/ 	// Return the exports of the module
/******/ 	return module.exports;
/******/ }
/******/ 
/************************************************************************/
var __webpack_exports__ = {};
const isValidLink = (ref) => ref && ref instanceof window.HTMLAnchorElement && ref.href && (!ref.target || ref.target === "_self") && ref.origin === window.location.origin && !ref.pathname.startsWith("/wp-admin") && !ref.pathname.startsWith("/wp-login.php") && !ref.getAttribute("href").startsWith("#") && !new URL(ref.href).searchParams.has("_wpnonce");
const isValidEvent = (event) => event && event.button === 0 && // Left clicks only.
!event.metaKey && // Open in new tab (Mac).
!event.ctrlKey && // Open in new tab (Windows).
!event.altKey && // Download.
!event.shiftKey && !event.defaultPrevented;
document.addEventListener("click", async (event) => {
  const ref = event.target.closest("a");
  if (isValidLink(ref) && isValidEvent(event)) {
    event.preventDefault();
    const { actions } = await Promise.resolve(/* import() */).then(__webpack_require__.bind(__webpack_require__, 438));
    actions.navigate(ref.href);
  }
});
document.addEventListener(
  "mouseenter",
  async (event) => {
    if (event.target?.nodeName === "A") {
      const ref = event.target.closest("a");
      if (isValidLink(ref) && isValidEvent(event)) {
        const { actions } = await Promise.resolve(/* import() */).then(__webpack_require__.bind(__webpack_require__, 438));
        actions.prefetch(ref.href);
      }
    }
  },
  true
);

