<?php

class WP_Import {

	var $posts = array ();
	var $posts_processed = array ();
    // Array of arrays. [[0] => XML fragment, [1] => New post ID]
	var $file;
	var $id;
	var $mtnames = array ();
	var $newauthornames = array ();
	var $j = -1;

	function header() {
		echo '<div class="wrap">';
		echo '<h2>'.__('Import WordPress').'</h2>';
	}

	function footer() {
		echo '</div>';
	}

	function unhtmlentities($string) { // From php.net for < 4.3 compat
		$trans_tbl = get_html_translation_table(HTML_ENTITIES);
		$trans_tbl = array_flip($trans_tbl);
		return strtr($string, $trans_tbl);
	}

	function greet() {
		echo '<div class="narrow">';
		echo '<p>'.__('Howdy! Upload your WordPress eXtended RSS (WXR) file and we&#8217;ll import the posts, comments, custom fields, and categories into this blog.').'</p>';
		echo '<p>'.__('Choose a WordPress WXR file to upload, then click Upload file and import.').'</p>';
		wp_import_upload_form("admin.php?import=wordpress&amp;step=1");
		echo '</div>';
	}

	function get_tag( $string, $tag ) {
		global $wpdb;
		preg_match("|<$tag.*?>(.*?)</$tag>|is", $string, $return);
		$return = preg_replace('|^<!\[CDATA\[(.*)\]\]>$|s', '$1', $return[1]);
		$return = $wpdb->escape( trim( $return ) );
		return $return;
	}

	function users_form($n) {
		global $wpdb, $testing;
		$users = $wpdb->get_results("SELECT user_login FROM $wpdb->users ORDER BY user_login");
?><select name="userselect[<?php echo $n; ?>]">
	<option value="#NONE#">- Select -</option>
	<?php
		foreach ($users as $user) {
			echo '<option value="'.$user->user_login.'">'.$user->user_login.'</option>';
		}
?>
	</select>
	<?php
	}

	//function to check the authorname and do the mapping
	function checkauthor($author) {
		global $wpdb;
		//mtnames is an array with the names in the mt import file
		$pass = 'changeme';
		if (!(in_array($author, $this->mtnames))) { //a new mt author name is found
			++ $this->j;
			$this->mtnames[$this->j] = $author; //add that new mt author name to an array
			$user_id = username_exists($this->newauthornames[$this->j]); //check if the new author name defined by the user is a pre-existing wp user
			if (!$user_id) { //banging my head against the desk now.
				if ($this->newauthornames[$this->j] == 'left_blank') { //check if the user does not want to change the authorname
					$user_id = wp_create_user($author, $pass);
					$this->newauthornames[$this->j] = $author; //now we have a name, in the place of left_blank.
				} else {
					$user_id = wp_create_user($this->newauthornames[$this->j], $pass);
				}
			} else {
				return $user_id; // return pre-existing wp username if it exists
			}
		} else {
			$key = array_search($author, $this->mtnames); //find the array key for $author in the $mtnames array
			$user_id = username_exists($this->newauthornames[$key]); //use that key to get the value of the author's name from $newauthornames
		}

		return $user_id;
	}

	function get_entries() {
		set_magic_quotes_runtime(0);

		$this->posts = array();
		$this->categories = array();
		$this->tags = array();
		$num = 0;
		$doing_entry = false;

		$fp = fopen($this->file, 'r');
		if ($fp) {
			while ( !feof($fp) ) {
				$importline = rtrim(fgets($fp));

				if ( false !== strpos($importline, '<wp:category>') ) {
					preg_match('|<wp:category>(.*?)</wp:category>|is', $importline, $category);
					$this->categories[] = $category[1];
					continue;
				}
				if ( false !== strpos($importline, '<wp:tag>') ) {
					preg_match('|<wp:tag>(.*?)</wp:tag>|is', $importline, $tag);
					$this->tags[] = $tag[1];
					continue;
				}
				if ( false !== strpos($importline, '<item>') ) {
					$this->posts[$num] = '';
					$doing_entry = true;
					continue;
				}
				if ( false !== strpos($importline, '</item>') ) {
					$num++;
					$doing_entry = false;
					continue;
				}
				if ( $doing_entry ) {
					$this->posts[$num] .= $importline . "\n";
				}
			}

			foreach ($this->posts as $post) {
				$post_ID = (int) $this->get_tag( $post, 'wp:post_id' );
				if ($post_ID) {
					$this->posts_processed[$post_ID][0] = &$post;
					$this->posts_processed[$post_ID][1] = 0;
				}
			}

			fclose($fp);
		}
	}

