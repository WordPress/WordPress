<?php

class MT_Import {

	var $posts = array ();
	var $file;
	var $id;
	var $mtnames = array ();
	var $newauthornames = array ();
	var $j = -1;

	function header() {
		echo '<div class="wrap">';
		echo '<h2>'.__('Import Movable Type').'</h2>';
	}

	function footer() {
		echo '</div>';
	}

	function greet() {
		$this->header();
?>
<p><?php _e('Howdy! We&#8217;re about to begin the process to import all of your Movable Type entries into WordPress. To begin, select a file to upload and click Import.'); ?></p>
<?php wp_import_upload_form( add_query_arg('step', 1) ); ?>
	<p><?php _e('The importer is smart enough not to import duplicates, so you can run this multiple times without worry if&#8212;for whatever reason&#8212;it doesn\'t finish. If you get an <strong>out of memory</strong> error try splitting up the import file into pieces.'); ?> </p>
<?php
		$this->footer();
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
		$importdata = preg_replace("/\n--------\n/", "--MT-ENTRY--\n", $importdata);
		$this->posts = explode("--MT-ENTRY--", $importdata);
	}

	function get_mt_authors() {
		$temp = array ();
		$i = -1;
		foreach ($this->posts as $post) {
			if ('' != trim($post)) {
				++ $i;
				preg_match("|AUTHOR:(.*)|", $post, $thematch);
				$thematch = trim($thematch[1]);
				array_push($temp, "$thematch"); //store the extracted author names in a temporary array
			}
		}

		//we need to find unique values of author names, while preserving the order, so this function emulates the unique_value(); php function, without the sorting.
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

	function mt_authors_form() {
?>
<p><?php _e('To make it easier for you to edit and save the imported posts and drafts, you may want to change the name of the author of the posts. For example, you may want to import all the entries as <code>admin</code>s entries.'); ?></p>
<p><?php _e('Below, you can see the names of the authors of the MovableType posts in <i>italics</i>. For each of these names, you can either pick an author in your WordPress installation from the menu, or enter a name for the author in the textbox.'); ?></p>
<p><?php _e('If a new user is created by WordPress, the password will be set, by default, to "changeme". Quite suggestive, eh? ;)'); ?></p>
	<?php


		$authors = $this->get_mt_authors();
		echo '<ol id="authors">';
		echo '<form action="?import=mt&amp;step=2&amp;id=' . $this->id . '" method="post">';
		$j = -1;
		foreach ($authors as $author) {
			++ $j;
			echo '<li><i>'.$author.'</i><br />'.'<input type="text" value="'.$author.'" name="'.'user[]'.'" maxlength="30">';
			$this->users_form($j);
			echo '</li>';
		}

		echo '<input type="submit" value="Submit">'.'<br/>';
		echo '</form>';
		echo '</ol>';

		flush();
	}

	function select_authors() {
		$file = wp_import_handle_upload();
		if ( isset($file['error']) ) {
			echo $file['error'];
			return;
		}
		$this->file = $file['file'];
		$this->id = $file['id'];

		$this->get_entries();
		$this->mt_authors_form();
	}

	function process_posts() {
		global $wpdb;
		$i = -1;
		echo "<ol>";
		foreach ($this->posts as $post) {
			if ('' != trim($post)) {
				++ $i;
				unset ($post_categories);

				// Take the pings out first
				preg_match("|(-----\n\nPING:.*)|s", $post, $pings);
				$post = preg_replace("|(-----\n\nPING:.*)|s", '', $post);

				// Then take the comments out
				preg_match("|(-----\nCOMMENT:.*)|s", $post, $comments);
				$post = preg_replace("|(-----\nCOMMENT:.*)|s", '', $post);

				// We ignore the keywords
				$post = preg_replace("|(-----\nKEYWORDS:.*)|s", '', $post);

				// We want the excerpt
				preg_match("|-----\nEXCERPT:(.*)|s", $post, $excerpt);
				$excerpt = $wpdb->escape(trim($excerpt[1]));
				$post = preg_replace("|(-----\nEXCERPT:.*)|s", '', $post);

				// We're going to put extended body into main body with a more tag
				preg_match("|-----\nEXTENDED BODY:(.*)|s", $post, $extended);
				$extended = trim($extended[1]);
				if ('' != $extended)
					$extended = "\n<!--more-->\n$extended";
				$post = preg_replace("|(-----\nEXTENDED BODY:.*)|s", '', $post);

				// Now for the main body
				preg_match("|-----\nBODY:(.*)|s", $post, $body);
				$body = trim($body[1]);
				$post_content = $wpdb->escape($body.$extended);
				$post = preg_replace("|(-----\nBODY:.*)|s", '', $post);

				// Grab the metadata from what's left
				$metadata = explode("\n", $post);
				foreach ($metadata as $line) {
					preg_match("/^(.*?):(.*)/", $line, $token);
					$key = trim($token[1]);
					$value = trim($token[2]);
					// Now we decide what it is and what to do with it
					switch ($key) {
						case '' :
							break;
						case 'AUTHOR' :
							$post_author = $value;
							break;
						case 'TITLE' :
							$post_title = $wpdb->escape($value);
							break;
						case 'STATUS' :
							// "publish" and "draft" enumeration items match up; no change required
							$post_status = $value;
							if (empty ($post_status))
								$post_status = 'publish';
							break;
						case 'ALLOW COMMENTS' :
							$post_allow_comments = $value;
							if ($post_allow_comments == 1) {
								$comment_status = 'open';
							} else {
								$comment_status = 'closed';
							}
							break;
						case 'CONVERT BREAKS' :
							$post_convert_breaks = $value;
							break;
						case 'ALLOW PINGS' :
							$ping_status = trim($meta[2][0]);
							if ($ping_status == 1) {
								$ping_status = 'open';
							} else {
								$ping_status = 'closed';
							}
							break;
						case 'PRIMARY CATEGORY' :
							if (! empty ($value) )
								$post_categories[] = $wpdb->escape($value);
							break;
						case 'CATEGORY' :
							if (! empty ($value) )
								$post_categories[] = $wpdb->escape($value);
							break;
						case 'DATE' :
							$post_modified = strtotime($value);
							$post_modified = date('Y-m-d H:i:s', $post_modified);
							$post_modified_gmt = get_gmt_from_date("$post_modified");
							$post_date = $post_modified;
							$post_date_gmt = $post_modified_gmt;
							break;
						default :
							// echo "\n$key: $value";
							break;
					} // end switch
				} // End foreach

				// Let's check to see if it's in already
				if ($post_id = post_exists($post_title, '', $post_date)) {
					echo '<li>';
					printf(__('Post <i>%s</i> already exists.'), stripslashes($post_title));
				} else {
					echo '<li>';
					printf(__('Importing post <i>%s</i>...'), stripslashes($post_title));

					$post_author = $this->checkauthor($post_author); //just so that if a post already exists, new users are not created by checkauthor

					$postdata = compact('post_author', 'post_date', 'post_date_gmt', 'post_content', 'post_title', 'post_excerpt', 'post_status', 'comment_status', 'ping_status', 'post_modified', 'post_modified_gmt');
					$post_id = wp_insert_post($postdata);
					// Add categories.
					if (0 != count($post_categories)) {
						wp_create_categories($post_categories, $post_id);
					}
				}

				$comment_post_ID = $post_id;
				$comment_approved = 1;

				// Now for comments
				$comments = explode("-----\nCOMMENT:", $comments[0]);
				$num_comments = 0;
				foreach ($comments as $comment) {
					if ('' != trim($comment)) {
						// Author
						preg_match("|AUTHOR:(.*)|", $comment, $comment_author);
						$comment_author = $wpdb->escape(trim($comment_author[1]));
						$comment = preg_replace('|(\n?AUTHOR:.*)|', '', $comment);
						preg_match("|EMAIL:(.*)|", $comment, $comment_author_email);
						$comment_author_email = $wpdb->escape(trim($comment_author_email[1]));
						$comment = preg_replace('|(\n?EMAIL:.*)|', '', $comment);

						preg_match("|IP:(.*)|", $comment, $comment_author_IP);
						$comment_author_IP = trim($comment_author_IP[1]);
						$comment = preg_replace('|(\n?IP:.*)|', '', $comment);

						preg_match("|URL:(.*)|", $comment, $comment_author_url);
						$comment_author_url = $wpdb->escape(trim($comment_author_url[1]));
						$comment = preg_replace('|(\n?URL:.*)|', '', $comment);

						preg_match("|DATE:(.*)|", $comment, $comment_date);
						$comment_date = trim($comment_date[1]);
						$comment_date = date('Y-m-d H:i:s', strtotime($comment_date));
						$comment = preg_replace('|(\n?DATE:.*)|', '', $comment);

						$comment_content = $wpdb->escape(trim($comment));
						$comment_content = str_replace('-----', '', $comment_content);
						// Check if it's already there
						if (!comment_exists($comment_author, $comment_date)) {
							$commentdata = compact('comment_post_ID', 'comment_author', 'comment_author_url', 'comment_author_email', 'comment_author_IP', 'comment_date', 'comment_content', 'comment_approved');
							$commentdata = wp_filter_comment($commentdata);
							wp_insert_comment($commentdata);
							$num_comments++;
						}
					}
				}
				if ( $num_comments )
					printf(__('(%s comments)'), $num_comments);

				// Finally the pings
				// fix the double newline on the first one
				$pings[0] = str_replace("-----\n\n", "-----\n", $pings[0]);
				$pings = explode("-----\nPING:", $pings[0]);
				$num_pings = 0;
				foreach ($pings as $ping) {
					if ('' != trim($ping)) {
						// 'Author'
						preg_match("|BLOG NAME:(.*)|", $ping, $comment_author);
						$comment_author = $wpdb->escape(trim($comment_author[1]));
						$ping = preg_replace('|(\n?BLOG NAME:.*)|', '', $ping);

						preg_match("|IP:(.*)|", $ping, $comment_author_IP);
						$comment_author_IP = trim($comment_author_IP[1]);
						$ping = preg_replace('|(\n?IP:.*)|', '', $ping);

						preg_match("|URL:(.*)|", $ping, $comment_author_url);
						$comment_author_url = $wpdb->escape(trim($comment_author_url[1]));
						$ping = preg_replace('|(\n?URL:.*)|', '', $ping);

						preg_match("|DATE:(.*)|", $ping, $comment_date);
						$comment_date = trim($comment_date[1]);
						$comment_date = date('Y-m-d H:i:s', strtotime($comment_date));
						$ping = preg_replace('|(\n?DATE:.*)|', '', $ping);

						preg_match("|TITLE:(.*)|", $ping, $ping_title);
						$ping_title = $wpdb->escape(trim($ping_title[1]));
						$ping = preg_replace('|(\n?TITLE:.*)|', '', $ping);

						$comment_content = $wpdb->escape(trim($ping));
						$comment_content = str_replace('-----', '', $comment_content);

						$comment_content = "<strong>$ping_title</strong>\n\n$comment_content";

						$comment_type = 'trackback';

						// Check if it's already there
						if (!comment_exists($comment_author, $comment_date)) {
							$commentdata = compact('comment_post_ID', 'comment_author', 'comment_author_url', 'comment_author_email', 'comment_author_IP', 'comment_date', 'comment_content', 'comment_type', 'comment_approved');
							$commentdata = wp_filter_comment($commentdata);
							wp_insert_comment($commentdata);
							$num_pings++;
						}
					}
				}
				if ( $num_pings )
					printf(__('(%s pings)'), $num_pings);
				
				echo "</li>";
			}
			flush();
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
	}

	function MT_Import() {
		// Nothing.	
	}
}

$mt_import = new MT_Import();

register_importer('mt', 'Movable Type', __('Import posts and comments from your Movable Type blog'), array ($mt_import, 'dispatch'));
?>
