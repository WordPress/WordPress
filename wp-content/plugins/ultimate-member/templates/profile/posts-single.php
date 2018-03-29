	<?php while ($ultimatemember->shortcodes->loop->have_posts()) { $ultimatemember->shortcodes->loop->the_post(); $post_id = get_the_ID(); ?>

		<div class="um-item">
			<div class="um-item-link"><i class="um-icon-ios-paper"></i><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></div>
			
			<?php if ( has_post_thumbnail( $post_id ) ) {
					$image_id = get_post_thumbnail_id( $post_id );
					$image_url = wp_get_attachment_image_src( $image_id, 'full', true );
			?>
			
			<div class="um-item-img"><a href="<?php the_permalink(); ?>"><?php echo get_the_post_thumbnail( $post_id, 'medium' ); ?></a></div>
			
			<?php } ?>
			
			<div class="um-item-meta">
				<span><?php echo sprintf(__('%s ago','ultimate-member'), human_time_diff( get_the_time('U'), current_time('timestamp') ) ); ?></span>
				<span><?php echo __('in','ultimate-member');?>: <?php the_category( ', ' ); ?></span>
				<span><?php comments_number( __('no comments','ultimate-member'), __('1 comment','ultimate-member'), __('% comments','ultimate-member') ); ?></span>
			</div>
		</div>
		
	<?php } ?>
	
	<?php if ( isset($ultimatemember->shortcodes->modified_args) && $ultimatemember->shortcodes->loop->have_posts() && $ultimatemember->shortcodes->loop->found_posts >= 10 ) { ?>
	
		<div class="um-load-items">
			<a href="#" class="um-ajax-paginate um-button" data-hook="um_load_posts" data-args="<?php echo $ultimatemember->shortcodes->modified_args; ?>"><?php _e('load more posts','ultimate-member'); ?></a>
		</div>
		
	<?php } ?>