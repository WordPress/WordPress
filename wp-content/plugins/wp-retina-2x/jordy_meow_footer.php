<?php

	if ( !function_exists( 'jordy_meow_flattr' ) ) {
		add_action( 'admin_head', 'jordy_meow_flattr', 1 );
		function jordy_meow_flattr () {
			?>
				<script type="text/javascript">
					/* <![CDATA[ */
					    (function() {
					        var s = document.createElement('script'), t = document.getElementsByTagName('script')[0];
					        s.type = 'text/javascript';
					        s.async = true;
					        s.src = '//api.flattr.com/js/0.6/load.js?mode=auto&uid=TigrouMeow';
					        t.parentNode.insertBefore(s, t);
					    })();
					/* ]]> */
				</script>
			<?php
		}
		function by_jordy_meow() {
			echo '<div><span style="font-size: 13px; position: relative; top: -6px;">Developed by <a style="text-decoration: none;" href="https://plus.google.com/+JordyMeow">Jordy Meow</a></span>
				<a class="FlattrButton" style="display:none;" rev="flattr;button:compact;" title="Jordy Meow / WordPress" href="http://profiles.wordpress.org/TigrouMeow/"></a></div>';
		}
	}

	if ( !function_exists( 'jordy_meow_donation' ) ) {
		function jordy_meow_donation($showWPE = false) {
			if ( defined( 'WP_HIDE_DONATION_BUTTONS' ) && WP_HIDE_DONATION_BUTTONS == true )
				return;
			if ( $showWPE ) {
				echo "<a href='http://www.shareasale.com/r.cfm?B=394686&U=767054&M=41388&urllink=' target='_blank'><img style='float: right; margin-top: 5px; margin-left: 15px;' width='120px' height='34px' src='" . plugins_url('/img/wpengine.png', __FILE__) . "' /></a>";
			}
		}
	}

	if ( !function_exists('jordy_meow_footer') ) {
		function jordy_meow_footer() {
			?>
			<div style='text-align: center;margin-top: 25px;'>
			<p><b>This plugin is actively developed and maintained by <a href='https://plus.google.com/+JordyMeow'>Jordy Meow</a></b>.
			<br />
			Please visit <a href='http://www.totorotimes.com'>Totoro Times</a>, my website about Japan & photography.<br />And thanks for following me on <a href='https://twitter.com/TigrouMeow'>Twitter</a> or <a href='https://plus.google.com/+JordyMeow'>Google+</a> :)</p>
			<?php
			if ( !(defined( 'WP_HIDE_DONATION_BUTTONS' ) && WP_HIDE_DONATION_BUTTONS == true) ) {
				?>
				<div><a href='https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=JAWE2XWH7ZE5U' target='_blank'>
					<img style='margin-top: -16px; margin-bottom: -6px;' width='140px' height='34px' src='<?php echo plugins_url('/img/donation.png', __FILE__); ?>' />
				</a></div>
				<?php
			}
			?>
			</div>
			<?php
		}
	}
?>