	function get_wp_authors() {
		$temp = array ();
		$i = -1;
		foreach ($this->posts as $post) {
			if ('' != trim($post)) {
				++ $i;
				$author = $this->get_tag( $post, 'dc:creator' );
				array_push($temp, "$author"); //store the extracted author names in a temporary array
			}
		}

		// We need to find unique values of author names, while preserving the order, so this function emulates the unique_value(); php function, without the sorting.
		$authors[0] = array_shift($temp);
		$y = count($temp) + 1;
		for ($x = 1; $x < $y; $x ++) {
			$next = array_shift($temp);
			if (!(in_array($next, $authors)))
				array_push($authors, "$next");
		}

		return $authors;
	}

	function get_authors_from_post() {
		$formnames = array ();
		$selectnames = array ();

		foreach ($_POST['user'] as $key => $line) {
			$newname = trim(stripslashes($line));
			if ($newname == '')
				$newname = 'left_blank'; //passing author names from step 1 to step 2 is accomplished by using POST. left_blank denotes an empty entry in the form.
			array_push($formnames, "$newname");
		} // $formnames is the array with the form entered names

		foreach ($_POST['userselect'] as $user => $key) {
			$selected = trim(stripslashes($key));
			array_push($selectnames, "$selected");
		}

		$count = count($formnames);
		for ($i = 0; $i < $count; $i ++) {
			if ($selectnames[$i] != '#NONE#') { //if no name was selected from the select menu, use the name entered in the form
				array_push($this->newauthornames, "$selectnames[$i]");
			} else {
				array_push($this->newauthornames, "$formnames[$i]");
			}
		}
	}

	function wp_authors_form() {
?>
<h2><?php _e('Assign Authors'); ?></h2>
<p><?php _e('To make it easier for you to edit and save the imported posts and drafts, you may want to change the name of the author of the posts. For example, you may want to import all the entries as <code>admin</code>s entries.'); ?></p>
<p><?php _e('If a new user is created by WordPress, the password will be set, by default, to "changeme". Quite suggestive, eh? ;)'); ?></p>
	<?php


		$authors = $this->get_wp_authors();
		echo '<ol id="authors">';
		echo '<form action="?import=wordpress&amp;step=2&amp;id=' . $this->id . '" method="post">';
		wp_nonce_field('import-wordpress');
		$j = -1;
		foreach ($authors as $author) {
			++ $j;
			echo '<li>'.__('Current author:').' <strong>'.$author.'</strong><br />'.sprintf(__('Create user %1$s or map to existing'), ' <input type="text" value="'.$author.'" name="'.'user[]'.'" maxlength="30"> <br />');
			$this->users_form($j);
			echo '</li>';
		}

		echo '<input type="submit" value="Submit">'.'<br />';
		echo '</form>';
		echo '</ol>';

	}

	function select_authors() {
		$file = wp_import_handle_upload();
		if ( isset($file['error']) ) {
			echo '<p>'.__('Sorry, there has been an error.').'</p>';
			echo '<p><strong>' . $file['error'] . '</strong></p>';
			return;
		}
		$this->file = $file['file'];
		$this->id = (int) $file['id'];

		$this->get_entries();
		$this->wp_authors_form();
	}

	function process_categories() {
		global $wpdb;

		$cat_names = (array) get_terms('category', 'fields=names');

		while ( $c = array_shift($this->categories) ) {
			$cat_name = trim($this->get_tag( $c, 'wp:cat_name' ));

			// If the category exists we leave it alone
			if ( in_array($cat_name, $cat_names) )
				continue;

			$category_nicename	= $this->get_tag( $c, 'wp:category_nicename' );
			$posts_private		= (int) $this->get_tag( $c, 'wp:posts_private' );
			$links_private		= (int) $this->get_tag( $c, 'wp:links_private' );

			$parent = $this->get_tag( $c, 'wp:category_parent' );

			if ( empty($parent) )
				$category_parent = '0';
			else
				$category_parent = category_exists($parent);

			$catarr = compact('category_nicename', 'category_parent', 'posts_private', 'links_private', 'posts_private', 'cat_name');

			$cat_ID = wp_insert_category($catarr);
		}
	}

