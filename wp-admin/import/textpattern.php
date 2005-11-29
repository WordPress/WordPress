<?php

/*
This Import Script is written by Aaron Brazell of Technosailor.com

It was developed using a large blog running Textpattern 4.0.2 and
  successfully imported nearly 3000 records at a time.  Higher
  scalability is uncertain.
  
  BACKUP YOUR DATABASE PRIOR TO RUNNING THIS IMPORT SCRIPT
*/

// BEGIN EDITING

// $txpconfig options can be found in the Textpattern %textpattern%/config.php file
$txpcfg['db'] = 'textpattern';
$txpcfg['user'] = 'root';
$txpcfg['pass'] = '';
$txpcfg['host'] = 'localhost';
$txpcfg['table_prefix'] = '';

// STOP EDITING

/**
	Add These Functions to make our lives easier
**/
if(!function_exists('get_cat_nicename'))
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

	var $posts = array ();
	var $file;

	function header() 
	{
		echo '<div class="wrap">';
		echo '<h2>'.__('Import Textpattern').'</h2>';
	}

	function footer() 
	{
		echo '</div>';
	}
	
	function greet() 
	{
		global $txpcfg;
		
		_e('<p>Howdy! This importer allows you to extract posts from any Textpattern 4.0.2+ into your blog. This has not been tested on previous versions of Textpattern.  Mileage may vary.</p>');
		_e('<p>Your Textpattern Configuration settings are as follows:</p>');
		_e('<ul><li><strong>Textpattern Database Name:</strong> '.$txpcfg['db'].'</li>');
		_e('<li><strong>Textpattern Database User:</strong> '.$txpcfg['user'].'</li>');
		_e('<li><strong>Textpattern Database Password:</strong> '.$txpcfg['pass'].'</li>');
		_e('<li><strong>Textpattern Database Host:</strong> '.$txpcfg['host'].'</li>');
		_e('</ul>');
		_e('<p>If this is incorrect, please modify settings in wp-admin/import/textpattern.php</p>');
		_e('<form action="admin.php?import=textpattern&amp;step=1" method="post">');
		_e('<input type="submit" name="submit" value="Import Categories" />');
		_e('</form>');
	}

	function get_txp_links()
	{
		//General Housekeeping
		global $txpdb;
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
									  echo'SELECT 
										id,
										date,
										category,
										url,
										linkname,
										description
									  FROM '.$prefix.'txp_link';
	}
	
	function get_txp_cats() 
	{
		
		// General Housekeeping
		global $txpdb;
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
	
	function get_txp_posts()
	{
		// General Housekeeping
		global $txpdb;
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
										comments_count,
										ADDDATE(Posted, "INTERVAL '.get_settings('gmt_offset').' HOURS") AS post_date_gmt,
										ADDDATE(LastMod, "INTERVAL '.get_settings('gmt_offset').' HOURS") AS post_modified_gmt
							   		FROM '.$prefix.'textpattern
							   		', ARRAY_A);
	}
	
	function get_txp_comments()
	{
		// General Housekeeping
		global $txpdb;
		set_magic_quotes_runtime(0);
		$prefix = get_option('tpre');
		
		// Get Comments
		return $txpdb->get_results('SELECT * FROM '.$prefix.'txp_discuss', ARRAY_A);
	}
	
	function get_txp_users()
	{
		// General Housekeeping
		global $txpdb;
		set_magic_quotes_runtime(0);
		$prefix = get_option('tpre');
		
		// Get Users
		$users = $txpdb->get_results('SELECT
										user_id,
										name,
										RealName,
										email,
										privs
							   		FROM '.$prefix.'txp_users', ARRAY_A);
		return $users;
	}
	
	function links2wp($links='')
	{
		// General Housekeeping
		global $wpdb;
		$count = 0;
		
		// Deal with the links
		if(is_array($links))
		{
			echo __('<p>Importing Links...<br /><br /></p>');
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
			echo __('<p>Done! <strong>'.$count.'</strong> Links imported.<br /><br /></p>');
			return true;
		}
		echo 'No Links to Import!';
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
			echo __('<p>Importing Users...<br /><br /></p>');
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
			
			
			echo __('<p>Done! <strong>'.$count.'</strong> users imported.<br /><br /></p>');
			return true;
		}// End if(is_array($users)
		
		echo 'No Users to Import!';
		return false;
		
	}// End function user2wp()
	
	function cat2wp($categories='') 
	{
		// General Housekeeping
		global $wpdb;
		$count = 0;
		$txpcat2wpcat = array();
		
		// Do the Magic
		if(is_array($categories))
		{
			echo __('<p>Importing Categories...<br /><br /></p>');
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
			echo __('<p>Done! <strong>'.$count.'</strong> categories imported.<br /><br /></p>');
			return true;
		}
		echo 'No Categories to Import!';
		return false;
		
	}
	
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
			echo __('<p>Importing Posts...<br /><br /></p>');
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

				if(!empty($cats)) { wp_set_post_cats('', $ret_id, $cats); }
			}
		}
		// Store ID translation for later use
		add_option('txpposts2wpposts',$txpposts2wpposts);
		
		echo __('<p>Done! <strong>'.$count.'</strong> posts imported.<br /><br /></p>');
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
			echo __('<p>Importing Comments...<br /><br /></p>');
			foreach($comments as $comment)
			{
				$count++;
				extract($comment);
				
				// WordPressify Data
				$comment_ID = ltrim($discussid, '0');
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
							'comment_post_ID'		=> $postarr[$parentid],
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
			
			
			echo __('<p>Done! <strong>'.$count.'</strong> comments imported.<br /><br /></p>');
			return true;
		}
		echo 'No Comments to Import!';
		return false;
	}
		
	function import_categories() 
	{	
		// Category Import	
		$cats = $this->get_txp_cats();
		$this->cat2wp($cats);
		add_option('txp_cats', $cats);
		
		
			
		_e('<form action="admin.php?import=textpattern&amp;step=2" method="post">');
		_e('<input type="submit" name="submit" value="Import Users" />');
		_e('</form>');

	}
	
	function import_users()
	{
		// User Import
		$users = $this->get_txp_users(); 
		$this->users2wp($users);
		
		_e('<form action="admin.php?import=textpattern&amp;step=3" method="post">');
		_e('<input type="submit" name="submit" value="Import Posts" />');
		_e('</form>');
	}
	
	function import_posts()
	{
		// Post Import
		$posts = $this->get_txp_posts();
		$this->posts2wp($posts);
		
		_e('<form action="admin.php?import=textpattern&amp;step=4" method="post">');
		_e('<input type="submit" name="submit" value="Import Comments" />');
		_e('</form>');
	}
	
	function import_comments()
	{
		// Comment Import
		$comments = $this->get_txp_comments();
		$this->comments2wp($comments);
		
		_e('<form action="admin.php?import=textpattern&amp;step=5" method="post">');
		_e('<input type="submit" name="submit" value="Import Links" />');
		_e('</form>');
	}
	
	function import_links()
	{
		//Link Import
		$links = $this->get_txp_links();
		$this->links2wp($links);
		add_option('txp_links', $links);
		
		_e('<form action="admin.php?import=textpattern&amp;step=6" method="post">');
		_e('<input type="submit" name="submit" value="Finish" />');
		_e('</form>');
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
		$this->tips();
	}
	
	function tips()
	{
		echo'<p>Welcome to WordPress.  We hope (and expect!) that you will find this platform incredibly rewarding!  As a new WordPress user coming from Textpattern, there are some things that we would like to point out.  Hopefully, they will help your transition go as smoothly as possible.</p>';
		echo'<h3>Users</h3>';
		echo'<p>You have already setup WordPress and have been assigned an administrative login and password.  Forget it.  You didn\'t have that login in Textpattern, why should you have it here?  Instead we have taken care to import all of your users into our system.  Unfortunately there is one downside.  Because both WordPress and Textpattern uses a strong encryption hash with passwords, it is impossible to decrypt it and we are forced to assign temporary passwords to all your users.  <strong>Every user has the same username, but their passwords are reset to password123.</strong>  This includes you.  So <a href="/wp-login.php">Login</a> and change it.</p>';
		echo'<h3>Preserving Authors</h3>';
		echo'<p>Secondly, we have attempted to preserve post authors.  If you are the only author or contributor to your blog, then you are safe.  In most cases, we are successful in this preservation endeavor.  However, if we cannot ascertain the name of the writer due to discrepancies between database tables, we assign it to you, the administrative user.</p>';
		echo'<h3>Textile</h3>';
		echo'<p>Also, since you\'re coming from Textpattern, you probably have been using Textile to format your comments and posts.  If this is the case, we recommend downloading and installing <a href="http://www.huddledmasses.org/2004/04/19/wordpress-plugin-textile-20/">Textile for WordPress</a>.  Trust me... You\'ll want it.</p>';
		echo'<h3>WordPress Resources</h3>';
		echo'<p>Finally, there are numerous WordPress resources around the internet.  Some of them are:</p>';
		echo'<ul>';
		echo'<li><a href="http://www.wordpress.org">The official WordPress site</a></li>';
		echo'<li><a href="http://wordpress.org/support/">The WordPress support forums</li>';
		echo'<li><a href="http://codex.wordpress.org">The Codex (In other words, the WordPress Bible)</a></li>';
		echo'</ul>';
		echo'<p>That\'s it! What are you waiting for? Go <a href="/wp-login.php">login</a>!</p>';
	}
	
	function dispatch() 
	{
		global $txpdb, $txpcfg;

		if (empty ($_GET['step']))
			$step = 0;
		else
			$step = (int) $_GET['step'];

		$this->header();
		
		if ( $step > 0 ) {
			add_option('tpre',$txpcfg['table_prefix']);
			$txpdb = new wpdb($txpcfg['user'], $txpcfg['pass'], $txpcfg['db'], $txpcfg['host']);
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
register_importer('textpattern', 'Textpattern', __('Import posts from a Textpattern Blog'), array ($txp_import, 'dispatch'));
?>
