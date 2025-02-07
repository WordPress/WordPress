var __webpack_exports__ = {};
/* wp:polyfill */
let formSettings;
try {
  formSettings = JSON.parse(document.getElementById('wp-script-module-data-@wordpress/block-library/form/view')?.textContent);
} catch {}

// eslint-disable-next-line eslint-comments/disable-enable-pair
/* eslint-disable no-undef */
document.querySelectorAll('form.wp-block-form').forEach(function (form) {
  // Bail If the form settings not provided or the form is not using the mailto: action.
  if (!formSettings || !form.action || !form.action.startsWith('mailto:')) {
    return;
  }
  const redirectNotification = status => {
    const urlParams = new URLSearchParams(window.location.search);
    urlParams.append('wp-form-result', status);
    window.location.search = urlParams.toString();
  };

  // Add an event listener for the form submission.
  form.addEventListener('submit', async function (event) {
    event.preventDefault();
    // Get the form data and merge it with the form action and nonce.
    const formData = Object.fromEntries(new FormData(form).entries());
    formData.formAction = form.action;
    formData._ajax_nonce = formSettings.nonce;
    formData.action = formSettings.action;
    formData._wp_http_referer = window.location.href;
    formData.formAction = form.action;
    try {
      const response = await fetch(formSettings.ajaxUrl, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: new URLSearchParams(formData).toString()
      });
      if (response.ok) {
        redirectNotification('success');
      } else {
        redirectNotification('error');
      }
    } catch (error) {
      redirectNotification('error');
    }
  });
});

