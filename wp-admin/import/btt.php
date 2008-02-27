<?php

class BunnyTags_Import {

	function header() {
		echo '<div class="wrap">';
		echo '<h2>'.__('Import Bunny&#8217;s Technorati Tags').'</h2>';
		echo '<p>'.__('Steps may take a few minutes depending on the size of your database. Please be patient.').'<br /><br /></p>';
	}

	function footer() {
		echo '</div>';
	}

	function greet() {
		echo '<div class="narrow">';
		echo '<p>'.__('Howdy! This imports tags from Bunny&#8217;s Technorati Tags into WordPress tags.').'</p>';
		echo '<p>'.__('This is suitable for Bunny&#8217;s Technorati Tags version 0.6.').'</p>';
		echo '<p><strong>'.__('All existing Bunny&#8217;s Technorati Tags will be removed after import.').'</strong></p>';
		echo '<p><strong>'.__('Don&#8217;t be stupid - backup your database before proceeding!').'</strong></p>';
		echo '<form action="admin.php?import=btt&amp;step=1" method="post">';
		wp_nonce_field('import-btt');
		echo '<p class="submit"><input type="submit" name="submit" value="'.__('Import Tags').'" /></p>';
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
				check_admin_referer('import-btt');
				$this->check_post_keyword( true );
				break;
			case 2 :
				check_admin_referer('import-btt');
				$this->check_post_keyword( false );
				break;
			case 3:
				$this->done();
				break;
		}

		// load the footer
		$this->footer();
	}

	function check_post_keyword($precheck = true) {
		global $wpdb;

		echo '<div class="narrow">';
		echo '<p><h3>'.__('Reading Bunny&#8217;s Technorati Tags&#8230;').'</h3></p>';

		// import Bunny's Keywords tags
		$metakeys = $wpdb->get_results("SELECT post_id, meta_id, meta_key, meta_value FROM $wpdb->postmeta WHERE $wpdb->postmeta.meta_key = 'tags'");
		if ( !is_array($metakeys)) {
			echo '<p>' . __('No Tags Found!') . '</p>';
			return false;
		} else {
			$count = count($metakeys);
			echo '<p>' . sprintf( __ngettext('Done! <strong>%s</strong> post with tags were read.', 'Done! <strong>%s</strong> posts with tags were read.', $count), $count ) . '<br /></p>';
			echo '<ul>';
			foreach ( $metakeys as $post_meta ) {
				if ( $post_meta->meta_value != '' ) {
					$post_keys = explode(' ', $post_meta->meta_value);
					foreach ( $post_keys as $keyword ) {
						$keyword = addslashes(trim(str_replace('+',' ',$keyword)));
						if ( '' != $keyword ) {
							echo '<li>' . $post_meta->post_id . '&nbsp;-&nbsp;' . $keyword . '</li>';
							if ( !$precheck )
								wp_add_post_tags($post_meta->post_id, $keyword);
						}
					}
				}
				if ( !$precheck )
					delete_post_meta($post_meta->post_id, 'tags');
			}
			echo '</ul>';
		}

		echo '<form action="admin.php?import=btt&amp;step='.($precheck? 2:3).'" method="post">';
		wp_nonce_field('import-btt');
		echo '<p class="submit"><input type="submit" name="submit" value="'.__('Next').'" /></p>';
		echo '</form>';
		echo '</div>';
	}

	function done() {
		echo '<div class="narrow">';
		echo '<p><h3>'.__('Import Complete!').'</h3></p>';
		echo '</div>';
	}

	function BunnyTags_Import() {
	}

}

// create the import object
$btt_import = new BunnyTags_Import();

// add it to the import page!
register_importer('btt', 'Bunny&#8217;s Technorati Tags', __('Import Bunny&#8217;s Technorati Tags into WordPress tags.'), array($btt_import, 'dispatch'));

?>
