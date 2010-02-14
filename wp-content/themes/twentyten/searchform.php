    <form id="searchform" name="searchform" method="get" action="<?php echo home_url(); ?>">
		<div>
			<label for="s"><?php _e( 'Search', 'twentyten' ); ?></label>
			<input type="text" id="s" name="s" />
			<input type="submit" id="searchsubmit" value="<?php esc_attr_e( 'Search', 'twentyten' ); ?>" />
		</div>
    </form>