<?php
if (!defined('WORDFENCE_VERSION')) { exit; }
/** 
 * Expects $id, $diameter (in pixels), $percentage (as decimal value), $inactiveColor, and $activeColor to be defined. 
 * $animateIn is optional and defaults to true.
 * $strokeWidth is option and defaults to 3 (pixels).
 * 
 * @var string $id
 */

if (!isset($animateIn)) { $animateIn = true; }
if (!isset($strokeWidth)) { $strokeWidth = 3; }

$strokeWidth = intval($strokeWidth);
$diameter = intval($diameter);

?>
<div id="<?php echo esc_attr($id); ?>" class="wf-status-circular"></div>
<script type="application/javascript">
	(function($) {
		$('#<?php echo esc_attr($id); ?>').wfCircularProgress({
			endPercent: <?php echo $percentage; ?>,
			color: '<?php echo esc_attr($activeColor); ?>',
			inactiveColor: '<?php echo esc_attr($inactiveColor); ?>',
			strokeWidth: <?php echo $strokeWidth; ?>,
			diameter: <?php echo $diameter; ?>,
		});
	})(jQuery);
</script>