	function process_tags() {
		global $wpdb;

		$tag_names = (array) get_terms('post_tag', 'fields=names');

		while ( $c = array_shift($this->tags) ) {
			$tag_name = trim($this->get_tag( $c, 'wp:tag_name' ));

			// If the category exists we leave it alone
			if ( in_array($tag_name, $tag_names) )
				continue;

			$slug = $this->get_tag( $c, 'wp:tag_slug' );
			$description = $this->get_tag( $c, 'wp:tag_description' );

			$tagarr = compact('slug', 'description');

			$tag_ID = wp_insert_term($tag_name, 'post_tag', $tagarr);
		}
	}

	function process_posts() {
		$i = -1;
		echo '<ol>';

		foreach ($this->posts as $post) {
			$result = $this->process_post($post);
			if ( is_wp_error( $result ) )
				return $result;
		}

		echo '</ol>';

		wp_import_cleanup($this->id);

		echo '<h3>'.sprintf(__('All done.').' <a href="%s">'.__('Have fun!').'</a>', get_option('home')).'</h3>';
	}

	function process_post($post) {
		global $wpdb;

		$post_ID = (int) $this->get_tag( $post, 'wp:post_id' );
  		if ( $post_ID && !empty($this->posts_processed[$post_ID][1]) ) // Processed already
			return 0;

		// There are only ever one of these
		$post_title     = $this->get_tag( $post, 'title' );
		$post_date      = $this->get_tag( $post, 'wp:post_date' );
		$post_date_gmt  = $this->get_tag( $post, 'wp:post_date_gmt' );
		$comment_status = $this->get_tag( $post, 'wp:comment_status' );
		$ping_status    = $this->get_tag( $post, 'wp:ping_status' );
		$post_status    = $this->get_tag( $post, 'wp:status' );
		$post_name      = $this->get_tag( $post, 'wp:post_name' );
		$post_parent    = $this->get_tag( $post, 'wp:post_parent' );
		$menu_order     = $this->get_tag( $post, 'wp:menu_order' );
		$post_type      = $this->get_tag( $post, 'wp:post_type' );
		$guid           = $this->get_tag( $post, 'guid' );
		$post_author    = $this->get_tag( $post, 'dc:creator' );

		$post_content = $this->get_tag( $post, 'content:encoded' );
		$post_content = preg_replace('|<(/?[A-Z]+)|e', "'<' . strtolower('$1')", $post_content);
		$post_content = str_replace('<br>', '<br />', $post_content);
		$post_content = str_replace('<hr>', '<hr />', $post_content);

		preg_match_all('|<category domain="tag">(.*?)</category>|is', $post, $tags);
		$tags = $tags[1];

		$tag_index = 0;
		foreach ($tags as $tag) {
			$tags[$tag_index] = $wpdb->escape($this->unhtmlentities(str_replace(array ('<![CDATA[', ']]>'), '', $tag)));
			$tag_index++;
		}

		preg_match_all('|<category>(.*?)</category>|is', $post, $categories);
		$categories = $categories[1];

		$cat_index = 0;
		foreach ($categories as $category) {
			$categories[$cat_index] = $wpdb->escape($this->unhtmlentities(str_replace(array ('<![CDATA[', ']]>'), '', $category)));
			$cat_index++;
		}

		if ($post_id = post_exists($post_title, '', $post_date)) {
			echo '<li>';
			printf(__('Post <i>%s</i> already exists.'), stripslashes($post_title));
		} else {

			// If it has parent, process parent first.
			$post_parent = (int) $post_parent;
			if ($parent = $this->posts_processed[$post_parent]) {
				if (!$parent[1]) { 
					$result = $this->process_post($parent[0]); // If not yet, process the parent first.
					if ( is_wp_error( $result ) )
						return $result;
				}
				$post_parent = $parent[1]; // New ID of the parent;
			}

			echo '<li>';
			printf(__('Importing post <i>%s</i>...'), stripslashes($post_title));

			$post_author = $this->checkauthor($post_author); //just so that if a post already exists, new users are not created by checkauthor

			$postdata = compact('post_author', 'post_date', 'post_date_gmt', 'post_content', 'post_title', 'post_excerpt', 'post_status', 'post_name', 'comment_status', 'ping_status', 'post_modified', 'post_modified_gmt', 'guid', 'post_parent', 'menu_order', 'post_type');
			$comment_post_ID = $post_id = wp_insert_post($postdata);
			if ( is_wp_error( $post_id ) )
				return $post_id;

			// Memorize old and new ID.
			if ( $post_id && $post_ID && $this->posts_processed[$post_ID] )
				$this->posts_processed[$post_ID][1] = $post_id; // New ID.

			// Add categories.
			if (count($categories) > 0) {
				$post_cats = array();
				foreach ($categories as $category) {
					$slug = sanitize_term_field('slug', $category, 0, 'category', 'db');
					$cat = get_term_by('slug', $slug, 'category');
					$cat_ID = 0;
					if ( ! empty($cat) )
						$cat_ID = $cat->term_id;
					if ($cat_ID == 0) {
						$category = $wpdb->escape($category);
						$cat_ID = wp_insert_category(array('cat_name' => $category));
					}
					$post_cats[] = $cat_ID;
				}
				wp_set_post_categories($post_id, $post_cats);
			}

			// Add tags.
			if (count($tags) > 0) {
				$post_tags = array();
				foreach ($tags as $tag) {
					$slug = sanitize_term_field('slug', $tag, 0, 'post_tag', 'db');
					$tag_obj = get_term_by('slug', $slug, 'post_tag');
					$tag_id = 0;
					if ( ! empty($tag_obj) )
						$tag_id = $tag_obj->term_id;
					if ( $tag_id == 0 ) {
						$tag = $wpdb->escape($tag);
						$tag_id = wp_insert_term($tag, 'post_tag');
						$tag_id = $tag_id['term_id'];
					}
					$post_tags[] = $tag_id;
				}
				wp_set_post_tags($post_id, $post_tags);
			}
		}

		// Now for comments
		preg_match_all('|<wp:comment>(.*?)</wp:comment>|is', $post, $comments);
		$comments = $comments[1];
		$num_comments = 0;
		if ( $comments) { foreach ($comments as $comment) {
			$comment_author       = $this->get_tag( $comment, 'wp:comment_author');
			$comment_author_email = $this->get_tag( $comment, 'wp:comment_author_email');
			$comment_author_IP    = $this->get_tag( $comment, 'wp:comment_author_IP');
			$comment_author_url   = $this->get_tag( $comment, 'wp:comment_author_url');
			$comment_date         = $this->get_tag( $comment, 'wp:comment_date');
			$comment_date_gmt     = $this->get_tag( $comment, 'wp:comment_date_gmt');
			$comment_content      = $this->get_tag( $comment, 'wp:comment_content');
			$comment_approved     = $this->get_tag( $comment, 'wp:comment_approved');
			$comment_type         = $this->get_tag( $comment, 'wp:comment_type');
			$comment_parent       = $this->get_tag( $comment, 'wp:comment_parent');

			if ( !comment_exists($comment_author, $comment_date) ) {
				$commentdata = compact('comment_post_ID', 'comment_author', 'comment_author_url', 'comment_author_email', 'comment_author_IP', 'comment_date', 'comment_date_gmt', 'comment_content', 'comment_approved', 'comment_type', 'comment_parent');
				wp_insert_comment($commentdata);
				$num_comments++;
			}
		} }

		if ( $num_comments )
			printf(' '.__('(%s comments)'), $num_comments);

		// Now for post meta
		preg_match_all('|<wp:postmeta>(.*?)</wp:postmeta>|is', $post, $postmeta);
		$postmeta = $postmeta[1];
		if ( $postmeta) { foreach ($postmeta as $p) {
			$key   = $this->get_tag( $p, 'wp:meta_key' );
			$value = $this->get_tag( $p, 'wp:meta_value' );
			$value = stripslashes($value); // add_post_meta() will escape.
			add_post_meta( $post_id, $key, $value );
		} }
	}

	function import() {
		$this->id = (int) $_GET['id'];

		$this->file = get_attached_file($this->id);
		$this->get_authors_from_post();
		$this->get_entries();
		$this->process_categories();
		$this->process_tags();
		$result = $this->process_posts();
		if ( is_wp_error( $result ) )
			return $result;
	}

	function dispatch() {
		if (empty ($_GET['step']))
			$step = 0;
		else
			$step = (int) $_GET['step'];

		$this->header();
		switch ($step) {
			case 0 :
				$this->greet();
				break;
			case 1 :
				check_admin_referer('import-upload');
				$this->select_authors();
				break;
			case 2:
				check_admin_referer('import-wordpress');
				$result = $this->import();
				if ( is_wp_error( $result ) )
					echo $result->get_error_message();
				break;
		}
		$this->footer();
	}

	function WP_Import() {
		// Nothing.
	}
}

$wp_import = new WP_Import();

register_importer('wordpress', 'WordPress', __('Import <strong>posts, comments, custom fields, pages, and categories</strong> from a WordPress export file'), array ($wp_import, 'dispatch'));

?>
