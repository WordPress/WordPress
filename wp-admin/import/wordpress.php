<?php

class WP_Import {

	var $posts = array ();
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
		echo '<p>'.__('Howdy! Upload your WordPress eXtended RSS (WXR) file and we&#8217;ll import the posts and comments into this blog.').'</p>';
		wp_import_upload_form("admin.php?import=wordpress&amp;step=1");
	}

	function get_tag( $string, $tag ) {
		preg_match("|<$tag.*?>(.*?)</$tag>|is", $string, $return);
		$return = addslashes( trim( $return[1] ) );
		return $return;
	}

	function users_form($n) {
		global $wpdb, $testing;
		$users = $wpdb->get_results("SELECT * FROM $wpdb->users ORDER BY ID");
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
				if ($newauthornames[$this->j] == 'left_blank') { //check if the user does not want to change the authorname
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
		$importdata = file($this->file); // Read the file into an array
		$importdata = implode('', $importdata); // squish it
		$importdata = preg_replace("/(\r\n|\n|\r)/", "\n", $importdata);
		preg_match_all('|<item>(.*?)</item>|is', $importdata, $this->posts);
		$this->posts = $this->posts[1];
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
		$j = -1;
		foreach ($authors as $author) {
			++ $j;
			echo '<li>Current author: <strong>'.$author.'</strong><br />'.'Create user <input type="text" value="'.$author.'" name="'.'user[]'.'" maxlength="30"> <br /> or map to existing ';
			$this->users_form($j);
			echo '</li>';
		}

		echo '<input type="submit" value="Submit">'.'<br/>';
		echo '</form>';
		echo '</ol>';

	}

	function select_authors() {
		$file = wp_import_handle_upload();
		if ( isset($file['error']) ) {
			$this->header();
			echo '<p>Sorry, there has been an error.</p>';
			echo '<p><strong>' . $file['error'] . '</strong></p>';
			$this->footer();
			return;
		}
		$this->file = $file['file'];
		$this->id = $file['id'];

		$this->get_entries();
		$this->wp_authors_form();
	}

	function process_posts() {
		global $wpdb;
		$i = -1;
		echo '<ol>';
		foreach ($this->posts as $post) {

			// There are only ever one of these
			$post_title     = $this->get_tag( $post, 'title' );
			$post_date      = $this->get_tag( $post, 'wp:post_date' );
			$post_date_gmt  = $this->get_tag( $post, 'wp:post_date_gmt' );
			$comment_status = $this->get_tag( $post, 'wp:comment_status' );
			$ping_status    = $this->get_tag( $post, 'wp:ping_status' );
			$post_status    = $this->get_tag( $post, 'wp:status' );
			$post_parent    = $this->get_tag( $post, 'wp:post_parent' );
			$post_type      = $this->get_tag( $post, 'wp:post_type' );
			$guid           = $this->get_tag( $post, 'guid' );
			$post_author    = $this->get_tag( $post, 'dc:creator' );

			$post_content = $this->get_tag( $post, 'content:encoded' );
			$post_content = str_replace(array ('<![CDATA[', ']]>'), '', $post_content);
			$post_content = preg_replace('|<(/?[A-Z]+)|e', "'<' . strtolower('$1')", $post_content);
			$post_content = str_replace('<br>', '<br />', $post_content);
			$post_content = str_replace('<hr>', '<hr />', $post_content);

			preg_match_all('|<category>(.*?)</category>|is', $post, $categories);
			$categories = $categories[1];

			$cat_index = 0;
			foreach ($categories as $category) {
				$categories[$cat_index] = $wpdb->escape($this->unhtmlentities($category));
				$cat_index++;
			}

			if ($post_id = post_exists($post_title, '', $post_date)) {
				echo '<li>';
				printf(__('Post <i>%s</i> already exists.'), stripslashes($post_title));
			} else {
				echo '<li>';
				printf(__('Importing post <i>%s</i>...'), stripslashes($post_title));

				$post_author = $this->checkauthor($post_author); //just so that if a post already exists, new users are not created by checkauthor

				$postdata = compact('post_author', 'post_date', 'post_date_gmt', 'post_content', 'post_title', 'post_excerpt', 'post_status', 'comment_status', 'ping_status', 'post_modified', 'post_modified_gmt', 'guid', 'post_parent', 'post_type');
				$comment_post_ID = $post_id = wp_insert_post($postdata);
				// Add categories.
				if (0 != count($categories)) {
					wp_create_categories($categories, $post_id);
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
					printf(__(' (%s comments)'), $num_comments);

				// Now for post meta
				preg_match_all('|<wp:postmeta>(.*?)</wp:postmeta>|is', $post, $postmeta);
				$postmeta = $postmeta[1];
				if ( $postmeta) { foreach ($postmeta as $p) {
					$key   = $this->get_tag( $p, 'wp:meta_key' );
					$value = $this->get_tag( $p, 'wp:meta_value' );
					add_post_meta( $post_id, $key, $value );
				} }

			$index++;
		}

		echo '</ol>';

		wp_import_cleanup($this->id);

		echo '<h3>'.sprintf(__('All done. <a href="%s">Have fun!</a>'), get_option('home')).'</h3>';
	}

	function import() {
		$this->id = (int) $_GET['id'];
		
		$this->file = get_attached_file($this->id);
		$this->get_authors_from_post();
		$this->get_entries();
		$this->process_posts();
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
				$this->select_authors();
				break;
			case 2:
				$this->import();
				break;
		}
		$this->footer();
	}

	function WP_Import() {
		// Nothing.
	}
}

$wp_import = new WP_Import();

register_importer('wordpress', 'WordPress', __('Import posts from a WordPress export file'), array ($wp_import, 'dispatch'));

?>
