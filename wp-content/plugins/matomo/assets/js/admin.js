/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 * @package matomo
 */

window.jQuery(document).ready(function ($) {
  // referral notice dismiss
  if (typeof mtmReferralDismissNoticeAjax !== 'undefined' && mtmReferralDismissNoticeAjax.ajax_url) {
    $(document).on( 'click', '#matomo-referral .notice-dismiss', function () {
      $.post(mtmReferralDismissNoticeAjax.ajax_url, {
        _ajax_nonce: mtmReferralDismissNoticeAjax.nonce,
        action: 'matomo_referral_dismiss_admin_notice',
      });
    });

    $(document).on( 'click', '#matomo-referral .matomo-dismiss-forever', function () {
      $.post(mtmReferralDismissNoticeAjax.ajax_url, {
        _ajax_nonce: mtmReferralDismissNoticeAjax.nonce,
        action: 'matomo_referral_dismiss_admin_notice',
        forever: '1',
      });
    });
  }

  // system report error dismiss
  if (typeof mtmSystemReportErrorNoticeAjax !== 'undefined' && mtmSystemReportErrorNoticeAjax.ajax_url) {
    $(document).on( 'click', '#matomo-systemreporterrors .notice-dismiss', function () {
      $.post(mtmSystemReportErrorNoticeAjax.ajax_url, {
        _ajax_nonce: mtmSystemReportErrorNoticeAjax.nonce,
        action: 'matomo_system_report_error_dismissed',
      });
    });
  }

  // scheduled task error dismiss
  if (typeof mtmScheduledTaskErrorAjax !== 'undefined' && mtmScheduledTaskErrorAjax.ajax_url) {
    $('body').on('click', '.matomo-cron-error .notice-dismiss', function (e) {
      $.post(mtmScheduledTaskErrorAjax.ajax_url, {
        _ajax_nonce: mtmScheduledTaskErrorAjax.nonce,
        action: 'mtm_remove_cron_error',
        matomo_job_id: $(e.target).closest('.matomo-cron-error').data('job')
      });
    });
  }

  // whats new notice dismiss
  if (typeof mtmWhatsNewNotificationAjax !== 'undefined' && mtmWhatsNewNotificationAjax.ajax_url) {
      $('body').on('click', '.matomo-whats-new .notice-dismiss', function (e) {
          $.post(mtmWhatsNewNotificationAjax.ajax_url, {
              _ajax_nonce: mtmWhatsNewNotificationAjax.nonce,
              action: 'mtm_dismiss_whats_new',
              matomo_notification: $(e.target).closest('.matomo-whats-new').data('notification-id'),
          });
      });
  }

  // add a notification dot to menu items that need it (see WhatsNewNotification.php)
  if (typeof mtmUnseenWhatsNewNotifications !== 'undefined' && mtmUnseenWhatsNewNotifications.length) {
      $('#toplevel_page_matomo a').each(function () {
          var href = $(this).attr('href');

          var m = href.match(/\?page=(.*?)$/);
          var page = m && m[1];
          if (!page) {
              return;
          }

          if (!mtmUnseenWhatsNewNotifications.includes(page)) {
              return;
          }

          $(this).addClass('matomo-notification-dot');
      });
  }
});
