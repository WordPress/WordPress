<?php
if (!defined('WORDFENCE_VERSION')) { exit; }
/**
 * @var wfActivityReportView $this
 */
$title = 'Wordfence Activity for the week of ' . wfUtils::formatLocalTime(get_option('date_format'));
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo get_option('blog_charset') ?>"/>
	<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
	<title><?php echo esc_html($title) ?></title>
	<link rel="stylesheet" href="http://www.wordfence.com/blog/wp-content/themes/twentytwelve/style.css?ver=4.1.1" title=""/>

	<style type="text/css">
		/* Based on The MailChimp Reset INLINE: Yes. */
		/* Client-specific Styles */
		#outlook a {
			padding: 0;
		}

		/* Force Outlook to provide a "view in browser" menu link. */
		body {
			width: 100% !important;
			-webkit-text-size-adjust: 100%;
			-ms-text-size-adjust: 100%;
			margin: 0;
			padding: 0;
		}

		/* Prevent Webkit and Windows Mobile platforms from changing default font sizes.*/
		.ExternalClass {
			width: 100%;
		}

		/* Force Hotmail to display emails at full width */
		.ExternalClass, .ExternalClass p, .ExternalClass span, .ExternalClass font, .ExternalClass td, .ExternalClass div {
			line-height: 100%;
		}

		/* Forces Hotmail to display normal line spacing.  More on that: http://www.emailonacid.com/forum/viewthread/43/ */
		#backgroundTable {
			margin: 0 auto;
			padding: 0;
			width: 100% !important;
			line-height: 100% !important;
		}

		/* End reset */

		/* Some sensible defaults for images
		Bring inline: Yes. */
		img {
			outline: none;
			text-decoration: none;
			-ms-interpolation-mode: bicubic;
		}

		a img {
			border: none;
		}

		.image_fix {
			display: block;
		}

		/* Yahoo paragraph fix
		Bring inline: Yes. */
		p {
			margin: 1em 0;
		}

		/* Hotmail header color reset
		Bring inline: Yes. */
		h1, h2, h3, h4, h5, h6 {
			color: black !important;
		}

		h1 a, h2 a, h3 a, h4 a, h5 a, h6 a {
			color: blue !important;
		}

		h1 a:active, h2 a:active, h3 a:active, h4 a:active, h5 a:active, h6 a:active {
			color: red !important; /* Preferably not the same color as the normal header link color.  There is limited support for psuedo classes in email clients, this was added just for good measure. */
		}

		h1 a:visited, h2 a:visited, h3 a:visited, h4 a:visited, h5 a:visited, h6 a:visited {
			color: purple !important; /* Preferably not the same color as the normal header link color. There is limited support for psuedo classes in email clients, this was added just for good measure. */
		}

		/* Outlook 07, 10 Padding issue fix
		Bring inline: No.*/
		table td {
			border-collapse: collapse;
		}

		/* Remove spacing around Outlook 07, 10 tables
		Bring inline: Yes */
		table {
			border-collapse: collapse;
			mso-table-lspace: 0pt;
			mso-table-rspace: 0pt;
		}

		/* Styling your links has become much simpler with the new Yahoo.  In fact, it falls in line with the main credo of styling in email and make sure to bring your styles inline.  Your link colors will be uniform across clients when brought inline.
		Bring inline: Yes. */
		a {
			color: orange;
		}

		.wrapper {
			line-height: 1.4;
			width: 600px;
			padding: 20px;
			margin: 0 auto;
			background-color: #FFFFFF;
		}

		h1, h2, h3, h4 {
			margin: 20px 0 4px;
			color: #222 !important;
		}

		h1 {
			float: right;
			text-align: right;
			font-size: 30px;
			color: #444444 !important;
			line-height: 1.1;
		}

		h2 {
			font-size: 20px;
		}

		h4 {
			font-size: 16px;
			color:#666666 !important;
		}

		table.wf-striped-table {
			width: 100%;
			max-width: 100%;
		}

		table.wf-striped-table th,
		table.wf-striped-table td {
			padding: 6px 4px;
			border: 1px solid #cccccc;
		}

		table.wf-striped-table thead th,
		table.wf-striped-table thead td {
			background-color: #222;
			color: #FFFFFF;
			font-weight: bold;
			border-color: #474747;
		}

		table.wf-striped-table tbody tr.even td {
			background-color: #eeeeee;
		}

		.loginFailValidUsername {
			color: #00c000;
			font-weight: bold;
		}

		.loginFailInvalidUsername {
			color: #e74a2a;
			font-weight: bold;
		}

		.display-file {
			font-size: 12px;
			width: 420px;
			overflow: auto;
		}

		.button {
			display: inline-block;
			font-size: 13px;
			line-height: 26px;
			height: 28px;
			margin: 0;
			padding: 0 10px 1px;
			cursor: pointer;
			border-radius: 3px;
			white-space: nowrap;
			box-sizing: border-box;
			background: none repeat scroll 0 0 #2EA2CC;
			border: 1px solid #0074A2;
			box-shadow: 0 1px 0 rgba(120, 200, 230, 0.5) inset, 0 1px 0 rgba(0, 0, 0, 0.15);
			color: #FFF;
			text-decoration: none;
		}
		.button:hover,
		.button:active,
		.button:focus {
			background: none repeat scroll 0 0 #1E8CBE;
			border-color: #0074A2;
			box-shadow: 0 1px 0 rgba(120, 200, 230, 0.6) inset;
			color: #FFF;
		}

		/***************************************************
		****************************************************
		MOBILE TARGETING
		****************************************************
		***************************************************/
		@media only screen and (max-device-width: 480px) {
			/* Part one of controlling phone number linking for mobile. */
			a[href^="tel"], a[href^="sms"] {
				text-decoration: none;
				color: blue; /* or whatever your want */
				pointer-events: none;
				cursor: default;
			}

			.mobile_link a[href^="tel"], .mobile_link a[href^="sms"] {
				text-decoration: default;
				color: orange !important;
				pointer-events: auto;
				cursor: default;
			}

		}

		/* More Specific Targeting */

		@media only screen and (min-device-width: 768px) and (max-device-width: 1024px) {
			/* You guessed it, ipad (tablets, smaller screens, etc) */
			/* repeating for the ipad */
			a[href^="tel"], a[href^="sms"] {
				text-decoration: none;
				color: blue; /* or whatever your want */
				pointer-events: none;
				cursor: default;
			}

			.mobile_link a[href^="tel"], .mobile_link a[href^="sms"] {
				text-decoration: default;
				color: orange !important;
				pointer-events: auto;
				cursor: default;
			}
		}

		@media only screen and (-webkit-min-device-pixel-ratio: 2) {
			/* Put your iPhone 4g styles in here */
		}

		/* Android targeting */
		@media only screen and (-webkit-device-pixel-ratio: .75) {
			/* Put CSS for low density (ldpi) Android layouts in here */
		}

		@media only screen and (-webkit-device-pixel-ratio: 1) {
			/* Put CSS for medium density (mdpi) Android layouts in here */
		}

		@media only screen and (-webkit-device-pixel-ratio: 1.5) {
			/* Put CSS for high density (hdpi) Android layouts in here */
		}

		/* end Android targeting */

	</style>

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
<body>
<!-- Wrapper/Container Table: Use a wrapper table to control the width and the background color consistently of your email. Use this approach instead of setting attributes on the body tag. -->
<table cellpadding="0" cellspacing="0" border="0" id="backgroundTable">
	<tr>
		<td valign="top">
			<div class="wrapper wp-core-ui">
				<div style="float: right;text-align: right;line-height:1.1;color: #666666;margin:20px 0 0;">
					Activity for week of<br> <strong><?php echo wfUtils::formatLocalTime(get_option('date_format')) ?></strong>
				</div>
				<a href="http://www.wordfence.com/zz7/"><img src="<?php echo wfUtils::getBaseURL(); ?>images/logo.png" alt=""/></a>

				<h2>Top 10 IPs Blocked</h2>

				<?php wfHelperString::cycle(); ?>

				<table class="wf-striped-table">
					<thead>
						<tr>
							<th>IP</th>
							<th>Country</th>
							<th>Block Count</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($top_ips_blocked as $row): ?>
							<tr class="<?php echo wfHelperString::cycle('odd', 'even') ?>">
								<td><code><?php echo wfUtils::inet_ntop($row->IP) ?></code></td>
								<td>
									<?php if ($row->countryCode): ?>
										<img src="<?php echo wfUtils::getBaseURL() . 'images/flags/' . esc_attr(strtolower($row->countryCode)) ?>.png" class="wfFlag" height="11" width="16">
										&nbsp;
										<?php echo esc_html($row->countryCode) ?>
									<?php else: ?>
										(Unknown)
									<?php endif ?>
								</td>
								<td><?php echo (int)$row->blockCount ?></td>
							</tr>
						<?php endforeach ?>
					</tbody>
				</table>

				<p>
					<a class="button" href="<?php echo network_admin_url('admin.php?page=WordfenceWAF#top#blocking') ?>">Update Blocked IPs</a>
				</p>

				<?php wfHelperString::cycle(); ?>

				<h2>Top 10 Countries Blocked</h2>

				<table class="wf-striped-table">
					<thead>
						<tr>
							<th>Country</th>
							<th>Total IPs Blocked</th>
							<th>Block Count</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($top_countries_blocked as $row): ?>
							<tr class="<?php echo wfHelperString::cycle('odd', 'even') ?>">
								<td>
									<?php if ($row->countryCode): ?>
										<img src="<?php echo wfUtils::getBaseURL() . 'images/flags/' . strtolower($row->countryCode) ?>.png" class="wfFlag" height="11" width="16">
										&nbsp;
										<?php echo esc_html($row->countryCode) ?>
									<?php else: ?>
										(Unknown)
									<?php endif ?>
								</td>
								<td><?php echo esc_html($row->totalIPs) ?></td>
								<td><?php echo (int)$row->totalBlockCount ?></td>
							</tr>
						<?php endforeach ?>
					</tbody>
				</table>

				<p>
					<a class="button" href="<?php echo network_admin_url('admin.php?page=WordfenceWAF#top#blocking') ?>">Update Blocked Countries</a>
				</p>

				<?php wfHelperString::cycle(); ?>

				<h2>Top 10 Failed Logins</h2>

				<table class="wf-striped-table">
					<thead>
						<tr>
							<th>Username</th>
							<th>Login Attempts</th>
							<th>Existing User</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($top_failed_logins as $row): ?>
							<tr class="<?php echo wfHelperString::cycle('odd', 'even') ?>">
								<td><?php echo esc_html($row->username) ?></td>
								<td><?php echo esc_html($row->fail_count) ?></td>
								<td class="<?php echo sanitize_html_class($row->is_valid_user ? 'loginFailValidUsername' : 'loginFailInvalidUsername') ?>"><?php echo $row->is_valid_user ? 'Yes' : 'No' ?></td>
							</tr>
						<?php endforeach ?>
					</tbody>
				</table>

				<p>
					<a class="button" href="<?php echo network_admin_url('admin.php?page=WordfenceWAF&subpage=waf_options#waf-options-bruteforce') ?>">Update Login Security Options</a>
				</p>

				<?php wfHelperString::cycle(); ?>

				<h2>Recently Modified Files</h2>

				<table class="wf-striped-table">
					<thead>
						<tr>
							<th>Modified</th>
							<th>File</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($recently_modified_files as $file_row):
							list($file, $mod_time) = $file_row;
							?>
							<tr class="<?php echo wfHelperString::cycle('odd', 'even') ?>">
								<td style="white-space: nowrap;"><?php echo $this->modTime($mod_time) ?></td>
								<td>
									<pre class="display-file"><?php echo esc_html($this->displayFile($file)) ?></pre>
								</td>
							</tr>
						<?php endforeach ?>
					</tbody>
				</table>

				<?php wfHelperString::cycle(); ?>

				<h2>Updates Needed</h2>

				<?php if ($updates_needed['core']): ?>
					<h4>Core</h4>
					<ul>
						<li>A new version of WordPress (v<?php echo esc_html($updates_needed['core']) ?>) is available.</li>
					</ul>
				<?php endif ?>
				<?php if ($updates_needed['plugins']): ?>
					<h4>Plugins</h4>
					<ul>
						<?php
						foreach ($updates_needed['plugins'] as $plugin):
							$newVersion = ($plugin['newVersion'] == 'Unknown' ? $plugin['newVersion'] : "v{$plugin['newVersion']}");
						?>
							<li>
								A new version of the plugin "<?php echo esc_html("{$plugin['Name']} ({$newVersion})") ?>" is available.
							</li>
						<?php endforeach ?>
					</ul>
				<?php endif ?>
				<?php if ($updates_needed['themes']): ?>
					<h4>Themes</h4>
					<ul>
						<?php
						foreach ($updates_needed['themes'] as $theme):
							$newVersion = ($theme['newVersion'] == 'Unknown' ? $theme['newVersion'] : "v{$theme['newVersion']}");
						?>
							<li>
								A new version of the theme "<?php echo esc_html("{$theme['name']} ({$newVersion})") ?>" is available.
							</li>
						<?php endforeach ?>
					</ul>
				<?php endif ?>
				
				<?php if ($updates_needed['core'] || $updates_needed['plugins'] || $updates_needed['themes']): ?>
					<p><a class="button" href="<?php echo esc_attr(admin_url('update-core.php')) ?>">Update Now</a></p>
				<?php endif ?>

<!--			<p>Generated in --><?php //printf('%.4f seconds', $microseconds) ?><!--</p>-->

			</div>
		</td>
	</tr>
</table>
<!-- End of wrapper table -->
</body>
</html>
