<?php
if (!defined('WORDFENCE_VERSION')) { exit; }
/**
 * Presents the country selection modal.
 */

require(WORDFENCE_PATH . 'lib/wfBulkCountries.php'); /** @var array $wfBulkCountries */
asort($wfBulkCountries);
$letters = '';
foreach ($wfBulkCountries as $name) {
	$l = strtoupper(substr($name, 0, 1));
	$test = strtoupper(substr($letters, -1));
	if ($l != $test) {
		$letters .= $l;
	}
}
$letters = str_split($letters);
?>
<script type="text/x-jquery-template" id="wfTmpl_countrySelector">
	<div class="wf-modal" id="wf-country-selector">
		<div class="wf-modal-header">
			<div class="wf-modal-header-content">
				<div class="wf-modal-title">
					<?php _e('Select Countries to Block from List', 'wordfence'); ?>
				</div>
			</div>
			<div class="wf-modal-header-action">
				<a href="#" class="wf-btn wf-btn-default wf-btn-callout-subtle wf-countries-shortcut" id="wf-country-selector-block-all" data-shortcut="select"><?php _e('Block All', 'wordfence'); ?></a>&nbsp;&nbsp;<a href="#" class="wf-btn wf-btn-default wf-btn-callout-subtle wf-countries-shortcut" id="wf-country-selector-unblock-all" data-shortcut="deselect"><?php _e('Unblock All', 'wordfence'); ?></a>
			</div>
		</div>
		<div class="wf-modal-content">
			<ul class="wf-country-selector-controls">
				<li>
					<ul class="wf-country-selector-section-options"> 
						<?php
						foreach ($letters as $l) {
							echo '<li><a href="#" data-letter="' . esc_attr($l) . '">' . esc_html($l) . '</a></li>';
						}
						?>
					</ul>
				</li>
			</ul>
			<div class="wf-country-selector-outer-wrapper">
				<div class="wf-country-selector-inner-wrapper">
					<div class="wf-country-selector-options">
					<?php
					$current = '';
					foreach ($wfBulkCountries as $code => $name) {
						$test = strtoupper(substr($name, 0, 1));
						if ($test != $current) {
							if ($current != '') {
								echo '</ul>';
							}
							$current = $test;
					?>
						<ul class="wf-blocked-countries" data-letter="<?php echo esc_attr($current); ?>">
						<?php
						}
						?>
							<li id="wf-country-option-<?php echo esc_attr(strtolower($code)); ?>" data-country="<?php echo esc_attr($code); ?>"><a href="#"><?php echo esc_html($name); ?></a></li>
					<?php
					}
							
					if ($current != '') {
						echo '</ul>';
					}
					?>
					</div>
				</div>
			</div>
		</div>
		<div class="wf-modal-footer">
			<ul class="wf-flex-horizontal wf-flex-full-width">
				<li id="wf-country-selector-count"></li>
				<li class="wf-right"><a href="#" class="wf-btn wf-btn-default wf-btn-callout-subtle" id="wf-country-selector-cancel"><?php _e('Back', 'wordfence'); ?></a>&nbsp;&nbsp;<a href="#" class="wf-btn wf-btn-primary wf-btn-callout-subtle" id="wf-country-selector-confirm"><?php _e('Save', 'wordfence'); ?></a></li>
			</ul>
		</div>
	</div>
</script>
