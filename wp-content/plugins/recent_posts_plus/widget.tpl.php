<?php echo $before_widget; ?>
<?php if ( $title ) echo $before_title . $title . $after_title; ?>
<ul>
<?php while ( $r->have_posts() ) : $r->the_post(); ?>

	<li>
		<div class="post-title">
			<a href="<?php the_permalink(); ?>"><?php get_the_title() ? the_title() : the_ID(); ?></a>
		</div>

		<div class="post-desc">
			<?php the_content() ?>
		</div>

		<div class="post-stats">
			<ul>
				<li>
					<span class="post-comments">
						<?php echo comments_number( 'no comments', 'One Comment', '% Comments' ); ?>
					</span>
				</li>

				<li>
					<span class="post-date"><?php echo $this->time_since(get_the_date()); ?></span>
				</li>
			</ul>
		</div>
	</li>

<?php endwhile; ?>
</ul>
<?php echo $after_widget; ?>
