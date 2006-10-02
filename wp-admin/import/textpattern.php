<?php
/**
	Add These Functions to make our lives easier
**/
if(!function_exists('get_catbynicename'))
{
	function get_catbynicename($category_nicename) 
	{
	global $wpdb;

	$cat_id -= 0; 	// force numeric
	$name = $wpdb->get_var('SELECT cat_ID FROM '.$wpdb->categories.' WHERE category_nicename="'.$category_nicename.'"');

	return $name;
	}
}

if(!function_exists('get_comment_count'))
{
	function get_comment_count($post_ID)
	{
		global $wpdb;
		return $wpdb->get_var('SELECT count(*) FROM '.$wpdb->comments.' WHERE comment_post_ID = '.$post_ID);
	}
}

if(!function_exists('link_exists'))
{
	function link_exists($linkname)
	{
		global $wpdb;
		return $wpdb->get_var('SELECT link_id FROM '.$wpdb->links.' WHERE link_name = "'.$wpdb->escape($linkname).'"');
	}
}

/**
	The Main Importer Class
**/
class Textpattern_Import {

	function header() 
	{
		echo '<div class="wrap">';
		echo '<h2>'.__('Import Textpattern').'</h2>';
		echo '<p>'.__('Steps may take a few minutes depending on the size of your database. Please be patient.').'</p>';
	}

	function footer() 
	{
		echo '</div>';
	}

	function greet() 
	{
		echo '<p>'.__('Howdy! This importer allows you to extract posts from any Textpattern 4.0.2+ into your blog. This has not been tested on previous versions of Textpattern.  Mileage may vary.').'</p>';
		echo '<p>'.__('Your Textpattern Configuration settings are as follows:').'</p>';
		echo '<form action="admin.php?import=textpattern&amp;step=1" method="post">';
		$this->db_form();
		echo '<input type="submit" name="submit" value="'.__('Import Categories').'" />';
		echo '</form>';
	}

	function get_txp_cats()
	{
		global $wpdb;
		// General Housekeeping
		$txpdb = new wpdb(get_option('txpuser'), get_option('txppass'), get_option('txpname'), get_option('txphost'));
		set_magic_quotes_runtime(0);
		$prefix = get_option('tpre');

		// Get Categories
		return $txpdb->get_results('SELECT 
										id,
										name,
										title
							   		 FROM '.$prefix.'txp_category 
							   		 WHERE type = "article"', 
									 ARRAY_A);
	}

