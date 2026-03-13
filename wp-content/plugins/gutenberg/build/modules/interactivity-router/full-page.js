// packages/interactivity-router/build-module/full-page.js
var isValidLink = (ref) => ref && ref instanceof window.HTMLAnchorElement && ref.href && (!ref.target || ref.target === "_self") && ref.origin === window.location.origin && !ref.pathname.startsWith("/wp-admin") && !ref.pathname.startsWith("/wp-login.php") && !ref.getAttribute("href").startsWith("#") && !new URL(ref.href).searchParams.has("_wpnonce");
var isValidEvent = (event) => event && event.button === 0 && // Left clicks only.
!event.metaKey && // Open in new tab (Mac).
!event.ctrlKey && // Open in new tab (Windows).
!event.altKey && // Download.
!event.shiftKey && !event.defaultPrevented;
document.addEventListener("click", async (event) => {
  const ref = event.target.closest("a");
  if (isValidLink(ref) && isValidEvent(event)) {
    event.preventDefault();
    const { actions } = await import("@wordpress/interactivity-router");
    actions.navigate(ref.href);
  }
});
document.addEventListener(
  "mouseenter",
  async (event) => {
    if (event.target?.nodeName === "A") {
      const ref = event.target.closest("a");
      if (isValidLink(ref) && isValidEvent(event)) {
        const { actions } = await import("@wordpress/interactivity-router");
        actions.prefetch(ref.href);
      }
    }
  },
  true
);
//# sourceMappingURL=full-page.js.map
