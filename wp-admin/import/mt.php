<?php


// enter the relative path of the import.txt file containing the mt entries. If the file is called import.txt and it is /wp-admin, then this line
//should be define('MTEXPORT', 'import.txt');
define('MTEXPORT', 'import.txt');

class MT_Import {

	var $posts = array ();
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
<p>Howdy! We&#8217;re about to begin the process to import all of your Movable Type entries into WordPress. Before we get started, you need to edit this file (<code>import/mt.php</code>) and change one line so we know where to find your MT export file. To make this easy put the import file into the <code>wp-admin/import</code> directory. Look for the line that says:</p>
<p><code>define('MTEXPORT', '');</code></p>
<p>and change it to</p>
<p><code>define('MTEXPORT', 'import.txt');</code></p>
<p>You have to do this manually for security reasons.</p>
<p>If you've done that and you&#8217;re all ready, <a href="<?php echo add_query_arg('step', 1)  ?>">let's go</a>! Remember that the import process may take a minute or so if you have a large number of entries and comments. Think of all the rebuilding time you'll be saving once it's done. :)</p>
<p>The importer is smart enough not to import duplicates, so you can run this multiple times without worry if&#8212;for whatever reason&#8212;it doesn't finish. If you get an <strong>out of memory</strong> error try splitting up the import file into pieces. </p>
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
		$md5pass = md5(changeme);
		if (!(in_array($author, $this->mtnames))) { //a new mt author name is found
			++ $this->j;
			$this->mtnames[$this->j] = $author; //add that new mt author name to an array 
			$user_id = $wpdb->get_var("SELECT ID FROM $wpdb->users WHERE user_login = '$this->newauthornames[$j]'"); //check if the new author name defined by the user is a pre-existing wp user
			if (!$user_id) { //banging my head against the desk now. 
				if ($newauthornames[$this->j] == 'left_blank') { //check if the user does not want to change the authorname
					$wpdb->query("INSERT INTO $wpdb->users (user_level, user_login, user_pass, user_nickname) VALUES ('1', '$author', '$md5pass', '$author')"); // if user does not want to change, insert the authorname $author
					$user_id = $wpdb->get_var("SELECT ID FROM $wpdb->users WHERE user_login = '$author'");
					$this->newauthornames[$this->j] = $author; //now we have a name, in the place of left_blank.
				} else {
					$wpdb->query("INSERT INTO $wpdb->users (user_level, user_login, user_pass, user_nickname) VALUES ('1', '{$this->newauthornames[$this->j]}', '$md5pass', '{$this->newauthornames[$this->j]}')"); //if not left_blank, insert the user specified name
					$user_id = $wpdb->get_var("SELECT ID FROM $wpdb->users WHERE user_login = '{$this->newauthornames[$this->j]}'");
				}
			} else {
				return $user_id; // return pre-existing wp username if it exists
			}
		} else {
			$key = array_search($author, $this->mtnames); //find the array key for $author in the $mtnames array
			$user_id = $wpdb->get_var("SELECT ID FROM $wpdb->users WHERE user_login = '{$this->newauthornames[$key]}'"); //use that key to get the value of the author's name from $newauthornames
		}

		return $user_id;
	}

	function get_entries() {
		set_magic_quotes_runtime(0);
		$importdata = file(MTEXPORT); // Read the file into an array
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
		echo '<form action="?import=mt&amp;step=2" method="post">';
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
		if ('' != MTEXPORT && !file_exists(MTEXPORT))
			die("The file you specified does not seem to exist. Please check the path you've given.");
		if ('' == MTEXPORT)
			die("You must edit the MTEXPORT line as described on the <a href='import-mt.php'>previous page</a> to continue.");

		$this->get_entries();
		$this->mt_authors_form();
	}

	function process_posts() {
		$i = -1;
		echo "<ol>";
		foreach ($posts as $post) {
			if ('' != trim($post)) {
				++ $i;
				unset ($post_categories);
				echo "<li>Processing post... ";

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
							echo '<i>'.stripslashes($post_title).'</i>... ';
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
							$post_allow_pings = trim($meta[2][0]);
							if ($post_allow_pings == 1) {
								$post_allow_pings = 'open';
							} else {
								$post_allow_pings = 'closed';
							}
							break;
						case 'PRIMARY CATEGORY' :
							$post_categories[] = $wpdb->escape($value);
							break;
						case 'CATEGORY' :
							$post_categories[] = $wpdb->escape($value);
							break;
						case 'DATE' :
							$post_modified = strtotime($value);
							$post_modified = date('Y-m-d H:i:s', $post_modified);
							$post_modified_gmt = get_gmt_from_date("$post_modified");
							break;
						default :
							// echo "\n$key: $value";
							break;
					} // end switch
				} // End foreach

				// Let's check to see if it's in already
				if (posts_exists($post_title, '', $post_date)) {
					echo "Post already imported.";
				} else {
					$post_author = checkauthor($post_author); //just so that if a post already exists, new users are not created by checkauthor

					$postdata = compact('post_author', 'post_date', 'post_date_gmt', 'post_content', 'post_title', 'post_excerpt', 'post_status', 'comment_status', 'ping_status', 'post_modified', 'post_modified_gmt');
					$post_id = wp_insert_post($postdata);

					// Add categories.
					if (0 != count($post_categories)) {
						wp_create_categories($post_categories);
					}
					echo " Post imported successfully...";
				}

				// Now for comments
				$comments = explode("-----\nCOMMENT:", $comments[0]);
				foreach ($comments as $comment) {
					if ('' != trim($comment)) {
						// Author
						preg_match("|AUTHOR:(.*)|", $comment, $comment_author);
						$comment_author = $wpdb->escape(trim($comment_author[1]));
						$comment = preg_replace('|(\n?AUTHOR:.*)|', '', $comment);
						preg_match("|EMAIL:(.*)|", $comment, $comment_email);
						$comment_email = $wpdb->escape(trim($comment_email[1]));
						$comment = preg_replace('|(\n?EMAIL:.*)|', '', $comment);

						preg_match("|IP:(.*)|", $comment, $comment_ip);
						$comment_ip = trim($comment_ip[1]);
						$comment = preg_replace('|(\n?IP:.*)|', '', $comment);

						preg_match("|URL:(.*)|", $comment, $comment_url);
						$comment_url = $wpdb->escape(trim($comment_url[1]));
						$comment = preg_replace('|(\n?URL:.*)|', '', $comment);

						preg_match("|DATE:(.*)|", $comment, $comment_date);
						$comment_date = trim($comment_date[1]);
						$comment_date = date('Y-m-d H:i:s', strtotime($comment_date));
						$comment = preg_replace('|(\n?DATE:.*)|', '', $comment);

						$comment_content = $wpdb->escape(trim($comment));
						$comment_content = str_replace('-----', '', $comment_content);
						// Check if it's already there
						if (!$wpdb->get_row("SELECT * FROM $wpdb->comments WHERE comment_date = '$comment_date' AND comment_content = '$comment_content'")) {
							$wpdb->query("INSERT INTO $wpdb->comments (comment_post_ID, comment_author, comment_author_email, comment_author_url, comment_author_IP, comment_date, comment_content, comment_approved)
																								VALUES
																								($post_id, '$comment_author', '$comment_email', '$comment_url', '$comment_ip', '$comment_date', '$comment_content', '1')");
							echo "Comment added.";
						}
					}
				}

				// Finally the pings
				// fix the double newline on the first one
				$pings[0] = str_replace("-----\n\n", "-----\n", $pings[0]);
				$pings = explode("-----\nPING:", $pings[0]);
				foreach ($pings as $ping) {
					if ('' != trim($ping)) {
						// 'Author'
						preg_match("|BLOG NAME:(.*)|", $ping, $comment_author);
						$comment_author = $wpdb->escape(trim($comment_author[1]));
						$ping = preg_replace('|(\n?BLOG NAME:.*)|', '', $ping);

						$comment_email = '';

						preg_match("|IP:(.*)|", $ping, $comment_ip);
						$comment_ip = trim($comment_ip[1]);
						$ping = preg_replace('|(\n?IP:.*)|', '', $ping);

						preg_match("|URL:(.*)|", $ping, $comment_url);
						$comment_url = $wpdb->escape(trim($comment_url[1]));
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

						// Check if it's already there
						if (!$wpdb->get_row("SELECT * FROM $wpdb->comments WHERE comment_date = '$comment_date' AND comment_content = '$comment_content'")) {
							$wpdb->query("INSERT INTO $wpdb->comments (comment_post_ID, comment_author, comment_author_email, comment_author_url, comment_author_IP, comment_date, comment_content, comment_approved, comment_type)
																												VALUES
																												($post_id, '$comment_author', '$comment_email', '$comment_url', '$comment_ip', '$comment_date', '$comment_content', '1', 'trackback')");
							echo " Comment added.";
						}

					}
				}
				echo "</li>";
			}
			flush();
		}

		upgrade_all();
		echo '</ol>';
		echo '<h3>'.sprintf(__('All done. <a href="%s">Have fun!</a>'), get_option('home')).'</h3>';
	}

	function import() {
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
		}
	}

	function MT_Import() {
		// Nothing.	
	}
}

$mt_import = new MT_Import();

register_importer('mt', 'Movable Type', 'Import posts and comments from your Movable Type blog', array ($mt_import, 'dispatch'));
?>