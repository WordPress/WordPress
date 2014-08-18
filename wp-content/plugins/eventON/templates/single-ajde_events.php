<?php	
/*
 *	The template for displaying single event
 *
 *	Override this tempalte by coping it to yourtheme/eventon/single-ajde_events.php
 *
 *	@Author: AJDE
 *	@EventON
 *	@version: 0.1
 */
	

	
	get_header();

	do_action('eventon_before_main_content');
	
	while( have_posts() ): the_post();
		
		?>
		<div class='eventon'>
			<h2><?php the_title();?></h2>
			
			<?php the_content(); ?>
			
		</div>
		<?php
	
	endwhile;
	
	do_action('eventon_after_main_content');
	
	get_footer();
?>