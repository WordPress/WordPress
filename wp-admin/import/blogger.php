<?php

class Blogger_Import {

	var $lump_authors = false;
	var $import = array();

	// Shows the welcome screen and the magic iframe.
	function greet() {
		$title = __('Import Blogger');
		$welcome = __('Howdy! This importer allows you to import posts and comments from your Blogger account into your WordPress blog.');
		$noiframes = __('This feature requires iframe support.');
		$warning = __('This will delete everything saved by the Blogger importer except your posts and comments. Are you sure you want to do this?');
		$reset = __('Reset this importer');
		echo "<div class='wrap'><h2>$title</h2><p>$welcome</p><iframe src='admin.php?import=blogger&amp;noheader=true' height='350px' width = '99%'>$noiframes</iframe><p><a href='admin.php?import=blogger&amp;restart=true&amp;noheader=true' onclick='return confirm(\"$warning\")'>$reset</a></p></div>\n";
	}

	// Deletes saved data and redirect.
	function restart() {
		delete_option('import-blogger');
		header("Location: admin.php?import=blogger");
		die();
	}

	// Generates a string that will make the page reload in a specified interval.
	function refresher($msec) {
		if ( $msec )
			return "<html><head><script type='text/javascript'>window.onload=setTimeout('window.location.reload()', $msec);</script>\n</head>\n<body>\n";
		else
			return "<html><head><script type='text/javascript'>window.onload=window.location.reload();</script>\n</head>\n<body>\n";
	}

	// Returns associative array of code, header, cookies, body. Based on code from php.net.
	function parse_response($this_response) {
		// Split response into header and body sections
		list($response_headers, $response_body) = explode("\r\n\r\n", $this_response, 2);
		$response_header_lines = explode("\r\n", $response_headers);

		// First line of headers is the HTTP response code
		$http_response_line = array_shift($response_header_lines);
		if(preg_match('@^HTTP/[0-9]\.[0-9] ([0-9]{3})@',$http_response_line, $matches)) { $response_code = $matches[1]; }

		// put the rest of the headers in an array
		$response_header_array = array();
		foreach($response_header_lines as $header_line) {
			list($header,$value) = explode(': ', $header_line, 2);
			$response_header_array[$header] .= $value."\n";
		}

		$cookie_array = array();
		$cookies = explode("\n", $response_header_array["Set-Cookie"]);
		foreach($cookies as $this_cookie) { array_push($cookie_array, "Cookie: ".$this_cookie); }

		return array("code" => $response_code, "header" => $response_header_array, "cookies" => $cookie_array, "body" => $response_body);
	}

	// Prints a form for the user to enter Blogger creds.
	function login_form($text='') {
		echo '<h1>' . __('Log in to Blogger') . "</h1>\n$text\n";
		echo '<form method="post" action="admin.php?import=blogger&amp;noheader=true&amp;step=0"><table><tr><td>' . __('Username') . ':</td><td><input type="text" name="user" /></td></tr><tr><td>' . __('Password') . ':</td><td><input type="password" name="pass" /></td><td><input type="submit" value="' . __('Start') . '" /></td></tr></table></form>';
		die;
	}

	// Sends creds to Blogger, returns the session cookies an array of headers.
	function login_blogger($user, $pass) {
		$_url = 'http://www.blogger.com/login.do';
		$params = "username=$user&password=$pass";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_POST,1);
		curl_setopt($ch, CURLOPT_POSTFIELDS,$params);
		curl_setopt($ch, CURLOPT_URL,$_url);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Blogger Exporter');
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
		curl_setopt($ch, CURLOPT_HEADER,1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		$response = curl_exec ($ch);

		$response = $this->parse_response($response);

		sleep(1);

		return $response['cookies'];
	}

	// Requests page from Blogger, returns the response array.
	function get_blogger($url, $header = '', $user=false, $pass=false) {
		$ch = curl_init();
		if ($user && $pass) curl_setopt($ch, CURLOPT_USERPWD,"{$user}:{$pass}");
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Blogger Exporter');
		curl_setopt($ch, CURLOPT_HEADER,1);
		if (is_array($header)) curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		$response = curl_exec ($ch);

		$response = $this->parse_response($response);
		$response['url'] = $url;

		if (curl_errno($ch)) {
			print curl_error($ch);
		} else {
			curl_close($ch);
		}

		return $response;
	}

