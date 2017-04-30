<article <?php hybrid_attr( 'post' ); ?>>

	<header class="entry-header">
		<h1 class="entry-title"><?php _e( 'Nothing found', 'stargazer' ); ?></h1>
	</header><!-- .entry-header -->

	<div <?php hybrid_attr( 'entry-content' ); ?>>
		<?php echo wpautop( __( 'Apologies, but no entries were found.', 'stargazer' ) ); ?>
	</div><!-- .entry-content -->

</article><!-- .entry -->