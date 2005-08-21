<?php
class RSS_Import {

	var $authors = array ();
	var $posts = array ();

	function header() {
		echo '<div class="wrap">';
		echo '<h2>'.__('Import RSS').'</h2>';
	}

	function footer() {
		echo '</div>';
	}

	function greet() {
		$this->header();
?>
<p>Howdy! This importer allows you to extract posts from any RSS 2.0 file into your blog. This is useful if you want to import your posts from a system that is not handled by a custom import tool. To get started you must edit the following line in this file (<code>import/rss.php</code>) </p>
<p><code>define('RSSFILE', '');</code></p>
<p>You want to define where the RSS file we'll be working with is, for example: </p>
<p><code>define('RSSFILE', 'rss.xml');</code></p>
<p>You have to do this manually for security reasons. When you're done reload this page and we'll take you to the next step.</p>
<?php if ('' != RSSFILE) : ?>
<a href="admin.php?import=rss&amp;step=1">Begin RSS Import &raquo;</a>
<?php

		endif;
		$this->footer();
	}

	function get_posts() {
		set_magic_quotes_runtime(0);
		$datalines = file(RSSFILE); // Read the file into an array
		$importdata = implode('', $datalines); // squish it
		$importdata = str_replace(array ("\r\n", "\r"), "\n", $importdata);

		preg_match_all('|<item>(.*?)</item>|is', $importdata, $posts);
		$this->posts = $posts[1];
	}

	function import_posts() {
		echo '<ol>';
		foreach ($this->posts as $post)
			: $title = $date = $categories = $content = $post_id = '';
		echo "<li>Importing post... ";

		preg_match('|<title>(.*?)</title>|is', $post, $title);
		$title = $wpdb->escape(trim($title[1]));
		$post_name = sanitize_title($title);

		preg_match('|<pubdate>(.*?)</pubdate>|is', $post, $date);

		if ($date)
			: $date = strtotime($date[1]);
		else
			: // if we don't already have something from pubDate
			preg_match('|<dc:date>(.*?)</dc:date>|is', $post, $date);
		$date = preg_replace('|([-+])([0-9]+):([0-9]+)$|', '\1\2\3', $date[1]);
		$date = str_replace('T', ' ', $date);
		$date = strtotime($date);
		endif;

		$post_date = gmdate('Y-m-d H:i:s', $date);

		preg_match_all('|<category>(.*?)</category>|is', $post, $categories);
		$categories = $categories[1];

		if (!$categories)
			: preg_match_all('|<dc:subject>(.*?)</dc:subject>|is', $post, $categories);
		$categories = $categories[1];
		endif;

		preg_match('|<guid.+?>(.*?)</guid>|is', $post, $guid);
		if ($guid)
			$guid = $wpdb->escape(trim($guid[1]));
		else
			$guid = '';

		preg_match('|<content:encoded>(.*?)</content:encoded>|is', $post, $content);
		$content = str_replace(array ('<![CDATA[', ']]>'), '', $wpdb->escape(trim($content[1])));

		if (!$content)
			: // This is for feeds that put content in description
			preg_match('|<description>(.*?)</description>|is', $post, $content);
		$content = $wpdb->escape(unhtmlentities(trim($content[1])));
		endif;

		// Clean up content
		$content = preg_replace('|<(/?[A-Z]+)|e', "'<' . strtolower('$1')", $content);
		$content = str_replace('<br>', '<br />', $content);
		$content = str_replace('<hr>', '<hr />', $content);

		// This can mess up on posts with no titles, but checking content is much slower
		// So we do it as a last resort
		if ('' == $title)
			: $dupe = $wpdb->get_var("SELECT ID FROM $wpdb->posts WHERE post_content = '$content' AND post_date = '$post_date'");
		else
			: $dupe = $wpdb->get_var("SELECT ID FROM $wpdb->posts WHERE post_title = '$title' AND post_date = '$post_date'");
		endif;

		// Now lets put it in the DB
		if ($dupe)
			: echo 'Post already imported';
		else
			: $wpdb->query("INSERT INTO $wpdb->posts 
					(post_author, post_date, post_date_gmt, post_content, post_title,post_status, comment_status, ping_status, post_name, guid)
					VALUES 
					('$post_author', '$post_date', DATE_ADD('$post_date', INTERVAL '$add_hours:$add_minutes' HOUR_MINUTE), '$content', '$title', 'publish', '$comment_status', '$ping_status', '$post_name', '$guid')");
		$post_id = $wpdb->get_var("SELECT ID FROM $wpdb->posts WHERE post_title = '$title' AND post_date = '$post_date'");
		if (!$post_id)
			die("couldn't get post ID");
		if (0 != count($categories))
			: foreach ($categories as $post_category)
				: $post_category = unhtmlentities($post_category);
		// See if the category exists yet
		$cat_id = $wpdb->get_var("SELECT cat_ID from $wpdb->categories WHERE cat_name = '$post_category'");
		if (!$cat_id && '' != trim($post_category)) {
			$cat_nicename = sanitize_title($post_category);
			$wpdb->query("INSERT INTO $wpdb->categories (cat_name, category_nicename) VALUES ('$post_category', '$cat_nicename')");
			$cat_id = $wpdb->get_var("SELECT cat_ID from $wpdb->categories WHERE cat_name = '$post_category'");
		}
		if ('' == trim($post_category))
			$cat_id = 1;
		// Double check it's not there already
		$exists = $wpdb->get_row("SELECT * FROM $wpdb->post2cat WHERE post_id = $post_id AND category_id = $cat_id");

		if (!$exists) {
			$wpdb->query("
						INSERT INTO $wpdb->post2cat
						(post_id, category_id)
						VALUES
						($post_id, $cat_id)
						");
		}
		endforeach;
		else
			: $exists = $wpdb->get_row("SELECT * FROM $wpdb->post2cat WHERE post_id = $post_id AND category_id = 1");
		if (!$exists)
			$wpdb->query("INSERT INTO $wpdb->post2cat (post_id, category_id) VALUES ($post_id, 1) ");
		endif;
		echo 'Done!</li>';
		endif;

		endforeach;
		echo '</ol>';

	}
	
	
	function import() {
		// FIXME:  Don't die.
		if ('' != RSSFILE && !file_exists(RSSFILE)) die("The file you specified does not seem to exist. Please check the path you've given.");
		if ('' == RSSFILE) die("You must edit the RSSFILE line as described on the <a href='import-mt.php'>previous page</a> to continue.");
	
		$this->get_posts();
		$this->import_posts();
		echo '<h3>All done. <a href="../">Have fun!</a></h3>';
	}
	
	function dispatch() {
		if (empty($_GET['step']))
			$step = 0;
		else
			$step = (int) $_GET['step'];
		
		switch ($step) {
			case 0:
				$this->greet();
				break;
			case 1:
				$this->import();
				break;
		}
	}
	
	function RSS_Import() {
		// Nothing.	
	}
}

$rss_import = new RSS_Import();

register_importer('rss', 'RSS', 'Import posts from and RSS feed', array($rss_import, 'dispatch'));

?>