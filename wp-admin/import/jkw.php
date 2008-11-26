<?php
/**
 * Jeromes Keyword Plugin Importer
 *
 * @package WordPress
 * @subpackage Importer
 */

/**
 * Jeromes Keyword Plugin Importer class
 *
 * Will convert Jeromes Keyword Plugin tags to WordPress taxonomy tags.
 *
 * @since 2.3
 */
class JeromesKeyword_Import {

	function header() {
		echo '<div class="wrap">';
		screen_icon();
		echo '<h2>'.__('Import Jerome&#8217;s Keywords').'</h2>';
		echo '<p>'.__('Steps may take a few minutes depending on the size of your database. Please be patient.').'<br /><br /></p>';
	}

	function footer() {
		echo '</div>';
	}

	function greet() {
		echo '<div class="narrow">';
		echo '<p>'.__('Howdy! This imports tags from Jerome&#8217;s Keywords into WordPress tags.').'</p>';
		echo '<p>'.__('This is suitable for Jerome&#8217;s Keywords version 1.x and 2.0a.').'</p>';
		echo '<p><strong>'.__('All existing Jerome&#8217;s Keywords will be removed after import.').'</strong></p>';
		echo '<p><strong>'.__('Don&#8217;t be stupid - backup your database before proceeding!').'</strong></p>';
		echo '<form action="admin.php?import=jkw&amp;step=1" method="post">';
		wp_nonce_field('import-jkw');
		echo '<p class="submit"><input type="submit" name="submit" class="button" value="'.__('Import Version 1.x').'" /></p>';
		echo '</form>';
		echo '<form action="admin.php?import=jkw&amp;step=3" method="post">';
		wp_nonce_field('import-jkw');
		echo '<p class="submit"><input type="submit" name="submit" class="button" value="'.__('Import Version 2.0a').'" /></p>';
		echo '</form>';
		echo '</div>';
	}

	function dispatch() {
		if ( empty($_GET['step']) )
			$step = 0;
		else
			$step = absint($_GET['step']);

		// load the header
		$this->header();

		switch ( $step ) {
			case 0 :
				$this->greet();
				break;
			case 1 :
				check_admin_referer('import-jkw');
				$this->check_V1_post_keyword( true );
				break;
			case 2 :
				check_admin_referer('import-jkw');
				$this->check_V1_post_keyword( false );
				break;
			case 3 :
				check_admin_referer('import-jkw');
				$this->check_V2_post_keyword( true );
				break;
			case 4 :
				check_admin_referer('import-jkw');
				$this->check_V2_post_keyword( false );
				break;
			case 5:
				check_admin_referer('import-jkw');
				$this->cleanup_V2_import();
				break;
			case 6:
				$this->done();
				break;
		}

		// load the footer
		$this->footer();
	}

	function check_V1_post_keyword($precheck = true) {
		global $wpdb;

		echo '<div class="narrow">';
		echo '<p><h3>'.__('Reading Jerome&#8217;s Keywords Tags&#8230;').'</h3></p>';

		// import Jerome's Keywords tags
		$metakeys = $wpdb->get_results("SELECT post_id, meta_id, meta_key, meta_value FROM $wpdb->postmeta WHERE $wpdb->postmeta.meta_key = 'keywords'");
		if ( !is_array($metakeys)) {
			echo '<p>' . __('No Tags Found!') . '</p>';
			return false;
		} else {
			$count = count($metakeys);
			echo '<p>' . sprintf( __ngettext('Done! <strong>%s</strong> post with tags were read.', 'Done! <strong>%s</strong> posts with tags were read.', $count), $count ) . '<br /></p>';
			echo '<ul>';
			foreach ( $metakeys as $post_meta ) {
				if ( $post_meta->meta_value != '' ) {
					$post_keys = explode(',', $post_meta->meta_value);
					foreach ( $post_keys as $keyword ) {
						$keyword = addslashes(trim($keyword));
						if ( '' != $keyword ) {
							echo '<li>' . $post_meta->post_id . '&nbsp;-&nbsp;' . $keyword . '</li>';
							if ( !$precheck )
								wp_add_post_tags($post_meta->post_id, $keyword);
						}
					}
				}
				if ( !$precheck )
					delete_post_meta($post_meta->post_id, 'keywords');
			}
			echo '</ul>';
		}

		echo '<form action="admin.php?import=jkw&amp;step='.($precheck? 2:6).'" method="post">';
		wp_nonce_field('import-jkw');
		echo '<p class="submit"><input type="submit" name="submit" class="button" value="'.__('Next').'" /></p>';
		echo '</form>';
		echo '</div>';
	}

	function check_V2_post_keyword($precheck = true) {
		global $wpdb;

		echo '<div class="narrow">';
		echo '<p><h3>'.__('Reading Jerome&#8217;s Keywords Tags&#8230;').'</h3></p>';

		// import Jerome's Keywords tags
		$tablename = $wpdb->prefix . substr(get_option('jkeywords_keywords_table'), 1, -1);
		$metakeys = $wpdb->get_results("SELECT post_id, tag_name FROM $tablename");
		if ( !is_array($metakeys) ) {
			echo '<p>' . __('No Tags Found!') . '</p>';
			return false;
		} else {
			$count = count($metakeys);
			echo '<p>' . sprintf( __ngettext('Done! <strong>%s</strong> tag were read.', 'Done! <strong>%s</strong> tags were read.', $count), $count ) . '<br /></p>';
			echo '<ul>';
			foreach ( $metakeys as $post_meta ) {
				$keyword = addslashes(trim($post_meta->tag_name));
				if ( $keyword != '' ) {
					echo '<li>' . $post_meta->post_id . '&nbsp;-&nbsp;' . $keyword . '</li>';
					if ( !$precheck )
						wp_add_post_tags($post_meta->post_id, $keyword);
				}
			}
		echo '</ul>';
		}
		echo '<form action="admin.php?import=jkw&amp;step='.($precheck? 4:5).'" method="post">';
		wp_nonce_field('import-jkw');
		echo '<p class="submit"><input type="submit" name="submit" class="button" value="'.__('Next').'" /></p>';
		echo '</form>';
		echo '</div>';
	}

	function cleanup_V2_import() {
		global $wpdb;

		/* options from V2.0a (jeromes-keywords.php) */
		$options = array('version', 'keywords_table', 'query_varname', 'template', 'meta_always_include', 'meta_includecats', 'meta_autoheader', 'search_strict', 'use_feed_cats', 'post_linkformat', 'post_tagseparator', 'post_includecats', 'post_notagstext', 'cloud_linkformat', 'cloud_tagseparator', 'cloud_includecats', 'cloud_sortorder', 'cloud_displaymax', 'cloud_displaymin', 'cloud_scalemax', 'cloud_scalemin');

		$wpdb->query('DROP TABLE IF EXISTS ' . $wpdb->prefix . substr(get_option('jkeywords_keywords_table'), 1, -1));

		foreach ( $options as $o )
			delete_option('jkeywords_' . $o);

		$this->done();
	}

	function done() {
		echo '<div class="narrow">';
		echo '<p><h3>'.__('Import Complete!').'</h3></p>';
		echo '</div>';
	}

	function JeromesKeyword_Import() {
	}

}

// create the import object
$jkw_import = new JeromesKeyword_Import();

// add it to the import page!
register_importer('jkw', 'Jerome&#8217;s Keywords', __('Import Jerome&#8217;s Keywords into WordPress tags.'), array($jkw_import, 'dispatch'));

?>
