<?php
if (!defined('WORDFENCE_VERSION')) { exit; }
/**
 * @var wfActivityReportView $this
 */

$start_time = wfActivityReport::getReportDateFrom();
$end_time = time();
$report_start = wfUtils::formatLocalTime(get_option('date_format'), $start_time);
$report_end = wfUtils::formatLocalTime(get_option('date_format'), $end_time);
$title = sprintf('Wordfence activity from <br><strong>%s</strong> to <strong>%s</strong>', $report_start, $report_end);
$bg_colors = array(
	'even' => 'background-color: #eeeeee;',
	'odd' => '',
);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo get_option('blog_charset') ?>"/>
	<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
	<title><?php echo esc_html(strip_tags($title)) ?></title>
	<!-- Targeting Windows Mobile -->
	<!--[if IEMobile 7]>
	<style type="text/css">

	</style>
	<![endif]-->

	<!-- ***********************************************
	****************************************************
	END MOBILE TARGETING
	****************************************************
	************************************************ -->

	<!--[if gte mso 9]>
	<style>
		/* Target Outlook 2007 and 2010 */
	</style>
	<![endif]-->
</head>
  <body style="font-size: 10pt; vertical-align: baseline; line-height: 1; font-family: Helvetica, Arial, sans-serif; text-rendering: optimizeLegibility; color: #000; background-image: none !important; width: 100% !important; -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; background-color: #e6e6e6; margin: 0; padding: 0; border: 0;" bgcolor="#e6e6e6"><style type="text/css">
