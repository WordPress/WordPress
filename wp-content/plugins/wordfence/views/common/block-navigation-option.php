<?php
if (!defined('WORDFENCE_VERSION')) { exit; }
/**
 * Presents a block navigation option with image, title, and subtitle.
 *
 * Expects $id, $title, $img, $link, and $subtitle to be defined.
 *
 * @var string $id The element ID.
 * @var string $title The option's title.
 * @var string $img The image name. If SVG, it will be inserted as an svg element rather than img.
 * @var string $link The link for the option to go to.
 * @var string $subtitle Subtitle for the option.
 */
?>
<div id="<?php echo esc_attr($id); ?>" class="wf-block-navigation-option">
	<?php if (preg_match('/\.svg$/i', $img)) : ?>
		<?php
		$contents = file_get_contents(WORDFENCE_PATH . '/images/' . $img);
		$contents = preg_replace('/<svg\s+xmlns="[^"]*"/i', '<svg', $contents);
		$contents = preg_replace('/(<svg[^>]+)/i', '${1} class="wf-block-navigation-option-icon"', $contents);
		echo $contents;
		?>
	<?php else: ?>
	<img src="<?php echo esc_attr(wfUtils::getBaseURL() . '/images/' . $img); ?>" class="wf-block-navigation-option-icon" alt="<?php echo esc_attr($title); ?>">
	<?php endif; ?> 
	<div class="wf-block-navigation-option-content">
		<h4><a href="<?php echo esc_attr($link); ?>"><?php echo esc_html($title); ?></a></h4>
		<p><?php echo esc_html($subtitle); ?></p>
	</div>
</div>
<script type="application/javascript">
	(function($) {
		$('#<?php echo esc_attr($id); ?>').on('click', function() {
			window.location.href = $(this).find('a').attr('href');
		});
	})(jQuery);
</script>