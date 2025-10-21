var __webpack_exports__ = {};
let formSettings;
try {
  formSettings = JSON.parse(
    document.getElementById(
      "wp-script-module-data-@wordpress/block-library/form/view"
    )?.textContent
  );
} catch {
}
document.querySelectorAll("form.wp-block-form").forEach(function(form) {
  if (!formSettings || !form.action || !form.action.startsWith("mailto:")) {
    return;
  }
  const redirectNotification = (status) => {
    const urlParams = new URLSearchParams(window.location.search);
    urlParams.append("wp-form-result", status);
    window.location.search = urlParams.toString();
  };
  form.addEventListener("submit", async function(event) {
    event.preventDefault();
    const formData = Object.fromEntries(new FormData(form).entries());
    formData.formAction = form.action;
    formData._ajax_nonce = formSettings.nonce;
    formData.action = formSettings.action;
    formData._wp_http_referer = window.location.href;
    formData.formAction = form.action;
    try {
      const response = await fetch(formSettings.ajaxUrl, {
        method: "POST",
        headers: {
          "Content-Type": "application/x-www-form-urlencoded"
        },
        body: new URLSearchParams(formData).toString()
      });
      if (response.ok) {
        redirectNotification("success");
      } else {
        redirectNotification("error");
      }
    } catch (error) {
      redirectNotification("error");
    }
  });
});

