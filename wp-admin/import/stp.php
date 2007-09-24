<?php
class STP_Import {
	function header()  {
		echo '<div class="wrap">';
		echo '<h2>'.__('Import Simple Tagging').'</h2>';
		echo '<p>'.__('Steps may take a few minutes depending on the size of your database. Please be patient.').'<br /><br /></p>';
	}

	function footer() {
		echo '</div>';
	}

	function greet() {
		echo '<div class="narrow">';
		echo '<p>'.__('Howdy! This imports tags from an existing Simple Tagging 1.6.2 installation into this blog using the new WordPress native tagging structure.').'</p>';
		echo '<p>'.__('This has not been tested on any other versions of Simple Tagging. Mileage may vary.').'</p>';
		echo '<p>'.__('To accommodate larger databases for those tag-crazy authors out there, we have made this into an easy 4-step program to help you kick that nasty Simple Tagging habit. Just keep clicking along and we will let you know when you are in the clear!').'</p>';
		echo '<p><strong>'.__('Don&#8217;t be stupid - backup your database before proceeding!').'</strong></p>';
		echo '<form action="admin.php?import=stp&amp;step=1" method="post">';
		wp_nonce_field('import-stp');
		echo '<p class="submit"><input type="submit" name="submit" value="'.__('Step 1 &raquo;').'" /></p>';
		echo '</form>';
		echo '</div>';
	}

	function dispatch () {
		if ( empty( $_GET['step'] ) ) {
			$step = 0;
		} else {
			$step = (int) $_GET['step'];
		}
		// load the header
		$this->header();
		switch ( $step ) {
			case 0 :
				$this->greet();
				break;
			case 1 :
				check_admin_referer('import-stp');
				$this->import_posts();
				break;
			case 2:
				check_admin_referer('import-stp');
				$this->import_t2p();
				break;
			case 3:
				check_admin_referer('import-stp');
				$this->cleanup_import();
				break;
		}
		// load the footer
		$this->footer();
	}


	function import_posts ( ) {
		echo '<div class="narrow">';
		echo '<p><h3>'.__('Reading STP Post Tags&#8230;').'</h3></p>';

		// read in all the STP tag -> post settings
		$posts = $this->get_stp_posts();

		// if we didn't get any tags back, that's all there is folks!
		if ( !is_array($posts) ) {
			echo '<p>' . __('No posts were found to have tags!') . '</p>';
			return false;
		}
		else {
			// if there's an existing entry, delete it
			if ( get_option('stpimp_posts') ) {
				delete_option('stpimp_posts');
			}
			
			add_option('stpimp_posts', $posts);
			$count = count($posts);
			echo '<p>' . sprintf( __('Done! <strong>%s</strong> tag to post relationships were read.'), $count ) . '<br /></p>';
		}

		echo '<form action="admin.php?import=stp&amp;step=2" method="post">';
		wp_nonce_field('import-stp');
		echo '<p class="submit"><input type="submit" name="submit" value="'.__('Step 2 &raquo;').'" /></p>';
		echo '</form>';
		echo '</div>';
	}


	function import_t2p ( ) {
		echo '<div class="narrow">';
		echo '<p><h3>'.__('Adding Tags to Posts&#8230;').'</h3></p>';
		
		// run that funky magic!
		$tags_added = $this->tag2post();
		
		echo '<p>' . sprintf( __('Done! <strong>%s</strong> tags where added!'), $tags_added ) . '<br /></p>';
		echo '<form action="admin.php?import=stp&amp;step=3" method="post">';
		wp_nonce_field('import-stp');
		echo '<p class="submit"><input type="submit" name="submit" value="'.__('Step 3 &raquo;').'" /></p>';
		echo '</form>';
		echo '</div>';
	}

	function get_stp_posts ( ) {
		global $wpdb;
		// read in all the posts from the STP post->tag table: should be wp_post2tag
		$posts_query = "SELECT post_id, tag_name FROM " . $wpdb->prefix . "stp_tags";
		$posts = $wpdb->get_results($posts_query);
		return $posts;
	}

	function tag2post ( ) {
		global $wpdb;

		// get the tags and posts we imported in the last 2 steps
		$posts = get_option('stpimp_posts');

		// null out our results
		$tags_added = 0;

		// loop through each post and add its tags to the db
		foreach ( $posts as $this_post ) {
			$the_post = (int) $this_post->post_id;
			$the_tag = $wpdb->escape($this_post->tag_name);
			// try to add the tag
			wp_add_post_tags($the_post, $the_tag);
			$tags_added++;
		}

		// that's it, all posts should be linked to their tags properly, pending any errors we just spit out!
		return $tags_added;
	}

	function cleanup_import ( ) {
		delete_option('stpimp_posts');
		$this->done();
	}

	function done ( ) {
		echo '<div class="narrow">';
		echo '<p><h3>'.__('Import Complete!').'</h3></p>';
		echo '<p>' . __('OK, so we lied about this being a 4-step program! You&#8217;re done!') . '</p>';
		echo '<p>' . __('Now wasn&#8217;t that easy?') . '</p>';
		echo '</div>';
	}

	function STP_Import ( ) {
		// Nothing.
	}
}

// create the import object
$stp_import = new STP_Import();

// add it to the import page!
register_importer('stp', 'Simple Tagging', __('Import Simple Tagging tags into the new native tagging structure.'), array($stp_import, 'dispatch'));
?>