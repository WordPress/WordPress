<?php
/**
 * @package WPSEO\Admin
 */

?>

<h3 id="wordpress-seo"><?php
	/* translators: %1$s expands to Yoast SEO */
	printf( __( '%1$s settings', 'wordpress-seo' ), 'Yoast SEO' );
	?></h3>
<table class="form-table">
	<tr>
		<th>
			<label
				for="wpseo_author_title"><?php _e( 'Title to use for Author page', 'wordpress-seo' ); ?></label>
		</th>
		<td><input class="regular-text" type="text" id="wpseo_author_title" name="wpseo_author_title"
		           value="<?php echo esc_attr( get_the_author_meta( 'wpseo_title', $user->ID ) ); ?>"/>
		</td>
	</tr>
	<tr>
		<th>
			<label
				for="wpseo_author_metadesc"><?php _e( 'Meta description to use for Author page', 'wordpress-seo' ); ?></label>
		</th>
		<td>
						<textarea rows="3" cols="30" id="wpseo_author_metadesc"
						          name="wpseo_author_metadesc"><?php echo esc_textarea( get_the_author_meta( 'wpseo_metadesc', $user->ID ) ); ?></textarea>
		</td>
	</tr>
	<?php if ( $options['usemetakeywords'] === true ) { ?>
		<tr>
			<th>
				<label
					for="wpseo_author_metakey"><?php _e( 'Meta keywords to use for Author page', 'wordpress-seo' ); ?></label>
			</th>
			<td>
				<input class="regular-text" type="text" id="wpseo_author_metakey"
				       name="wpseo_author_metakey"
				       value="<?php echo esc_attr( get_the_author_meta( 'wpseo_metakey', $user->ID ) ); ?>"/>
			</td>
		</tr>
	<?php } ?>
	<tr>
		<th>
			<label
				for="wpseo_author_exclude"><?php _e( 'Exclude user from Author-sitemap', 'wordpress-seo' ); ?></label>
		</th>
		<td>
			<input class="checkbox double" type="checkbox" id="wpseo_author_exclude"
			       name="wpseo_author_exclude"
			       value="on" <?php echo ( get_the_author_meta( 'wpseo_excludeauthorsitemap', $user->ID ) === 'on' ) ? 'checked' : ''; ?> />
		</td>
	</tr>
</table>
<br/><br/>
