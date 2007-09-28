<?php

class UTW_Import {

	function header()  {
		echo '<div class="wrap">';
		echo '<h2>'.__('Import Ultimate Tag Warrior').'</h2>';
		echo '<p>'.__('Steps may take a few minutes depending on the size of your database. Please be patient.').'<br /><br /></p>';
	}

	function footer() {
		echo '</div>';
	}

	function greet() {
		echo '<div class="narrow">';
		echo '<p>'.__('Howdy! This imports tags from an existing Ultimate Tag Warrior 3 installation into this blog using the new WordPress native tagging structure.').'</p>';
		echo '<p>'.__('This has not been tested on any other versions of Ultimate Tag Warrior. Mileage may vary.').'</p>';
		echo '<p>'.__('To accommodate larger databases for those tag-crazy authors out there, we have made this into an easy 5-step program to help you kick that nasty UTW habit. Just keep clicking along and we will let you know when you are in the clear!').'</p>';
		echo '<p><strong>'.__('Don&#8217;t be stupid - backup your database before proceeding!').'</strong></p>';
		echo '<form action="admin.php?import=utw&amp;step=1" method="post">';
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

		if ( $step > 1 )
			check_admin_referer('import-utw');

		// load the header
		$this->header();

		switch ( $step ) {
			case 0 :
				$this->greet();
				break;
			case 1 :
				$this->import_tags();
				break;
			case 2 :
				$this->import_posts();
				break;
			case 3:
				$this->import_t2p();
				break;
			case 4:
				$this->cleanup_import();
				break;
		}

		// load the footer
		$this->footer();
	}


	function import_tags ( ) {
		echo '<div class="narrow">';
		echo '<p><h3>'.__('Reading UTW Tags&#8230;').'</h3></p>';

		$tags = $this->get_utw_tags();

		// if we didn't get any tags back, that's all there is folks!
		if ( !is_array($tags) ) {
			echo '<p>' . __('No Tags Found!') . '</p>';
			return false;
		}
		else {

			// if there's an existing entry, delete it
			if ( get_option('utwimp_tags') ) {
				delete_option('utwimp_tags');
			}

			add_option('utwimp_tags', $tags);


			$count = count($tags);

			echo '<p>' . sprintf( __('Done! <strong>%s</strong> tags were read.'), $count ) . '<br /></p>';
			echo '<p>' . __('The following tags were found:') . '</p>';

			echo '<ul>';

			foreach ( $tags as $tag_id => $tag_name ) {

				echo '<li>' . $tag_name . '</li>';

			}

			echo '</ul>';

			echo '<br />';

			echo '<p>' . __('If you don&#8217;t want to import any of these tags, you should delete them from the UTW tag management page and then re-run this import.') . '</p>';


		}

		echo '<form action="admin.php?import=utw&amp;step=2" method="post">';
		wp_nonce_field('import-utw');
		echo '<p class="submit"><input type="submit" name="submit" value="'.__('Step 2 &raquo;').'" /></p>';
		echo '</form>';
		echo '</div>';
	}


	function import_posts ( ) {
		echo '<div class="narrow">';
		echo '<p><h3>'.__('Reading UTW Post Tags&#8230;').'</h3></p>';

		// read in all the UTW tag -> post settings
		$posts = $this->get_utw_posts();

		// if we didn't get any tags back, that's all there is folks!
		if ( !is_array($posts) ) {
			echo '<p>' . __('No posts were found to have tags!') . '</p>';
			return false;
		}
		else {

			// if there's an existing entry, delete it
			if ( get_option('utwimp_posts') ) {
				delete_option('utwimp_posts');
			}

			add_option('utwimp_posts', $posts);


			$count = count($posts);

			echo '<p>' . sprintf( __('Done! <strong>%s</strong> tag to post relationships were read.'), $count ) . '<br /></p>';

		}

		echo '<form action="admin.php?import=utw&amp;step=3" method="post">';
		wp_nonce_field('import-utw');
		echo '<p class="submit"><input type="submit" name="submit" value="'.__('Step 3 &raquo;').'" /></p>';
		echo '</form>';
		echo '</div>';

	}


	function import_t2p ( ) {

		echo '<div class="narrow">';
		echo '<p><h3>'.__('Adding Tags to Posts&#8230;').'</h3></p>';

		// run that funky magic!
		$tags_added = $this->tag2post();

		echo '<p>' . sprintf( __('Done! <strong>%s</strong> tags were added!'), $tags_added ) . '<br /></p>';

		echo '<form action="admin.php?import=utw&amp;step=4" method="post">';
		wp_nonce_field('import-utw');
		echo '<p class="submit"><input type="submit" name="submit" value="'.__('Step 4 &raquo;').'" /></p>';
		echo '</form>';
		echo '</div>';

	}


	function get_utw_tags ( ) {

		global $wpdb;

		// read in all the tags from the UTW tags table: should be wp_tags
		$tags_query = "SELECT tag_id, tag FROM " . $wpdb->prefix . "tags";

		$tags = $wpdb->get_results($tags_query);

		// rearrange these tags into something we can actually use
		foreach ( $tags as $tag ) {

			$new_tags[$tag->tag_id] = $tag->tag;

		}

		return $new_tags;

	}

	function get_utw_posts ( ) {

		global $wpdb;

		// read in all the posts from the UTW post->tag table: should be wp_post2tag
		$posts_query = "SELECT tag_id, post_id FROM " . $wpdb->prefix . "post2tag";

		$posts = $wpdb->get_results($posts_query);

		return $posts;

	}


	function tag2post ( ) {

		// get the tags and posts we imported in the last 2 steps
		$tags = get_option('utwimp_tags');
		$posts = get_option('utwimp_posts');

		// null out our results
		$tags_added = 0;

		// loop through each post and add its tags to the db
		foreach ( $posts as $this_post ) {

			$the_post = (int) $this_post->post_id;
			$the_tag = (int) $this_post->tag_id;

			// what's the tag name for that id?
			$the_tag = $tags[$the_tag];

			// screw it, just try to add the tag
			wp_add_post_tags($the_post, $the_tag);

			$tags_added++;

		}

		// that's it, all posts should be linked to their tags properly, pending any errors we just spit out!
		return $tags_added;


	}


	function cleanup_import ( ) {

		delete_option('utwimp_tags');
		delete_option('utwimp_posts');

		$this->done();

	}


	function done ( ) {

		echo '<div class="narrow">';
		echo '<p><h3>'.__('Import Complete!').'</h3></p>';

		echo '<p>' . __('OK, so we lied about this being a 5-step program! You&#8217;re done!') . '</p>';

		echo '<p>' . __('Now wasn&#8217;t that easy?') . '</p>';

		echo '</div>';

	}


	function UTW_Import ( ) {

		// Nothing.

	}

}


// create the import object
$utw_import = new UTW_Import();

// add it to the import page!
register_importer('utw', 'Ultimate Tag Warrior', __('Import Ultimate Tag Warrior tags into the new native tagging structure.'), array($utw_import, 'dispatch'));

?>
