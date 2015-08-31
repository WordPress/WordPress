<?php if (!defined('W3TC')) die(); ?>
<?php foreach ($items as $item): ?>
<h4>
	<a href="<?php echo $item['link']; ?>"><?php echo $item['title']; ?></a>
</h4>
<?php endforeach; ?>

<p style="text-align: center;">
	<a href="<?php echo W3TC_FEED_URL; ?>" target="_blank">View Feed</a>
</p>