	// Posts data to Blogger, returns response array.
	function post_blogger($url, $header = false, $paramary = false, $parse=true) {
		$params = '';
		if ( is_array($paramary) ) {
			foreach($paramary as $key=>$value)
				if($key && $value != '')
					$params.=$key."=".urlencode(stripslashes($value))."&";
		}
		if ($user && $pass) $params .= "username=$user&password=$pass";
		$params = trim($params,'&');
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_POST,1);
		curl_setopt($ch, CURLOPT_POSTFIELDS,$params);
		if ($user && $pass) curl_setopt($ch, CURLOPT_USERPWD,"{$user}:{$pass}");
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Blogger Exporter');
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_HEADER,$parse);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		if ($header) curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		$response = curl_exec ($ch);
	
		if ($parse) {
			$response = $this->parse_response($response);
			$response['url'] = $url;
			return $response;
		}
	
		return $response;
	}

	// Prints the list of blogs for import.
	function show_blogs() {
		global $import;
		echo '<h1>' . __('Selecting a Blog') . "</h1>\n<ul>";
		foreach ( $this->import['blogs'] as $blog ) {
			if (9 == $blog['nextstep']) $status = "100%";
			elseif (8 == $blog['nextstep']) $status = "90%";
			elseif (7 == $blog['nextstep']) $status = "82.5%";
			elseif (6 == $blog['nextstep']) $status = "75%";
			elseif (5 == $blog['nextstep']) $status = "57%";
			elseif (4 == $blog['nextstep']) $status = "28%";
			elseif (3 == $blog['nextstep']) $status = "14%";
			else $status = "0%";
			echo "\t<li><a href='admin.php?import=blogger&amp;noheader=true&amp;blog={$blog['id']}'>{$blog['title']}</a> $status</li>\n";
		}
		die("</ul>\n");
	}

	// Publishes.
	function publish_blogger($i, $text) {
		$head = $this->refresher(2000) . "<h1>$text</h1>\n";
		if ( ! strstr($this->import['blogs'][$_GET['blog']]['publish'][$i], 'http') ) {
			// First call. Start the publish process with a fresh set of cookies.
			$this->import['cookies'] = $this->login_blogger($this->import['user'], $this->import['pass']);
			update_option('import-blogger', $this->import);
			$paramary = array('blogID' => $_GET['blog'], 'all' => '1', 'republishAll' => 'Republish Entire Blog', 'publish' => '1', 'redirectUrl' => "/publish.do?blogID={$_GET['blog']}&inprogress=true");

			$response = $this->post_blogger("http://www.blogger.com/publish.do?blogID={$_GET['blog']}", $this->import['cookies'], $paramary);
			if ( $response['code'] == '302' ) {
				$url = str_replace('publish.g', 'publish-body.g', $response['header']['Location']);
				$this->import['blogs'][$_GET['blog']]['publish'][$i] = $url;
				update_option('import-blogger', $this->import);
				$response = $this->get_blogger($url, $this->import['cookies']);
				preg_match('#<p class="progressIndicator">.*</p>#U', $response['body'], $matches);
				$progress = $matches[0];
				die($head . $progress);
			} else {
				$this->import['blogs'][$_GET['blog']]['publish'][$i] = false;
				update_option('import-blogger', $this->import);
				die($head);
			}
		} else {
			// Subsequent call. Keep checking status until Blogger reports publish complete.
			$url = $this->import['blogs'][$_GET['blog']]['publish'][$i];
			$response = $this->get_blogger($url, $this->import['cookies']);
			if ( preg_match('#<p class="progressIndicator">.*</p>#U', $response['body'], $matches) ) {
				$progress = $matches[0];
				if ( strstr($progress, '100%') ) {
					$this->set_next_step($i);
					$progress .= '<p>'.__('Moving on...').'</p>';
				}
				die($head . $progress);
			} else {
				$this->import['blogs'][$_GET['blog']]['publish'][$i] = false;
				update_option('import-blogger', $this->import);
				die("$head<p>" . __('Trying again...') . '</p>');
			}
		}
	}

	// Sets next step, saves options
	function set_next_step($step) {
		$this->import['blogs'][$_GET['blog']]['nextstep'] = $step;
		update_option('import-blogger', $this->import);
	}
	
	// Redirects to next step
	function do_next_step() {
		header("Location: admin.php?import=blogger&noheader=true&blog={$_GET['blog']}");
		die();
	}

	// Step 0: Do Blogger login, get blogid/title pairs.
	function do_login() {
		if ( ( ! $this->import['user'] && ! is_array($this->import['cookies']) ) ) {
			// The user must provide a Blogger username and password.
			if ( ! ( $_POST['user'] && $_POST['pass'] ) ) {
				$this->login_form(__('The script will log into your Blogger account, change some settings so it can read your blog, and restore the original settings when it\'s done. Here\'s what you do:</p><ol><li>Back up your Blogger template.</li><li>Back up any other Blogger settings you might need later.</li><li>Log out of Blogger</li><li>Log in <em>here</em> with your Blogger username and password.</li><li>On the next screen, click one of your Blogger blogs.</li><li>Do not close this window or navigate away until the process is complete.</li></ol>'));
			}
		
			// Try logging in. If we get an array of cookies back, we at least connected.		
			$this->import['cookies'] = $this->login_blogger($_POST['user'], $_POST['pass']);
			if ( !is_array( $this->import['cookies'] ) ) {
				$this->login_form("Login failed. Please enter your credentials again.");
			}
			
			// Save the password so we can log the browser in when it's time to publish.
			$this->import['pass'] = $_POST['pass'];
			$this->import['user'] = $_POST['user'];

			// Get the Blogger welcome page and scrape the blog numbers and names from it
			$response = $this->get_blogger('http://www.blogger.com/home', $this->import['cookies']);
			if (! stristr($response['body'], 'signed in as') ) $this->login_form("Login failed. Please re-enter your username and password.");
			$blogsary = array();
			preg_match_all('#posts\.g\?blogID=(\d+)">([^<]+)</a>#U', $response['body'], $blogsary);
			if ( ! count( $blogsary[1] < 1 ) )
				die(__('No blogs found for this user.'));
			$this->import['blogs'] = array();
			$template = '<MainPage><br /><br /><br /><p>'.__('Are you looking for %title%? It is temporarily out of service. Please try again in a few minutes. Meanwhile, discover <a href="http://wordpress.org/">a better blogging tool</a>.').'</p><BloggerArchives><a class="archive" href="<$BlogArchiveURL$>"><$BlogArchiveName$></a><br /></BloggerArchives></MainPage><ArchivePage><Blogger><wordpresspost><$BlogItemDateTime$>|W|P|<$BlogItemAuthorNickname$>|W|P|<$BlogItemBody$>|W|P|<$BlogItemNumber$>|W|P|<$BlogItemTitle$>|W|P|<$BlogItemAuthorEmail$><BlogItemCommentsEnabled><BlogItemComments><wordpresscomment><$BlogCommentDateTime$>|W|P|<$BlogCommentAuthor$>|W|P|<$BlogCommentBody$></BlogItemComments></BlogItemCommentsEnabled></Blogger></ArchivePage>';
			foreach ( $blogsary[1] as $key => $id ) {
				// Define the required Blogger options.
				$blog_opts = array(
					'blog-options-basic' => false,
					'blog-options-archiving' => array('archiveFrequency' => 'm'),
					'blog-publishing' => array('publishMode'=>'0', 'blogID' => "$id", 'subdomain' => mt_rand().mt_rand(), 'pingWeblogs' => 'false'),
					'blog-formatting' => array('timeStampFormat' => '0', 'encoding'=>'UTF-8', 'convertLineBreaks'=>'false', 'floatAlignment'=>'false'),
					'blog-comments' => array('commentsTimeStampFormat' => '0'),
					'template-edit' => array( 'templateText' =>  str_replace('%title%', trim($blogsary[2][$key]), $template) )
				);

				// Build the blog options array template
				foreach ($blog_opts as $blog_opt => $modify)
					$new_opts["$blog_opt"] = array('backup'=>false, 'modify' => $modify, 'error'=>false);

				$this->import['blogs']["$id"] = array(
					'id' => $id,
					'title' => trim($blogsary[2][$key]),
					'options' => $new_opts,
					'url' => false,
					'publish_cookies' => false,
					'published' => false,
					'archives' => false,
					'lump_authors' => false,
					'newusers' => array(),
					'nextstep' => 2
				);
			}
			update_option('import-blogger', $this->import);
			header("Location: admin.php?import=blogger&noheader=true&step=1");
		}
		die();
	}

	// Step 1: Select one of the blogs belonging to the user logged in.
	function select_blog() {
		if ( is_array($this->import['blogs']) ) {
			$this->show_blogs();
			die();
		} else {
			$this->restart();
		}
	}

	// Step 2: Backup the Blogger options pages, updating some of them.
	function backup_settings() {
		$output.= "<h1>Backing up Blogger options</h1>\n";
		$form = false;
		foreach ($this->import['blogs'][$_GET['blog']]['options'] as $blog_opt => $optary) {
			if ( $blog_opt == $_GET['form'] ) {
				// Save the posted form data
				$this->import['blogs'][$_GET['blog']]['options']["$blog_opt"]['backup'] = $_POST;
				update_option('import-blogger',$this->import);

				// Post the modified form data to Blogger
				if ( $optary['modify'] ) {
					$posturl = "http://www.blogger.com/{$blog_opt}.do";
					$headers = array_merge($this->import['blogs'][$_GET['blog']]['options']["$blog_opt"]['cookies'], $this->import['cookies']);
					if ( 'blog-publishing' == $blog_opt ) {
						if ( $_POST['publishMode'] > 0 ) {
							$response = $this->get_blogger("http://www.blogger.com/blog-publishing.g?blogID={$_GET['blog']}&publishMode=0", $headers);
							if ( $response['code'] >= 400 )
								die('<h2>'.__('Failed attempt to change publish mode from FTP to BlogSpot.').'</h2><pre>' . addslashes(print_r($headers, 1)) . addslashes(print_r($response, 1)) . '</pre>');
							$this->import['blogs'][$_GET['blog']]['url'] = 'http://' . $optary['modify']['subdomain'] . '.blogspot.com/';
							sleep(2);
						} else {
							$this->import['blogs'][$_GET['blog']]['url'] = 'http://' . $_POST['subdomain'] . '.blogspot.com/';
							update_option('import-blogger', $this->import);
							$output .= "<del><p>$blog_opt</p></del>\n";
							continue;
						}
						$paramary = $optary['modify'];
					} else {
						$paramary = array_merge($_POST, $optary['modify']);
					}
					$response = $this->post_blogger($posturl, $headers, $paramary);
					if ( $response['code'] >= 400 || strstr($response['body'], 'There are errors on this form') )
						die('<p>'.__('Error on form submission. Retry or reset the importer.').'</p>' . addslashes(print_r($response, 1)));
				}
				$output .= "<del><p>$blog_opt</p></del>\n";
			} elseif ( is_array($this->import['blogs'][$_GET['blog']]['options']["$blog_opt"]['backup']) ) {
				// This option set has already been backed up.
				$output .= "<del><p>$blog_opt</p></del>\n";
			} elseif ( ! $form ) {
				// This option page needs to be downloaded and given to the browser for submission back to this script.
				$response = $this->get_blogger("http://www.blogger.com/{$blog_opt}.g?blogID={$_GET['blog']}", $this->import['cookies']);
				$this->import['blogs'][$_GET['blog']]['options']["$blog_opt"]['cookies'] = $response['cookies'];
				update_option('import-blogger',$this->import);
				$body = $response['body'];
				$body = preg_replace("|\<!DOCTYPE.*\<body[^>]*>|ms","",$body);
				$body = preg_replace("|/?{$blog_opt}.do|","admin.php?import=blogger&amp;noheader=true&amp;step=2&amp;blog={$_GET['blog']}&amp;form={$blog_opt}",$body);
				$body = str_replace("name='submit'","name='supermit'",$body);
				$body = str_replace('name="submit"','name="supermit"',$body);
				$body = str_replace('</body>','',str_replace('</html>','',$body));
				$form = "<div style='height:0px;width:0px;overflow:hidden;'>";
				$form.= $body;
				$form.= "</div><script type='text/javascript'>forms=document.getElementsByTagName('form');for(i=0;i<forms.length;i++){if(forms[i].action.search('{$blog_opt}')){forms[i].submit();break;}}</script>";
				$output.= "<p><strong>$blog_opt</strong> in progress, please wait...</p>\n";
			} else {
				$output.= "<p>$blog_opt</p>\n";
			}
		}
		if ( $form )
			die($output . $form);

		$this->set_next_step(4);
		$this->do_next_step();
	}

	// Step 3: Cancelled :-)

	// Step 4: Publish with the new template and settings.
	function publish_blog() {
		$this->publish_blogger(5, __('Publishing with new template and options'));
	}

	// Step 5: Get the archive URLs from the new blog.
	function get_archive_urls() {
		$bloghtml = $this->get_blogger($this->import['blogs'][$_GET['blog']]['url']);
		if (! strstr($bloghtml['body'], '<a class="archive"') )
			die(__('Your Blogger blog did not take the new template or did not respond.'));
		preg_match_all('#<a class="archive" href="([^"]*)"#', $bloghtml['body'], $archives);
		foreach ($archives[1] as $archive) {
			$this->import['blogs'][$_GET['blog']]['archives'][$archive] = false;
		}
		$this->set_next_step(6);
		$this->do_next_step();
	}

	// Step 6: Get each monthly archive, import it, mark it done.
	function get_archive() {
		global $wpdb;
		$output = '<h2>'.__('Importing Blogger archives into WordPress').'</h2>';
		$did_one = false;
		$post_array = $posts = array();
		foreach ( $this->import['blogs'][$_GET['blog']]['archives'] as $url => $status ) {
			$archivename = substr(basename($url),0,7);
			if ( $status || $did_one ) {
				$foo = 'bar';
				// Do nothing.
			} else {
				// Import the selected month
				$postcount = 0;
				$skippedpostcount = 0;
				$commentcount = 0;
				$skippedcommentcount = 0;
				$status = __('in progress...');
				$this->import['blogs'][$_GET['blog']]['archives']["$url"] = $status;
				update_option('import-blogger', $import);
				$archive = $this->get_blogger($url);
				if ( $archive['code'] > 200 )
					continue;	
				$posts = explode('<wordpresspost>', $archive['body']);
				for ($i = 1; $i < count($posts); $i = $i + 1) {
					$postparts = explode('<wordpresscomment>', $posts[$i]);
					$postinfo = explode('|W|P|', $postparts[0]);
					$post_date = $postinfo[0];
					$post_content = $postinfo[2];
					// Don't try to re-use the original numbers
					// because the new, longer numbers are too
					// big to handle as ints.
					//$post_number = $postinfo[3];
					$post_title = ( $postinfo[4] != '' ) ? $postinfo[4] : $postinfo[3];
					$post_author_name = $wpdb->escape(trim($postinfo[1]));
					$post_author_email = $postinfo[5] ? $postinfo[5] : 'user@wordpress.org';
	
					if ( $this->lump_authors ) {
						// Ignore Blogger authors. Use the current user_ID for all posts imported.
						$post_author = $GLOBALS['user_ID'];
					} else {
						// Add a user for each new author encountered.
						if (! username_exists($post_author_name) ) {
							$user_login = $wpdb->escape($post_author_name);
							$user_email = $wpdb->escape($post_author_email);
							$user_password = substr(md5(uniqid(microtime())), 0, 6);
							$result = wp_create_user( $user_login, $user_password, $user_email );
							$status.= "Registered user <strong>$user_login</strong>. ";
							$this->import['blogs'][$_GET['blog']]['newusers'][] = $user_login;
						}
						$userdata = get_userdatabylogin( $post_author_name );
						$post_author = $userdata->ID;
					}
					$post_date = explode(' ', $post_date);
					$post_date_Ymd = explode('/', $post_date[0]);
					$postyear = $post_date_Ymd[2];
					$postmonth = zeroise($post_date_Ymd[0], 2);
					$postday = zeroise($post_date_Ymd[1], 2);
					$post_date_His = explode(':', $post_date[1]);
					$posthour = zeroise($post_date_His[0], 2);
					$postminute = zeroise($post_date_His[1], 2);
					$postsecond = zeroise($post_date_His[2], 2);
	
					if (($post_date[2] == 'PM') && ($posthour != '12'))
						$posthour = $posthour + 12;
					else if (($post_date[2] == 'AM') && ($posthour == '12'))
						$posthour = '00';
	
					$post_date = "$postyear-$postmonth-$postday $posthour:$postminute:$postsecond";
	
					$post_content = addslashes($post_content);
					$post_content = str_replace(array('<br>','<BR>','<br/>','<BR/>','<br />','<BR />'), "\n", $post_content); // the XHTML touch... ;)
	
					$post_title = addslashes($post_title);
			
					$post_status = 'publish';
	
					if ( $ID = post_exists($post_title, '', $post_date) ) {
						$post_array[$i]['ID'] = $ID;
						$skippedpostcount++;
					} else {
						$post_array[$i]['post'] = compact('post_author', 'post_content', 'post_title', 'post_category', 'post_author', 'post_date', 'post_status');
						$post_array[$i]['comments'] = false;
					}

					// Import any comments attached to this post.
					if ($postparts[1]) :
					for ($j = 1; $j < count($postparts); $j = $j + 1) {
						$commentinfo = explode('|W|P|', $postparts[$j]);
						$comment_date = explode(' ', $commentinfo[0]);
						$comment_date_Ymd = explode('/', $comment_date[0]);
						$commentyear = $comment_date_Ymd[2];
						$commentmonth = zeroise($comment_date_Ymd[0], 2);
						$commentday = zeroise($comment_date_Ymd[1], 2);
						$comment_date_His = explode(':', $comment_date[1]);
						$commenthour = zeroise($comment_date_His[0], 2);
						$commentminute = zeroise($comment_date_His[1], 2);
						$commentsecond = '00';
						if (($comment_date[2] == 'PM') && ($commenthour != '12'))
							$commenthour = $commenthour + 12;
						else if (($comment_date[2] == 'AM') && ($commenthour == '12'))
							$commenthour = '00';
						$comment_date = "$commentyear-$commentmonth-$commentday $commenthour:$commentminute:$commentsecond";
						$comment_author = addslashes(strip_tags(html_entity_decode($commentinfo[1])));
						if ( strpos($commentinfo[1], 'a href') ) {
							$comment_author_parts = explode('&quot;', htmlentities($commentinfo[1]));
							$comment_author_url = $comment_author_parts[1];
						} else $comment_author_url = '';
						$comment_content = $commentinfo[2];
						$comment_content = str_replace(array('<br>','<BR>','<br/>','<BR/>','<br />','<BR />'), "\n", $comment_content);
						$comment_approved = 1;
						if ( comment_exists($comment_author, $comment_date) ) {
							$skippedcommentcount++;
						} else {
							$comment = compact('comment_author', 'comment_author_url', 'comment_date', 'comment_content', 'comment_approved');
							$post_array[$i]['comments'][$j] = wp_filter_comment($comment);
						}
						$commentcount++;
					}
					endif;
					$postcount++;
				}
				if ( count($post_array) ) {
					krsort($post_array);
					foreach($post_array as $post) {
						if ( ! $comment_post_ID = $post['ID'] )
							$comment_post_ID = wp_insert_post($post['post']);
						if ( $post['comments'] ) {
							foreach ( $post['comments'] as $comment ) {
								$comment['comment_post_ID'] = $comment_post_ID;
								wp_insert_comment($comment);
							}
						}
					}
				}
				$status = "$postcount ".__('post(s) parsed,')." $skippedpostcount ".__('skipped...')." $commentcount ".__('comment(s) parsed,')." $skippedcommentcount ".__('skipped...').' <strong>'.__('Done').'</strong>';
				$import = $this->import;
				$import['blogs'][$_GET['blog']]['archives']["$url"] = $status;
				update_option('import-blogger', $import);
				$did_one = true;
			}
			$output.= "<p>$archivename $status</p>\n";
 		}
		if ( ! $did_one )
			$this->set_next_step(7);
		die( $this->refresher(1000) . $output );
	}

	// Step 7: Restore the backed-up settings to Blogger
	function restore_settings() {
		$output = '<h1>'.__('Restoring your Blogger options')."</h1>\n";
		$did_one = false;
		// Restore options in reverse order.
		if ( ! $this->import['reversed'] ) {
			$this->import['blogs'][$_GET['blog']]['options'] = array_reverse($this->import['blogs'][$_GET['blog']]['options'], true);
			$this->import['reversed'] = true;
			update_option('import-blogger', $this->import);
		}
		foreach ( $this->import['blogs'][$_GET['blog']]['options'] as $blog_opt => $optary ) {
			if ( $did_one ) {
				$output .= "<p>$blog_opt</p>\n";
			} elseif ( $optary['restored'] || ! $optary['modify'] ) {
				$output .= "<p><del>$blog_opt</del></p>\n";
			} else {
				$posturl = "http://www.blogger.com/{$blog_opt}.do";
				$headers = array_merge($this->import['blogs'][$_GET['blog']]['options']["$blog_opt"]['cookies'], $this->import['cookies']);
				if ( 'blog-publishing' == $blog_opt) {
					if ( $optary['backup']['publishMode'] > 0 ) {
						$response = $this->get_blogger("http://www.blogger.com/blog-publishing.g?blogID={$_GET['blog']}&publishMode={$optary['backup']['publishMode']}", $headers);
						sleep(2);
						if ( $response['code'] >= 400 )
							die('<h1>Error restoring publishMode.</h1><p>Please tell the devs.</p>' . addslashes(print_r($response, 1)) );
					}
				}
				if ( $optary['backup'] != $optary['modify'] ) {
					$response = $this->post_blogger($posturl, $headers, $optary['backup']);
					if ( $response['code'] >= 400 || strstr($response['body'], 'There are errors on this form') ) {
						$this->import['blogs'][$_GET['blog']]['options']["$blog_opt"]['error'] = true;
						update_option('import-blogger', $this->import);
						$output .= "<p><strong>$blog_opt</strong> ".__('failed. Trying again.').'</p>';
					} else {
						$this->import['blogs'][$_GET['blog']]['options']["$blog_opt"]['restored'] = true;
						update_option('import-blogger', $this->import);
						$output .= "<p><strong>$blog_opt</strong> ".__('restored.').'</p>';
					}
				}
				$did_one = true;
			}
		}

		if ( $did_one ) {
			die( $this->refresher(1000) . $output );
		} elseif ( $this->import['blogs'][$_GET['blog']]['options']['blog-publishing']['backup']['publishMode'] > 0 ) {
			$this->set_next_step(9);
		} else {
			$this->set_next_step(8);
		}

		$this->do_next_step();
	}

	// Step 8: Republish, all back to normal
	function republish_blog() {
		$this->publish_blogger(9, __('Publishing with original template and options'));
	}

	// Step 9: Congratulate the user
	function congrats() {
		echo '<h1>'.__('Congratulations!').'</h1><p>'.__('Now that you have imported your Blogger blog into WordPress, what are you going to do? Here are some suggestions:').'</p><ul><li>'.__('That was hard work! Take a break.').'</li>';
		if ( count($this->import['blogs']) > 1 )
			echo '<li>'.__('In case you haven\'t done it already, you can import the posts from your other blogs:'). $this->show_blogs() . '</li>';
		if ( $n = count($this->import['blogs'][$_GET['blog']]['newusers']) )
			echo '<li>'.__('Go to <a href="users.php" target="_parent">Authors & Users</a>, where you can modify the new user(s) or delete them. If you want to make all of the imported posts yours, you will be given that option when you delete the new authors.').'</li>';
		echo '<li>'.__('For security, click the link below to reset this importer. That will clear your Blogger credentials and options from the database.').'</li>';
		echo '</ul>';
	}

	// Figures out what to do, then does it.
	function start() {
		if ( $_GET['restart'] == 'true' ) {
			$this->restart();
		}
		
		if ( isset($_GET['noheader']) ) {
			$this->import = get_settings('import-blogger');

			if ( false === $this->import ) {
				$step = 0;
			} elseif ( isset($_GET['step']) ) {
				$step = (int) $_GET['step'];
			} elseif ( isset($_GET['blog']) && isset($this->import['blogs'][$_GET['blog']]['nextstep']) ) {
				$step = $this->import['blogs'][$_GET['blog']]['nextstep'];
			} elseif ( is_array($this->import['blogs']) ) {
				$step = 1;
			} else {
				$step = 0;
			}
//echo "Step $step.";
//die('<pre>'.print_r($this->import,1).'</pre');
			switch ($step) {
				case 0 :
					$this->do_login();
					break;
				case 1 :
					$this->select_blog();
					break;
				case 2 :
					$this->backup_settings();
					break;
				case 3 :
					$this->wait_for_blogger();
					break;
				case 4 :
					$this->publish_blog();
					break;
				case 5 :
					$this->get_archive_urls();
					break;
				case 6 :
					$this->get_archive();
					break;
				case 7 :
					$this->restore_settings();
					break;
				case 8 :
					$this->republish_blog();
					break;
				case 9 :
					$this->congrats();
					break;
			}
			die;
			
		} else {
			$this->greet();
		}
	}

	function Blogger_Import() {
		// This space intentionally left blank.
	}
}

$blogger_import = new Blogger_Import();

register_importer('blogger', 'Blogger', __('Import posts and comments from a Blogger account'), array ($blogger_import, 'start'));

?>
