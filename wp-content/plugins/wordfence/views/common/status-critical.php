<?php
if (!defined('WORDFENCE_VERSION')) { exit; }
/**
 * Expects $id, $title, $subtitle, and $link, and $linkLabel to be defined.
 * If $linkLabel is null, the link will be hidden.
 * $linkNewWindow can optionally be defined and defaults to false.
 */

if (!isset($linkNewWindow)) { $linkNewWindow = false; }
?>
<div id="<?php echo esc_attr($id); ?>" class="wf-status-detail">
	<div class="wf-status-critical">
		<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 116.93 108.46"><path d="M115.82,96.14,65.76,4.37a8.33,8.33,0,0,0-3.06-3.2,8.24,8.24,0,0,0-8.47,0,8.34,8.34,0,0,0-3.07,3.2L1.11,96.14a7.78,7.78,0,0,0,.13,8.21,8.16,8.16,0,0,0,3,3,8.13,8.13,0,0,0,4.14,1.11H108.52a8.36,8.36,0,0,0,7.17-4.11,7.78,7.78,0,0,0,.13-8.21Zm-49-6.52a2.09,2.09,0,0,1-.62,1.53,2,2,0,0,1-1.46.62H52.21a2,2,0,0,1-1.46-.62,2.08,2.08,0,0,1-.62-1.53V77.24a2.08,2.08,0,0,1,.62-1.53,2,2,0,0,1,1.46-.62H64.72a2,2,0,0,1,1.47.62,2.09,2.09,0,0,1,.62,1.53V89.62Zm-.13-24.37A1.45,1.45,0,0,1,66,66.32a2.66,2.66,0,0,1-1.53.42H52.4a2.81,2.81,0,0,1-1.56-.42,1.25,1.25,0,0,1-.65-1.08L49.08,35.46a1.56,1.56,0,0,1,.65-1.37,2.52,2.52,0,0,1,1.56-.72H65.64a2.51,2.51,0,0,1,1.57.72,1.43,1.43,0,0,1,.65,1.24Zm0,0"/></svg>
	</div>
	<p class="wf-status-detail-title"><?php echo esc_html($title); ?></p>
	<p class="wf-status-detail-subtitle"><?php echo esc_html($subtitle); ?></p>
	<p class="wf-status-detail-link"><?php if ($linkLabel !== null): ?><a href="<?php echo esc_attr($link); ?>"<?php echo ($linkNewWindow ? ' target="_blank" rel="noopener noreferrer"' : ''); ?>><?php echo esc_html($linkLabel); ?></a><?php endif; ?></p>
</div>