	function get_txp_users()
	{
		global $wpdb;
		// General Housekeeping
		$txpdb = new wpdb(get_option('txpuser'), get_option('txppass'), get_option('txpname'), get_option('txphost'));
		set_magic_quotes_runtime(0);
		$prefix = get_option('tpre');

		// Get Users

		return $txpdb->get_results('SELECT
										user_id,
										name,
										RealName,
										email,
										privs
							   		FROM '.$prefix.'txp_users', ARRAY_A);
	}

	function get_txp_posts()
	{
		// General Housekeeping
		$txpdb = new wpdb(get_option('txpuser'), get_option('txppass'), get_option('txpname'), get_option('txphost'));
		set_magic_quotes_runtime(0);
		$prefix = get_option('tpre');

		// Get Posts
		return $txpdb->get_results('SELECT 
										ID,
										Posted,
										AuthorID,
										LastMod,
										Title,
										Body,
										Excerpt,
										Category1,
										Category2,
										Status,
										Keywords,
										url_title,
										comments_count
							   		FROM '.$prefix.'textpattern
							   		', ARRAY_A);
	}

	function get_txp_comments()
	{
		global $wpdb;
		// General Housekeeping
		$txpdb = new wpdb(get_option('txpuser'), get_option('txppass'), get_option('txpname'), get_option('txphost'));
		set_magic_quotes_runtime(0);
		$prefix = get_option('tpre');

		// Get Comments
		return $txpdb->get_results('SELECT * FROM '.$prefix.'txp_discuss', ARRAY_A);
	}

		function get_txp_links()
	{
		//General Housekeeping
		$txpdb = new wpdb(get_option('txpuser'), get_option('txppass'), get_option('txpname'), get_option('txphost'));
		set_magic_quotes_runtime(0);
		$prefix = get_option('tpre');

		return $txpdb->get_results('SELECT 
										id,
										date,
										category,
										url,
										linkname,
										description
									  FROM '.$prefix.'txp_link', 
									  ARRAY_A);						  
	}

	function cat2wp($categories='') 
	{
		// General Housekeeping
		global $wpdb;
		$count = 0;
		$txpcat2wpcat = array();
		// Do the Magic
		if(is_array($categories))
		{
			echo '<p>'.__('Importing Categories...').'<br /><br /></p>';
			foreach ($categories as $category) 
			{
				$count++;
				extract($category);


				// Make Nice Variables
				$name = $wpdb->escape($name);
				$title = $wpdb->escape($title);

				if($cinfo = category_exists($name))
				{
					$ret_id = wp_insert_category(array('cat_ID' => $cinfo, 'category_nicename' => $name, 'cat_name' => $title));
				}
				else
				{
					$ret_id = wp_insert_category(array('category_nicename' => $name, 'cat_name' => $title));
				}
				$txpcat2wpcat[$id] = $ret_id;
			}

			// Store category translation for future use
			add_option('txpcat2wpcat',$txpcat2wpcat);
			echo '<p>'.sprintf(__('Done! <strong>%1$s</strong> categories imported.'), $count).'<br /><br /></p>';
			return true;
		}
		echo __('No Categories to Import!');
		return false;
	}

	function users2wp($users='')
	{
		// General Housekeeping
		global $wpdb;
		$count = 0;
		$txpid2wpid = array();

		// Midnight Mojo
		if(is_array($users))
		{
			echo '<p>'.__('Importing Users...').'<br /><br /></p>';
			foreach($users as $user)
			{
				$count++;
				extract($user);

				// Make Nice Variables
				$name = $wpdb->escape($name);
				$RealName = $wpdb->escape($RealName);

				if($uinfo = get_userdatabylogin($name))
				{

					$ret_id = wp_insert_user(array(
								'ID'			=> $uinfo->ID,
								'user_login'	=> $name,
								'user_nicename'	=> $RealName,
								'user_email'	=> $email,
								'user_url'		=> 'http://',
								'display_name'	=> $name)
								);
				}
				else 
				{
					$ret_id = wp_insert_user(array(
								'user_login'	=> $name,
								'user_nicename'	=> $RealName,
								'user_email'	=> $email,
								'user_url'		=> 'http://',
								'display_name'	=> $name)
								);
				}
				$txpid2wpid[$user_id] = $ret_id;

				// Set Textpattern-to-WordPress permissions translation
				$transperms = array(1 => '10', 2 => '9', 3 => '5', 4 => '4', 5 => '3', 6 => '2', 7 => '0');

				// Update Usermeta Data
				$user = new WP_User($ret_id);
				if('10' == $transperms[$privs]) { $user->set_role('administrator'); }
				if('9'  == $transperms[$privs]) { $user->set_role('editor'); }
				if('5'  == $transperms[$privs]) { $user->set_role('editor'); }
				if('4'  == $transperms[$privs]) { $user->set_role('author'); }
				if('3'  == $transperms[$privs]) { $user->set_role('contributor'); }
				if('2'  == $transperms[$privs]) { $user->set_role('contributor'); }
				if('0'  == $transperms[$privs]) { $user->set_role('subscriber'); }

				update_usermeta( $ret_id, 'wp_user_level', $transperms[$privs] );
				update_usermeta( $ret_id, 'rich_editing', 'false');
			}// End foreach($users as $user)

			// Store id translation array for future use
			add_option('txpid2wpid',$txpid2wpid);


			echo '<p>'.sprintf(__('Done! <strong>%1$s</strong> users imported.'), $count).'<br /><br /></p>';
			return true;
		}// End if(is_array($users)

		echo __('No Users to Import!');
		return false;

	}// End function user2wp()

	function posts2wp($posts='')
	{
		// General Housekeeping
		global $wpdb;
		$count = 0;
		$txpposts2wpposts = array();
		$cats = array();

		// Do the Magic
		if(is_array($posts))
		{
			echo '<p>'.__('Importing Posts...').'<br /><br /></p>';
			foreach($posts as $post)
			{
				$count++;
				extract($post);

				// Set Textpattern-to-WordPress status translation
				$stattrans = array(1 => 'draft', 2 => 'private', 3 => 'draft', 4 => 'publish', 5 => 'publish');

				//Can we do this more efficiently?
				$uinfo = ( get_userdatabylogin( $AuthorID ) ) ? get_userdatabylogin( $AuthorID ) : 1;
				$authorid = ( is_object( $uinfo ) ) ? $uinfo->ID : $uinfo ;

				$Title = $wpdb->escape($Title);
				$Body = $wpdb->escape($Body);
				$Excerpt = $wpdb->escape($Excerpt);
				$post_status = $stattrans[$Status];

				// Import Post data into WordPress

				if($pinfo = post_exists($Title,$Body))
				{
					$ret_id = wp_insert_post(array(
							'ID'				=> $pinfo,
							'post_date'			=> $Posted,
							'post_date_gmt'		=> $post_date_gmt,
							'post_author'		=> $authorid,
							'post_modified'		=> $LastMod,
							'post_modified_gmt' => $post_modified_gmt,
							'post_title'		=> $Title,
							'post_content'		=> $Body,
							'post_excerpt'		=> $Excerpt,
							'post_status'		=> $post_status,
							'post_name'			=> $url_title,
							'comment_count'		=> $comments_count)
							);
				}
				else 
				{
					$ret_id = wp_insert_post(array(
							'post_date'			=> $Posted,
							'post_date_gmt'		=> $post_date_gmt,
							'post_author'		=> $authorid,
							'post_modified'		=> $LastMod,
							'post_modified_gmt' => $post_modified_gmt,
							'post_title'		=> $Title,
							'post_content'		=> $Body,
							'post_excerpt'		=> $Excerpt,
							'post_status'		=> $post_status,
							'post_name'			=> $url_title,
							'comment_count'		=> $comments_count)
							);
				}
				$txpposts2wpposts[$ID] = $ret_id;

				// Make Post-to-Category associations
				$cats = array();
				if($cat1 = get_catbynicename($Category1)) { $cats[1] = $cat1; }
				if($cat2 = get_catbynicename($Category2)) { $cats[2] = $cat2; }

				if(!empty($cats)) { wp_set_post_categories($ret_id, $cats); }
			}
		}
		// Store ID translation for later use
		add_option('txpposts2wpposts',$txpposts2wpposts);

		echo '<p>'.sprintf(__('Done! <strong>%1$s</strong> posts imported.'), $count).'<br /><br /></p>';
		return true;
	}

	function comments2wp($comments='')
	{
		// General Housekeeping
		global $wpdb;
		$count = 0;
		$txpcm2wpcm = array();
		$postarr = get_option('txpposts2wpposts');

		// Magic Mojo
		if(is_array($comments))
		{
			echo '<p>'.__('Importing Comments...').'<br /><br /></p>';
			foreach($comments as $comment)
			{
				$count++;
				extract($comment);

				// WordPressify Data
				$comment_ID = ltrim($discussid, '0');
				$comment_post_ID = $postarr[$parentid];
				$comment_approved = (1 == $visible) ? 1 : 0;
				$name = $wpdb->escape($name);
				$email = $wpdb->escape($email);
				$web = $wpdb->escape($web);
				$message = $wpdb->escape($message);

				if($cinfo = comment_exists($name, $posted))
				{
					// Update comments
					$ret_id = wp_update_comment(array(
							'comment_ID'			=> $cinfo,
							'comment_post_ID'		=> $comment_post_ID,
							'comment_author'		=> $name,
							'comment_author_email'	=> $email,
							'comment_author_url'	=> $web,
							'comment_date'			=> $posted,
							'comment_content'		=> $message,
							'comment_approved'		=> $comment_approved)
							);
				}
				else 
				{
					// Insert comments
					$ret_id = wp_insert_comment(array(
							'comment_post_ID'		=> $comment_post_ID,
							'comment_author'		=> $name,
							'comment_author_email'	=> $email,
							'comment_author_url'	=> $web,
							'comment_author_IP'		=> $ip,
							'comment_date'			=> $posted,
							'comment_content'		=> $message,
							'comment_approved'		=> $comment_approved)
							);
				}
				$txpcm2wpcm[$comment_ID] = $ret_id;
			}
			// Store Comment ID translation for future use
			add_option('txpcm2wpcm', $txpcm2wpcm);

			// Associate newly formed categories with posts
			get_comment_count($ret_id);


			echo '<p>'.sprintf(__('Done! <strong>%1$s</strong> comments imported.'), $count).'<br /><br /></p>';
			return true;
		}
		echo __('No Comments to Import!');
		return false;
	}

	function links2wp($links='')
	{
		// General Housekeeping
		global $wpdb;
		$count = 0;

		// Deal with the links
		if(is_array($links))
		{
			echo '<p>'.__('Importing Links...').'<br /><br /></p>';
			foreach($links as $link)
			{
				$count++;
				extract($link);

				// Make nice vars
				$category = $wpdb->escape($category);
				$linkname = $wpdb->escape($linkname);
				$description = $wpdb->escape($description);

				if($linfo = link_exists($linkname))
				{
					$ret_id = wp_insert_link(array(
								'link_id'			=> $linfo,
								'link_url'			=> $url,
								'link_name'			=> $linkname,
								'link_category'		=> $category,
								'link_description'	=> $description,
								'link_updated'		=> $date)
								);
				}
				else 
				{
					$ret_id = wp_insert_link(array(
								'link_url'			=> $url,
								'link_name'			=> $linkname,
								'link_category'		=> $category,
								'link_description'	=> $description,
								'link_updated'		=> $date)
								);
				}
				$txplinks2wplinks[$link_id] = $ret_id;
			}
			add_option('txplinks2wplinks',$txplinks2wplinks);
			echo '<p>';
			printf(__('Done! <strong>%s</strong> Links imported'), $count);
			echo '<br /><br /></p>';
			return true;
		}
		echo __('No Links to Import!');
		return false;
	}

	function import_categories() 
	{
		// Category Import
		$cats = $this->get_txp_cats();
		$this->cat2wp($cats);
		add_option('txp_cats', $cats);



		echo '<form action="admin.php?import=textpattern&amp;step=2" method="post">';
		printf('<input type="submit" name="submit" value="%s" />', __('Import Users'));
		echo '</form>';

	}

	function import_users()
	{
		// User Import
		$users = $this->get_txp_users(); 
		$this->users2wp($users);

		echo '<form action="admin.php?import=textpattern&amp;step=3" method="post">';
		printf('<input type="submit" name="submit" value="%s" />', __('Import Posts'));
		echo '</form>';
	}

	function import_posts()
	{
		// Post Import
		$posts = $this->get_txp_posts();
		$this->posts2wp($posts);

		echo '<form action="admin.php?import=textpattern&amp;step=4" method="post">';
		printf('<input type="submit" name="submit" value="%s" />', __('Import Comments'));
		echo '</form>';
	}

	function import_comments()
	{
		// Comment Import
		$comments = $this->get_txp_comments();
		$this->comments2wp($comments);

		echo '<form action="admin.php?import=textpattern&amp;step=5" method="post">';
		printf('<input type="submit" name="submit" value="%s" />', __('Import Links'));
		echo '</form>';
	}

	function import_links()
	{
		//Link Import
		$links = $this->get_txp_links();
		$this->links2wp($links);
		add_option('txp_links', $links);

		echo '<form action="admin.php?import=textpattern&amp;step=6" method="post">';
		printf('<input type="submit" name="submit" value="%s" />', __('Finish'));
		echo '</form>';
	}

	function cleanup_txpimport()
	{
		delete_option('tpre');
		delete_option('txp_cats');
		delete_option('txpid2wpid');
		delete_option('txpcat2wpcat');
		delete_option('txpposts2wpposts');
		delete_option('txpcm2wpcm');
		delete_option('txplinks2wplinks');
		delete_option('txpuser');
		delete_option('txppass');
		delete_option('txpname');
		delete_option('txphost');
		$this->tips();
	}

	function tips()
	{
		echo '<p>'.__('Welcome to WordPress.  We hope (and expect!) that you will find this platform incredibly rewarding!  As a new WordPress user coming from Textpattern, there are some things that we would like to point out.  Hopefully, they will help your transition go as smoothly as possible.').'</p>';
		echo '<h3>'.__('Users').'</h3>';
		echo '<p>'.sprintf(__('You have already setup WordPress and have been assigned an administrative login and password.  Forget it.  You didn\'t have that login in Textpattern, why should you have it here?  Instead we have taken care to import all of your users into our system.  Unfortunately there is one downside.  Because both WordPress and Textpattern uses a strong encryption hash with passwords, it is impossible to decrypt it and we are forced to assign temporary passwords to all your users.  <strong>Every user has the same username, but their passwords are reset to password123.</strong>  So <a href="%1$s">Login</a> and change it.'), '/wp-login.php').'</p>';
		echo '<h3>'.__('Preserving Authors').'</h3>';
		echo '<p>'.__('Secondly, we have attempted to preserve post authors.  If you are the only author or contributor to your blog, then you are safe.  In most cases, we are successful in this preservation endeavor.  However, if we cannot ascertain the name of the writer due to discrepancies between database tables, we assign it to you, the administrative user.').'</p>';
		echo '<h3>'.__('Textile').'</h3>';
		echo '<p>'.__('Also, since you\'re coming from Textpattern, you probably have been using Textile to format your comments and posts.  If this is the case, we recommend downloading and installing <a href="http://www.huddledmasses.org/category/development/wordpress/textile/">Textile for WordPress</a>.  Trust me... You\'ll want it.').'</p>';
		echo '<h3>'.__('WordPress Resources').'</h3>';
		echo '<p>'.__('Finally, there are numerous WordPress resources around the internet.  Some of them are:').'</p>';
		echo '<ul>';
		echo '<li>'.__('<a href="http://www.wordpress.org">The official WordPress site</a>').'</li>';
		echo '<li>'.__('<a href="http://wordpress.org/support/">The WordPress support forums</a>').'</li>';
		echo '<li>'.__('<a href="http://codex.wordpress.org">The Codex (In other words, the WordPress Bible)</a>').'</li>';
		echo '</ul>';
		echo '<p>'.sprintf(__('That\'s it! What are you waiting for? Go <a href="%1$s">login</a>!'), '/wp-login.php').'</p>';
	}

	function db_form()
	{
		echo '<ul>';
		printf('<li><label for="dbuser">%s</label> <input type="text" name="dbuser" id="dbuser" /></li>', __('Textpattern Database User:'));
		printf('<li><label for="dbpass">%s</label> <input type="password" name="dbpass" id="dbpass" /></li>', __('Textpattern Database Password:'));
		printf('<li><label for="dbname">%s</label> <input type="text" id="dbname" name="dbname" /></li>', __('Textpattern Database Name:'));
		printf('<li><label for="dbhost">%s</label> <input type="text" id="dbhost" name="dbhost" value="localhost" /></li>', __('Textpattern Database Host:'));
		printf('<li><label for="dbprefix">%s</label> <input type="text" name="dbprefix" id="dbprefix"  /></li>', __('Textpattern Table prefix (if any):'));
		echo '</ul>';
	}

	function dispatch() 
	{

		if (empty ($_GET['step']))
			$step = 0;
		else
			$step = (int) $_GET['step'];
		$this->header();

		if ( $step > 0 ) 
		{
			if($_POST['dbuser'])
			{
				if(get_option('txpuser'))
					delete_option('txpuser');
				add_option('txpuser',$_POST['dbuser']);
			}
			if($_POST['dbpass'])
			{
				if(get_option('txppass'))
					delete_option('txppass');
				add_option('txppass',$_POST['dbpass']);
			}

			if($_POST['dbname'])
			{
				if(get_option('txpname'))
					delete_option('txpname');
				add_option('txpname',$_POST['dbname']);
			}
			if($_POST['dbhost'])
			{
				if(get_option('txphost'))
					delete_option('txphost');
				add_option('txphost',$_POST['dbhost']); 
			}
			if($_POST['dbprefix'])
			{
				if(get_option('tpre'))
					delete_option('tpre');
				add_option('tpre',$_POST['dbprefix']); 
			}


		}

		switch ($step) 
		{
			default:
			case 0 :
				$this->greet();
				break;
			case 1 :
				$this->import_categories();
				break;
			case 2 :
				$this->import_users();
				break;
			case 3 :
				$this->import_posts();
				break;
			case 4 :
				$this->import_comments();
				break;
			case 5 :
				$this->import_links();
				break;
			case 6 :
				$this->cleanup_txpimport();
				break;
		}

		$this->footer();
	}

	function Textpattern_Import() 
	{
		// Nothing.
	}
}

$txp_import = new Textpattern_Import();
register_importer('textpattern', __('Textpattern'), __('Import posts from a Textpattern Blog'), array ($txp_import, 'dispatch'));
?>