blockquote:before { content: none !important; }
blockquote:after { content: none !important; }
q:before { content: none !important; }
q:after { content: none !important; }
a:focus { outline: thin dotted !important; }
.clear:after { clear: both !important; }
.wrapper:after { clear: both !important; }
.format-status .entry-header:after { clear: both !important; }
.clear:before { display: table !important; content: "" !important; }
.clear:after { display: table !important; content: "" !important; }
.wrapper:before { display: table !important; content: "" !important; }
.wrapper:after { display: table !important; content: "" !important; }
.format-status .entry-header:before { display: table !important; content: "" !important; }
.format-status .entry-header:after { display: table !important; content: "" !important; }
.menu-toggle:hover { color: #5e5e5e !important; background-color: #ebebeb !important; background-repeat: repeat-x !important; background-image: linear-gradient(top, #f9f9f9, #ebebeb) !important; }
.menu-toggle:focus { color: #5e5e5e !important; background-color: #ebebeb !important; background-repeat: repeat-x !important; background-image: linear-gradient(top, #f9f9f9, #ebebeb) !important; }
button:hover { color: #5e5e5e !important; background-color: #ebebeb !important; background-repeat: repeat-x !important; background-image: linear-gradient(top, #f9f9f9, #ebebeb) !important; }
input[type="submit"]:hover { color: #5e5e5e !important; background-color: #ebebeb !important; background-repeat: repeat-x !important; background-image: linear-gradient(top, #f9f9f9, #ebebeb) !important; }
input[type="button"]:hover { color: #5e5e5e !important; background-color: #ebebeb !important; background-repeat: repeat-x !important; background-image: linear-gradient(top, #f9f9f9, #ebebeb) !important; }
input[type="reset"]:hover { color: #5e5e5e !important; background-color: #ebebeb !important; background-repeat: repeat-x !important; background-image: linear-gradient(top, #f9f9f9, #ebebeb) !important; }
article.post-password-required input[type=submit]:hover { color: #5e5e5e !important; background-color: #ebebeb !important; background-repeat: repeat-x !important; background-image: linear-gradient(top, #f9f9f9, #ebebeb) !important; }
.menu-toggle:active { color: #757575 !important; background-color: #e1e1e1 !important; background-repeat: repeat-x !important; background-image: linear-gradient(top, #ebebeb, #e1e1e1) !important; box-shadow: inset 0 0 8px 2px #c6c6c6, 0 1px 0 0 #f4f4f4 !important; border-color: transparent !important; }
button:active { color: #757575 !important; background-color: #e1e1e1 !important; background-repeat: repeat-x !important; background-image: linear-gradient(top, #ebebeb, #e1e1e1) !important; box-shadow: inset 0 0 8px 2px #c6c6c6, 0 1px 0 0 #f4f4f4 !important; border-color: transparent !important; }
input[type="submit"]:active { color: #757575 !important; background-color: #e1e1e1 !important; background-repeat: repeat-x !important; background-image: linear-gradient(top, #ebebeb, #e1e1e1) !important; box-shadow: inset 0 0 8px 2px #c6c6c6, 0 1px 0 0 #f4f4f4 !important; border-color: transparent !important; }
input[type="button"]:active { color: #757575 !important; background-color: #e1e1e1 !important; background-repeat: repeat-x !important; background-image: linear-gradient(top, #ebebeb, #e1e1e1) !important; box-shadow: inset 0 0 8px 2px #c6c6c6, 0 1px 0 0 #f4f4f4 !important; border-color: transparent !important; }
input[type="reset"]:active { color: #757575 !important; background-color: #e1e1e1 !important; background-repeat: repeat-x !important; background-image: linear-gradient(top, #ebebeb, #e1e1e1) !important; box-shadow: inset 0 0 8px 2px #c6c6c6, 0 1px 0 0 #f4f4f4 !important; border-color: transparent !important; }
a:hover { color: #0f3647 !important; }
.main-navigation .assistive-text:focus { background: #fff !important; border: 2px solid #333 !important; border-radius: 3px !important; clip: auto !important; color: #000 !important; display: block !important; font-size: 12px !important; padding: 12px !important; position: absolute !important; top: 5px !important; left: 5px !important; z-index: 100000 !important; }
.site-header h1 a:hover { color: #21759b !important; }
.site-header h2 a:hover { color: #21759b !important; }
.main-navigation a:hover { color: #21759b !important; }
.main-navigation a:focus { color: #21759b !important; }
.widget-area .widget a:hover { color: #21759b !important; }
.widget-area .widget a:visited { color: #9f9f9f !important; }
footer[role="contentinfo"] a:hover { color: #21759b !important; }
.comments-link a:hover { color: #21759b !important; }
.entry-meta a:hover { color: #21759b !important; }
.entry-content a:visited { color: #9f9f9f !important; }
.comment-content a:visited { color: #9f9f9f !important; }
article.format-aside h1 a:hover { color: #2e3542 !important; }
.format-status .entry-header header a:hover { color: #21759b !important; }
.comments-area article header a:hover { color: #21759b !important; }
.comments-area article header cite a:hover { text-decoration: underline !important; }
a.comment-reply-link:hover { color: #21759b !important; }
a.comment-edit-link:hover { color: #21759b !important; }
.template-front-page .widget-area .widget li a:hover { color: #21759b !important; }
@-ms-viewport { width: device-width !important; }
@viewport { width: device-width !important; }
.main-navigation li a:hover { color: #000 !important; }
.main-navigation li a:focus { color: #000 !important; }
.main-navigation ul li:hover > ul { border-left: 0 !important; clip: inherit !important; overflow: inherit !important; height: inherit !important; width: inherit !important; }
.main-navigation ul li:focus > ul { border-left: 0 !important; clip: inherit !important; overflow: inherit !important; height: inherit !important; width: inherit !important; }
.main-navigation li ul li a:hover { background: #e3e3e3 !important; color: #444 !important; }
.main-navigation li ul li a:focus { background: #e3e3e3 !important; color: #444 !important; }
footer a[rel=bookmark]:after { content: " [" attr(href) "] " !important; }
footer a[rel=bookmark]:visited:after { content: " [" attr(href) "] " !important; }
h1 a:active { color: red !important; }
h2 a:active { color: red !important; }
h3 a:active { color: red !important; }
h4 a:active { color: red !important; }
h5 a:active { color: red !important; }
h6 a:active { color: red !important; }
h1 a:visited { color: purple !important; }
h2 a:visited { color: purple !important; }
h3 a:visited { color: purple !important; }
h4 a:visited { color: purple !important; }
h5 a:visited { color: purple !important; }
h6 a:visited { color: purple !important; }
.button:hover { background: none repeat scroll 0 0 #1E8CBE !important; border-color: #0074A2 !important; box-shadow: 0 1px 0 rgba(120, 200, 230, 0.6) inset !important; color: #FFF !important; }
.button:active { background: none repeat scroll 0 0 #1E8CBE !important; border-color: #0074A2 !important; box-shadow: 0 1px 0 rgba(120, 200, 230, 0.6) inset !important; color: #FFF !important; }
.button:focus { background: none repeat scroll 0 0 #1E8CBE !important; border-color: #0074A2 !important; box-shadow: 0 1px 0 rgba(120, 200, 230, 0.6) inset !important; color: #FFF !important; }
</style>
<!-- Wrapper/Container Table: Use a wrapper table to control the width and the background color consistently of your email. Use this approach instead of setting attributes on the body tag. -->
<table cellpadding="0" cellspacing="0" border="0" id="backgroundTable" style="font-size: 100%; vertical-align: baseline; border-collapse: collapse; border-spacing: 0; width: 100% !important; line-height: 100% !important; mso-table-lspace: 0pt; mso-table-rspace: 0pt; margin: 0 auto; padding: 0; border: 0;">
	<tr style="font-size: 100%; vertical-align: baseline; margin: 0; padding: 0; border: 0;">
		<td valign="top" style="font-size: 100%; vertical-align: baseline; font-weight: normal; text-align: left; border-collapse: collapse; margin: 0; padding: 0; border: 0;" align="left">
			<div class="wrapper wp-core-ui" style="font-size: 100%; vertical-align: baseline; border-top-style: none; box-shadow: none; line-height: 1.4; width: 600px; background-color: #FFFFFF; margin: 0 auto; padding: 20px; border: 0;">
				<div style="float: right; text-align: right; line-height: 1.1; color: #666666; font-size: 100%; vertical-align: baseline; margin: 20px 0 0; padding: 0; border: 0;" align="right">
					<?php echo $title ?>
				</div>
				<a href="http://www.wordfence.com/zz5/" style="font-size: 100%; vertical-align: baseline; outline: none; color: orange; text-decoration: none; margin: 0; padding: 0; border: 0;"><img src="<?php echo wfUtils::getBaseURL(); ?>images/logo.png" alt="" style="font-size: 100%; vertical-align: baseline; -ms-interpolation-mode: bicubic; outline: none; text-decoration: none; margin: 0; padding: 0; border: 0 none; width: 560px;" /></a>

				<p style="font-size: 100%; vertical-align: baseline; margin: 1em 0; padding: 0; border: 0;">
					This email was sent from your website <a href="<?php echo network_site_url() ?>"><?php echo network_site_url() ?></a> and is a summary of security related activity that Wordfence monitors for the period <?php printf('%s to %s', $report_start, $report_end) ?>. <?php if (!wfConfig::get('isPaid')): ?>NOTE: You are using the free version of Wordfence and are missing out on features like cellphone sign-in, country blocking and detecting if your site IP is sending spam. <a href="http://www.wordfence.com/zz6/">Click here to upgrade to Wordfence Premium now</a>.<?php endif ?>
				</p>

				<h2 style="font-size: 20px; vertical-align: baseline; clear: both; color: #222 !important; margin: 20px 0 4px; padding: 0; border: 0;">
					Top 10 IPs Blocked
				</h2>

				<?php wfHelperString::cycle(); ?>

				<table class="activity-table" style="font-size: 100%; vertical-align: baseline; border-collapse: collapse; border-spacing: 0; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%; max-width: 100%; margin: 0; padding: 0; border: 0;">
					<thead style="font-size: 100%; vertical-align: baseline; margin: 0; padding: 0; border: 0;">
						<tr style="font-size: 100%; vertical-align: baseline; margin: 0; padding: 0; border: 0;">
							<th style="font-size: 100%; vertical-align: baseline; font-weight: bold; text-align: left; color: #FFFFFF; background-color: #222; margin: 0; padding: 6px 4px; border: 1px solid #474747;" align="left" bgcolor="#222" valign="baseline">IP</th>
							<th style="font-size: 100%; vertical-align: baseline; font-weight: bold; text-align: left; color: #FFFFFF; background-color: #222; margin: 0; padding: 6px 4px; border: 1px solid #474747;" align="left" bgcolor="#222" valign="baseline">Country</th>
							<th style="font-size: 100%; vertical-align: baseline; font-weight: bold; text-align: left; color: #FFFFFF; background-color: #222; margin: 0; padding: 6px 4px; border: 1px solid #474747;" align="left" bgcolor="#222" valign="baseline">Block Count</th>
						</tr>
					</thead>
					<tbody style="font-size: 100%; vertical-align: baseline; margin: 0; padding: 0; border: 0;">
						<?php if ($top_ips_blocked): ?>
						<?php foreach ($top_ips_blocked as $row): ?>
							<?php
							$stripe = wfHelperString::cycle('odd', 'even');
							?>
							<tr class="<?php echo $stripe ?>" style="font-size: 100%; vertical-align: baseline; margin: 0; padding: 0; border: 0;">
								<td style="font-size: 100%; vertical-align: baseline; font-weight: normal; text-align: left; border-collapse: collapse; margin: 0; padding: 6px 4px; border: 1px solid #cccccc;<?php echo $bg_colors[$stripe] ?>" align="left" valign="baseline"><code style="font-size: 100%; vertical-align: baseline; margin: 0; padding: 0; border: 0;"><?php echo wfUtils::inet_ntop($row->IP) ?></code></td>
								<td style="font-size: 100%; vertical-align: baseline; font-weight: normal; text-align: left; border-collapse: collapse; margin: 0; padding: 6px 4px; border: 1px solid #cccccc;<?php echo $bg_colors[$stripe] ?>" align="left" valign="baseline">
									<?php if ($row->countryCode): ?>
										<img src="<?php echo wfUtils::getBaseURL() . 'images/flags/' . esc_attr(strtolower($row->countryCode)) ?>.png" class="wfFlag" height="11" width="16" style="font-size: 100%; vertical-align: baseline; -ms-interpolation-mode: bicubic; outline: none; text-decoration: none; margin: 0; padding: 0; border: 0;">
										&nbsp;
										<?php echo esc_html($row->countryCode) ?>
									<?php else: ?>
										(Unknown)
									<?php endif ?>
								</td>
								<td style="font-size: 100%; vertical-align: baseline; font-weight: normal; text-align: left; border-collapse: collapse; margin: 0; padding: 6px 4px; border: 1px solid #cccccc;<?php echo $bg_colors[$stripe] ?>" align="left" valign="baseline"><?php echo (int)$row->blockCount ?></td>
							</tr>
						<?php endforeach ?>
						<?php else: ?>
							<tr>
								<td colspan="3">
									No data currently.
								</td>
							</tr>
						<?php endif ?>
					</tbody>
				</table>

				<p style="font-size: 100%; vertical-align: baseline; margin: 1em 0; padding: 0; border: 0;">
					<a class="button" href="<?php echo network_admin_url('admin.php?page=WordfenceWAF#top#blocking') ?>"  style="font-size: 13px; vertical-align: baseline; outline: none; color: #FFF; text-decoration: none; display: inline-block; line-height: 26px; height: 28px; cursor: pointer; border-radius: 3px; white-space: nowrap; box-sizing: border-box; box-shadow: 0 1px 0 rgba(120, 200, 230, 0.5) inset, 0 1px 0 rgba(0, 0, 0, 0.15); background-image: none; background-attachment: scroll; background-repeat: repeat; background-color: #2EA2CC; margin: 0; padding: 0 10px 1px; border: 1px solid #0074a2;">Update Blocked IPs</a>
				</p>

				<?php wfHelperString::cycle(); ?>

				<h2 style="font-size: 20px; vertical-align: baseline; clear: both; color: #222 !important; margin: 20px 0 4px; padding: 0; border: 0;">Top 10 Countries Blocked</h2>

				<table class="activity-table" style="font-size: 100%; vertical-align: baseline; border-collapse: collapse; border-spacing: 0; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%; max-width: 100%; margin: 0; padding: 0; border: 0;">
					<thead style="font-size: 100%; vertical-align: baseline; margin: 0; padding: 0; border: 0;">
						<tr style="font-size: 100%; vertical-align: baseline; margin: 0; padding: 0; border: 0;">
							<th style="font-size: 100%; vertical-align: baseline; font-weight: bold; text-align: left; color: #FFFFFF; background-color: #222; margin: 0; padding: 6px 4px; border: 1px solid #474747;" align="left" bgcolor="#222" valign="baseline">Country</th>
							<th style="font-size: 100%; vertical-align: baseline; font-weight: bold; text-align: left; color: #FFFFFF; background-color: #222; margin: 0; padding: 6px 4px; border: 1px solid #474747;" align="left" bgcolor="#222" valign="baseline">Total IPs Blocked</th>
							<th style="font-size: 100%; vertical-align: baseline; font-weight: bold; text-align: left; color: #FFFFFF; background-color: #222; margin: 0; padding: 6px 4px; border: 1px solid #474747;" align="left" bgcolor="#222" valign="baseline">Block Count</th>
						</tr>
					</thead>
					<tbody style="font-size: 100%; vertical-align: baseline; margin: 0; padding: 0; border: 0;">
						<?php if ($top_countries_blocked): ?>
							<?php foreach ($top_countries_blocked as $row): ?>
								<?php
								$stripe = wfHelperString::cycle('odd', 'even');
								?>
								<tr class="<?php echo $stripe ?>" style="font-size: 100%; vertical-align: baseline; margin: 0; padding: 0; border: 0;">
									<td style="font-size: 100%; vertical-align: baseline; font-weight: normal; text-align: left; border-collapse: collapse; margin: 0; padding: 6px 4px; border: 1px solid #cccccc;<?php echo $bg_colors[$stripe] ?>" align="left" valign="baseline">
										<?php if ($row->countryCode): ?>
											<img src="<?php echo wfUtils::getBaseURL() . 'images/flags/' . strtolower($row->countryCode) ?>.png" class="wfFlag" height="11" width="16" style="font-size: 100%; vertical-align: baseline; -ms-interpolation-mode: bicubic; outline: none; text-decoration: none; margin: 0; padding: 0; border: 0;">
											&nbsp;
											<?php echo esc_html($row->countryCode) ?>
										<?php else: ?>
											(Unknown)
										<?php endif ?>
									</td>
									<td style="font-size: 100%; vertical-align: baseline; font-weight: normal; text-align: left; border-collapse: collapse; margin: 0; padding: 6px 4px; border: 1px solid #cccccc;<?php echo $bg_colors[$stripe] ?>" align="left" valign="baseline"><?php echo esc_html($row->totalIPs) ?></td>
									<td style="font-size: 100%; vertical-align: baseline; font-weight: normal; text-align: left; border-collapse: collapse; margin: 0; padding: 6px 4px; border: 1px solid #cccccc;<?php echo $bg_colors[$stripe] ?>" align="left" valign="baseline"><?php echo (int)$row->totalBlockCount ?></td>
								</tr>
							<?php endforeach ?>
						<?php else: ?>
							<tr>
								<td colspan="3">
									No data currently.
								</td>
							</tr>
						<?php endif ?>
					</tbody>
				</table>

				<p style="font-size: 100%; vertical-align: baseline; margin: 1em 0; padding: 0; border: 0;">
					<a class="button" href="<?php echo network_admin_url('admin.php?page=WordfenceWAF#top#blocking') ?>" style="font-size: 13px; vertical-align: baseline; outline: none; color: #FFF; text-decoration: none; display: inline-block; line-height: 26px; height: 28px; cursor: pointer; border-radius: 3px; white-space: nowrap; box-sizing: border-box; box-shadow: 0 1px 0 rgba(120, 200, 230, 0.5) inset, 0 1px 0 rgba(0, 0, 0, 0.15); background-image: none; background-attachment: scroll; background-repeat: repeat; background-color: #2EA2CC; margin: 0; padding: 0 10px 1px; border: 1px solid #0074a2;">Update Blocked Countries</a>
				</p>

				<?php wfHelperString::cycle(); ?>

				<h2 style="font-size: 20px; vertical-align: baseline; clear: both; color: #222 !important; margin: 20px 0 4px; padding: 0; border: 0;">Top 10 Failed Logins</h2>

				<table class="activity-table" style="font-size: 100%; vertical-align: baseline; border-collapse: collapse; border-spacing: 0; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%; max-width: 100%; margin: 0; padding: 0; border: 0;">
					<thead style="font-size: 100%; vertical-align: baseline; margin: 0; padding: 0; border: 0;">
						<tr style="font-size: 100%; vertical-align: baseline; margin: 0; padding: 0; border: 0;">
							<th style="font-size: 100%; vertical-align: baseline; font-weight: bold; text-align: left; color: #FFFFFF; background-color: #222; margin: 0; padding: 6px 4px; border: 1px solid #474747;" align="left" bgcolor="#222" valign="baseline">Username</th>
							<th style="font-size: 100%; vertical-align: baseline; font-weight: bold; text-align: left; color: #FFFFFF; background-color: #222; margin: 0; padding: 6px 4px; border: 1px solid #474747;" align="left" bgcolor="#222" valign="baseline">Login Attempts</th>
							<th style="font-size: 100%; vertical-align: baseline; font-weight: bold; text-align: left; color: #FFFFFF; background-color: #222; margin: 0; padding: 6px 4px; border: 1px solid #474747;" align="left" bgcolor="#222" valign="baseline">Existing User</th>
						</tr>
					</thead>
					<tbody style="font-size: 100%; vertical-align: baseline; margin: 0; padding: 0; border: 0;">
						<?php if ($top_failed_logins): ?>
							<?php foreach ($top_failed_logins as $row): ?>
								<?php
								$stripe = wfHelperString::cycle('odd', 'even');
								?>
								<tr class="<?php echo $stripe ?>" style="font-size: 100%; vertical-align: baseline; margin: 0; padding: 0; border: 0;">
									<td style="font-size: 100%; vertical-align: baseline; font-weight: normal; text-align: left; border-collapse: collapse; margin: 0; padding: 6px 4px; border: 1px solid #cccccc;<?php echo $bg_colors[$stripe] ?>" align="left" valign="baseline"><?php echo esc_html($row->username) ?></td>
									<td style="font-size: 100%; vertical-align: baseline; font-weight: normal; text-align: left; border-collapse: collapse; margin: 0; padding: 6px 4px; border: 1px solid #cccccc;<?php echo $bg_colors[$stripe] ?>" align="left" valign="baseline"><?php echo esc_html($row->fail_count) ?></td>
									<td style="font-size: 100%; vertical-align: baseline; font-weight: normal; text-align: left; border-collapse: collapse; margin: 0; padding: 6px 4px; border: 1px solid #cccccc;<?php echo $bg_colors[$stripe] ?>" align="left" valign="baseline" class="<?php echo sanitize_html_class($row->is_valid_user ? 'loginFailValidUsername' : 'loginFailInvalidUsername') ?>"><?php echo $row->is_valid_user ? 'Yes' : 'No' ?></td>
								</tr>
							<?php endforeach ?>
						<?php else: ?>
							<tr>
								<td colspan="3">
									No failed logins yet.
								</td>
							</tr>
						<?php endif ?>
					</tbody>
				</table>

				<p style="font-size: 100%; vertical-align: baseline; margin: 1em 0; padding: 0; border: 0;">
					<a class="button" href="<?php echo network_admin_url('admin.php?page=WordfenceWAF&subpage=waf_options#waf-options-bruteforce') ?>" style="font-size: 13px; vertical-align: baseline; outline: none; color: #FFF; text-decoration: none; display: inline-block; line-height: 26px; height: 28px; cursor: pointer; border-radius: 3px; white-space: nowrap; box-sizing: border-box; box-shadow: 0 1px 0 rgba(120, 200, 230, 0.5) inset, 0 1px 0 rgba(0, 0, 0, 0.15); background-image: none; background-attachment: scroll; background-repeat: repeat; background-color: #2EA2CC; margin: 0; padding: 0 10px 1px; border: 1px solid #0074a2;">Update Login Security Options</a>
				</p>
				
				<?php wfHelperString::cycle(); ?>
				
				<h2 style="font-size: 20px; vertical-align: baseline; clear: both; color: #222 !important; margin: 20px 0 4px; padding: 0; border: 0;">Recently Blocked Attacks</h2>
				
				<table class="activity-table" style="font-size: 100%; vertical-align: baseline; border-collapse: collapse; border-spacing: 0; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%; max-width: 100%; margin: 0; padding: 0; border: 0;">
					<thead style="font-size: 100%; vertical-align: baseline; margin: 0; padding: 0; border: 0;">
					<tr style="font-size: 100%; vertical-align: baseline; margin: 0; padding: 0; border: 0;">
						<th style="font-size: 100%; vertical-align: baseline; font-weight: bold; text-align: left; color: #FFFFFF; background-color: #222; margin: 0; padding: 6px 4px; border: 1px solid #474747;" align="left" bgcolor="#222" valign="baseline">Time</th>
						<th style="font-size: 100%; vertical-align: baseline; font-weight: bold; text-align: left; color: #FFFFFF; background-color: #222; margin: 0; padding: 6px 4px; border: 1px solid #474747;" align="left" bgcolor="#222" valign="baseline">IP / Action</th>
					</tr>
					</thead>
					<tbody style="font-size: 100%; vertical-align: baseline; margin: 0; padding: 0; border: 0;">
					<?php if (count($recent_firewall_activity) > 0): ?>
					<?php foreach ($recent_firewall_activity as $attack_row):
						?>
						<?php
						$stripe = wfHelperString::cycle('odd', 'even');
						?>
						<tr class="<?php echo $stripe ?>" style="font-size: 100%; vertical-align: baseline; margin: 0; padding: 0; border: 0;">
							<td style="font-size: 100%; vertical-align: baseline; font-weight: normal; text-align: left; border-collapse: collapse; margin: 0; padding: 6px 4px; border: 1px solid #cccccc;white-space: nowrap;<?php echo $bg_colors[$stripe] ?>" align="left" valign="baseline"><?php echo $this->attackTime($attack_row->attackLogTime) ?></td>
							<td style="font-size: 100%; vertical-align: baseline; font-weight: normal; text-align: left; border-collapse: collapse; margin: 0; padding: 6px 4px; border: 1px solid #cccccc;<?php echo $bg_colors[$stripe] ?>" align="left" valign="baseline">
								<div style="font-weight: bold; font-size: 12px;"><?php echo $this->displayIP($attack_row->IP) ?></div> 
								<pre class="display-file" style="font-size: 12px; vertical-align: baseline; width: 420px; margin: 0; padding: 0; border: 0; white-space: normal;"><?php echo wfUtils::potentialBinaryStringToHTML($attack_row->longDescription, true) ?></pre>
							</td>
						</tr>
					<?php endforeach ?>
					<?php else: ?>
						<tr>
							<td colspan="2">
								No blocked attacks yet.
							</td>
						</tr>
					<?php endif ?>
					</tbody>
				</table>
				
				<?php
				if ($omitted_firewall_activity > 10):
				?>
				<div style="font-size: 14px; vertical-align: baseline; clear: both; color: #f00 !important; margin: 8px 0 4px; padding: 0; border: 0;">and <?php echo $omitted_firewall_activity ?> additional attacks</div>
				<?php endif ?> 
				
				<p style="font-size: 100%; vertical-align: baseline; margin: 1em 0; padding: 0; border: 0;">
					<a class="button" href="<?php echo network_admin_url('admin.php?page=WordfenceTools&subpage=livetraffic') ?>" style="font-size: 13px; vertical-align: baseline; outline: none; color: #FFF; text-decoration: none; display: inline-block; line-height: 26px; height: 28px; cursor: pointer; border-radius: 3px; white-space: nowrap; box-sizing: border-box; box-shadow: 0 1px 0 rgba(120, 200, 230, 0.5) inset, 0 1px 0 rgba(0, 0, 0, 0.15); background-image: none; background-attachment: scroll; background-repeat: repeat; background-color: #2EA2CC; margin: 0; padding: 0 10px 1px; border: 1px solid #0074a2;">View Recent Traffic</a>
				</p>

				<?php wfHelperString::cycle(); ?>

				<h2 style="font-size: 20px; vertical-align: baseline; clear: both; color: #222 !important; margin: 20px 0 4px; padding: 0; border: 0;">Recently Modified Files</h2>

				<table class="activity-table" style="font-size: 100%; vertical-align: baseline; border-collapse: collapse; border-spacing: 0; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%; max-width: 100%; margin: 0; padding: 0; border: 0;">
					<thead style="font-size: 100%; vertical-align: baseline; margin: 0; padding: 0; border: 0;">
						<tr style="font-size: 100%; vertical-align: baseline; margin: 0; padding: 0; border: 0;">
							<th style="font-size: 100%; vertical-align: baseline; font-weight: bold; text-align: left; color: #FFFFFF; background-color: #222; margin: 0; padding: 6px 4px; border: 1px solid #474747;" align="left" bgcolor="#222" valign="baseline">Modified</th>
							<th style="font-size: 100%; vertical-align: baseline; font-weight: bold; text-align: left; color: #FFFFFF; background-color: #222; margin: 0; padding: 6px 4px; border: 1px solid #474747;" align="left" bgcolor="#222" valign="baseline">File</th>
						</tr>
					</thead>
					<tbody style="font-size: 100%; vertical-align: baseline; margin: 0; padding: 0; border: 0;">
						<?php foreach ($recently_modified_files as $file_row):
							list($file, $mod_time) = $file_row;
							?>
							<?php
							$stripe = wfHelperString::cycle('odd', 'even');
							?>
							<tr class="<?php echo $stripe ?>" style="font-size: 100%; vertical-align: baseline; margin: 0; padding: 0; border: 0;">
								<td style="font-size: 100%; vertical-align: baseline; font-weight: normal; text-align: left; border-collapse: collapse; margin: 0; padding: 6px 4px; border: 1px solid #cccccc;white-space: nowrap;<?php echo $bg_colors[$stripe] ?>" align="left" valign="baseline"><?php echo $this->modTime($mod_time) ?></td>
								<td style="font-size: 100%; vertical-align: baseline; font-weight: normal; text-align: left; border-collapse: collapse; margin: 0; padding: 6px 4px; border: 1px solid #cccccc;<?php echo $bg_colors[$stripe] ?>" align="left" valign="baseline">
									<pre class="display-file" style="font-size: 12px; vertical-align: baseline; width: 420px; overflow: auto; margin: 0; padding: 0; border: 0;"><?php echo esc_html($this->displayFile($file)) ?></pre>
								</td>
							</tr>
						<?php endforeach ?>
					</tbody>
				</table>

				<div style="font-size: 12px; font-style: italic; vertical-align: baseline; clear: both; margin: 8px 0 4px; padding: 0; border: 0;">This list may include WordPress core/plugin/theme updates, error logs, cache files, and other normal changes.</div>

				<?php wfHelperString::cycle(); ?>

				<h2 style="font-size: 20px; vertical-align: baseline; clear: both; color: #222 !important; margin: 20px 0 4px; padding: 0; border: 0;">Updates Needed</h2>

				<?php if ($updates_needed['core']): ?>
					<h4 style="font-size: 16px; vertical-align: baseline; clear: both; color: #666666 !important; margin: 20px 0 4px; padding: 0; border: 0;">Core</h4>
					<ul style="font-size: 100%; vertical-align: baseline; list-style-type: none; margin: 0; padding: 0; border: 0;">
						<li style="font-size: 100%; vertical-align: baseline; margin: 0; padding: 0; border: 0;">A new version of WordPress (v<?php echo esc_html($updates_needed['core']) ?>) is available.</li>
					</ul>
				<?php endif ?>
				<?php if ($updates_needed['plugins']): ?>
					<h4 style="font-size: 16px; vertical-align: baseline; clear: both; color: #666666 !important; margin: 20px 0 4px; padding: 0; border: 0;">Plugins</h4>
					<ul style="font-size: 100%; vertical-align: baseline; list-style-type: none; margin: 0; padding: 0; border: 0;">
						<?php
						foreach ($updates_needed['plugins'] as $plugin):
							$newVersion = ($plugin['newVersion'] == 'Unknown' ? $plugin['newVersion'] : "v{$plugin['newVersion']}");
						?>
							<li style="font-size: 100%; vertical-align: baseline; margin: 0; padding: 0; border: 0;">
								A new version of the plugin "<?php echo esc_html("{$plugin['Name']} ({$newVersion})") ?>" is available.<?php if (isset($plugin['vulnerable']) && $plugin['vulnerable']) { echo " <strong>This update includes security-related fixes.</strong>"; } ?>
							</li>
						<?php endforeach ?>
					</ul>
				<?php endif ?>
				<?php if ($updates_needed['themes']): ?>
					<h4 style="font-size: 16px; vertical-align: baseline; clear: both; color: #666666 !important; margin: 20px 0 4px; padding: 0; border: 0;">Themes</h4>
					<ul style="font-size: 100%; vertical-align: baseline; list-style-type: none; margin: 0; padding: 0; border: 0;">
						<?php
						foreach ($updates_needed['themes'] as $theme):
							$newVersion = ($theme['newVersion'] == 'Unknown' ? $theme['newVersion'] : "v{$theme['newVersion']}");
						?>
							<li style="font-size: 100%; vertical-align: baseline; margin: 0; padding: 0; border: 0;">
								A new version of the theme "<?php echo esc_html("{$theme['name']} ({$newVersion})") ?>" is available.<?php if (isset($theme['vulnerable']) && $theme['vulnerable']) { echo " <strong>This update includes security-related fixes.</strong>"; } ?>
							</li>
						<?php endforeach ?>
					</ul>
				<?php endif ?>
				
				<?php if ($updates_needed['core'] || $updates_needed['plugins'] || $updates_needed['themes']): ?>
					<p style="font-size: 100%; vertical-align: baseline; margin: 1em 0; padding: 0; border: 0;">
						<a class="button" href="<?php echo esc_attr(network_admin_url('update-core.php')) ?>" style="font-size: 13px; vertical-align: baseline; outline: none; color: #FFF; text-decoration: none; display: inline-block; line-height: 26px; height: 28px; cursor: pointer; border-radius: 3px; white-space: nowrap; box-sizing: border-box; box-shadow: 0 1px 0 rgba(120, 200, 230, 0.5) inset, 0 1px 0 rgba(0, 0, 0, 0.15); background-image: none; background-attachment: scroll; background-repeat: repeat; background-color: #2EA2CC; margin: 0; padding: 0 10px 1px; border: 1px solid #0074a2;">Update Now</a>
					</p>
				<?php else: ?>
					<p style="font-size: 100%; vertical-align: baseline; margin: 1em 0; padding: 0; border: 0;">
						No updates are available at this time.
					</p>
				<?php endif ?>

				<p style="font-size: 100%; vertical-align: baseline; margin: 1em 0; padding: 0; border: 0;">
					If you would like to sign-in to <a href="<?php echo network_site_url() ?>"><?php echo network_site_url() ?></a> please <a href="<?php echo network_admin_url() ?>">click here</a> now. You can change the frequency of this email or turn it on and off by visiting your <a href="<?php echo network_admin_url('admin.php?page=Wordfence&subpage=global_options#global-options-email-summary') ?>">Wordfence options page</a>.
				</p>
			</div>
		</td>
	</tr>
</table>
<!-- End of wrapper table -->
</body>
</html>
