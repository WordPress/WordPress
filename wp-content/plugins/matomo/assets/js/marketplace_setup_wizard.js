/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 * @package matomo
 */

window.jQuery(document).ready(function ($) {
  function pollForPluginActivation() {
    $('.wizard-waiting-for').show();

    var interval = setInterval(function () {
      $.post(mtmMarketplaceWizardAjax.ajax_url, {
        _ajax_nonce: mtmMarketplaceWizardAjax.is_active_nonce,
        action: 'matomo_is_marketplace_active',
      }, function (data) {
        if (data.active) {
          $('.wizard-waiting-for').hide();
          $('.wizard-reloading').show();

          window.location.reload();
          clearInterval(interval);
        }
      });
    }, 2000);
  }

  function activateMarketplace(e) {
    e.preventDefault();
    e.stopPropagation();

    $('.wizard-waiting-for').show();

    $.post(mtmMarketplaceWizardAjax.ajax_url, {
      _ajax_nonce: mtmMarketplaceWizardAjax.activate_nonce,
      action: 'matomo_activate_marketplace',
    }, pollForPluginActivation);
  }

  if (typeof mtmMarketplaceWizardAjax !== 'undefined' && mtmMarketplaceWizardAjax.ajax_url) {
    $('.matomo-marketplace-wizard .open-plugin-upload').on('click', pollForPluginActivation);
    $('.matomo-marketplace-wizard .activate-plugin').on('click', activateMarketplace);
  }
});
