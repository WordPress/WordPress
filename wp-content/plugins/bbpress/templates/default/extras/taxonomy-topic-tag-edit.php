<?php

/**
 * Topic Tag Edit
 *
 * @package bbPress
 * @subpackage Theme
 */

get_header(); ?>

	<?php do_action( 'bbp_before_main_content' ); ?>

	<?php do_action( 'bbp_template_notices' ); ?>

	<div id="topic-tag" class="bbp-topic-tag">
		<h1 class="entry-title"><?php printf( __( 'Topic Tag: %s', 'bbpress' ), '<span>' . bbp_get_topic_tag_name() . '</span>' ); ?></h1>

		<div class="entry-content">

			<?php bbp_get_template_part( 'content', 'topic-tag-edit' ); ?>

		</div>
	</div><!-- #topic-tag -->

	<?php do_action( 'bbp_after_main_content' ); ?>

<?php get_sidebar(); ?>
<?php get_footer(); ?>
