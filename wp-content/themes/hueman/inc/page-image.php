<?php if ( has_post_thumbnail() ): ?>
<div class="page-image">
	<div class="image-container">
		<?php the_post_thumbnail('thumb-large'); ?>
		<?php 
			$caption = get_post(get_post_thumbnail_id())->post_excerpt;
			$description = get_post(get_post_thumbnail_id())->post_content;
			echo '<div class="page-image-text">';
			if ( isset($caption) && $caption ) echo '<div class="caption">'.$caption.'</div>';
			if ( isset($description) && $description ) echo '<div class="description"><i>'.$description.'</i></div>';
			echo '</div>';
		?>
	</div>
</div><!--/.page-image-->
<?php endif; ?>	