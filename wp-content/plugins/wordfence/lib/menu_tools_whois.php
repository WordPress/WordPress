<?php
if (!defined('WORDFENCE_VERSION')) { exit; }
?>
<script type="application/javascript">
	(function($) {
		$(function() {
			document.title = "<?php esc_attr_e('Whois Lookup', 'wordfence'); ?>" + " \u2039 " + WFAD.basePageName;
		});
	})(jQuery);
</script>

<div class="wordfenceModeElem" id="wordfenceMode_whois"></div>

<div id="wf-tools-whois">
	<div class="wf-section-title">
		<h2><?php _e('Whois Lookup', 'wordfence') ?></h2>
		<span><?php printf(__('<a href="%s" target="_blank" rel="noopener noreferrer" class="wf-help-link">Learn more<span class="wf-hidden-xs"> about Whois Lookup</span></a>', 'wordfence'), wfSupportController::esc_supportURL(wfSupportController::ITEM_TOOLS_WHOIS_LOOKUP)); ?>
			<i class="wf-fa wf-fa-external-link" aria-hidden="true"></i></span>
	</div>

	<p><?php _e("The whois service gives you a way to look up who owns an IP address or domain name that is visiting your website or is engaging in malicious activity on your website.", 'wordfence') ?></p>

	<div>

		<div class="wf-form wf-flex-row">
			<div class="wf-flex-row-1">
				<input type="text" class="wf-form-control" name="whois" id="wfwhois" value="" maxlength="255" onkeydown="if(event.keyCode == 13){ whois(jQuery('#wfwhois').val()) }"/>
			</div>
			<div class="wf-flex-row-0 wf-padding-add-left">
				<input type="button" name="whoisbutton" id="whoisbutton" class="wf-btn wf-btn-primary" value="Look up IP or Domain" onclick="whois(jQuery('#wfwhois').val());">
			</div>
		</div>
		<?php if (isset($_GET['wfnetworkblock']) && $_GET['wfnetworkblock']) { ?>
			<h2><?php _e('How to block a network', 'wordfence') ?></h2>
			<p style="width: 600px;">
				<?php printf(__("You've chosen to block the network that <span style=\"color: #F00;\">%s</span> is part of. We've marked the networks we found that this IP address belongs to in red below. Make sure you read all the WHOIS information so that you see all networks this IP belongs to. We recommend blocking the network with the lowest number of addresses. You may find this is listed at the end as part of the 'rWHOIS' query which contacts the local WHOIS server that is run by the network administrator."), esc_html($_GET['whoisval'])) ?>
			</p>
		<?php } ?>
		<div id="wfrawhtml" class="wf-padding-add-top"></div>
	</div>
	<script type="text/x-jquery-template" id="wfBlockedRangesTmpl">
		<div>
			<div style="border-bottom: 1px solid #CCC; padding-bottom: 10px; margin-bottom: 10px;">
				<table border="0" style="width: 100%">
					{{each(idx, elem) results}}
					<tr>
						<td></td>
					</tr>
					{{/each}}
				</table>
			</div>
		</div>
	</script>
	<script type="text/javascript">
		var whoisval = "<?php if (isset($_GET['whoisval'])) {
			echo esc_js($_GET['whoisval']);
		} ?>";
		if (whoisval) {
			jQuery(function() {
				jQuery('#wfwhois').val(whoisval);
				whois(whoisval);
			});
		}
	</script>

	<script type="text/x-jquery-template" id="wfWhoisBlock">
		<div class="wf-block wf-active">
			<div class="wf-block-header">
				<div class="wf-block-header-content">
					<div class="wf-block-title">
						<strong><?php _e('Whois Lookup', 'wordfence') ?> <a>${ip}</a></strong>
					</div>
				</div>
			</div>
			<div class="wf-block-content wf-clearfix">
				<ul class="wf-block-list">
					<li>
						<div class="wf-padding-add-top">{{html whois}}</div>
					</li>
				</ul>
			</div>
		</div>
	</script>

</div>

<script type="text/javascript">
	function whois(ip) {
		var val = ip.replace(' ', '');
		if (!/\w+/.test(val)) {
			WFAD.colorboxModal('300px', "Enter a valid IP or domain", "Please enter a valid IP address or domain name for your whois lookup.");
			return;
		}
		var whoisButton = jQuery('#whoisbutton').attr('disabled', 'disabled')
			.attr('value', 'Loading...');
		WFAD.ajax('wordfence_whois', {
			val: val
		}, function(res) {
			whoisButton.removeAttr('disabled')
				.attr('value', 'Look up IP or Domain');
			if (res.ok) {
				var whoisHTML = WFAD.completeWhois(res, true);
				console.log(whoisHTML);
				jQuery('#wfrawhtml').html(jQuery('#wfWhoisBlock').tmpl({
					ip: val,
					whois: whoisHTML
				}));
			}
		});
	}
</script>