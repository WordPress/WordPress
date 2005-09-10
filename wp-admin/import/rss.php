<?php

// Example:
// define('RSSFILE', '/home/example/public_html/rss.xml');
define('RSSFILE', 'rss.xml');

class RSS_Import {

	var $posts = array ();

	function header() {
		echo '<div class="wrap">';
		echo '<h2>'.__('Import RSS').'</h2>';
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
		global $wpdb;
		
		set_magic_quotes_runtime(0);
		$datalines = file(RSSFILE); // Read the file into an array
		$importdata = implode('', $datalines); // squish it
		$importdata = str_replace(array ("\r\n", "\r"), "\n", $importdata);

		preg_match_all('|<item>(.*?)</item>|is', $importdata, $this->posts);
		$this->posts = $this->posts[1];
		$index = 0;
		foreach ($this->posts as $post) {
			preg_match('|<title>(.*?)</title>|is', $post, $post_title);
			$post_title = $wpdb->escape(trim($post_title[1]));

			preg_match('|<pubdate>(.*?)</pubdate>|is', $post, $post_date);

			if ($post_date) {
				$post_date = strtotime($post_date[1]);
			} else {
				// if we don't already have something from pubDate
				preg_match('|<dc:date>(.*?)</dc:date>|is', $post, $post_date);
				$post_date = preg_replace('|([-+])([0-9]+):([0-9]+)$|', '\1\2\3', $post_date[1]);
				$post_date = str_replace('T', ' ', $post_date);
				$post_date = strtotime($post_date);
			}

			$post_date = gmdate('Y-m-d H:i:s', $post_date);

			preg_match_all('|<category>(.*?)</category>|is', $post, $categories);
			$categories = $categories[1];

			if (!$categories) {
				preg_match_all('|<dc:subject>(.*?)</dc:subject>|is', $post, $categories);
				$categories = $categories[1];
			}

			$cat_index = 0;
			foreach ($categories as $category) {
				$categories[$cat_index] = $wpdb->escape($this->unhtmlentities($category));
				$cat_index++;
			}

			preg_match('|<guid.+?>(.*?)</guid>|is', $post, $guid);
			if ($guid)
				$guid = $wpdb->escape(trim($guid[1]));
			else
				$guid = '';

			preg_match('|<content:encoded>(.*?)</content:encoded>|is', $post, $post_content);
			$post_content = str_replace(array ('<![CDATA[', ']]>'), '', $wpdb->escape(trim($post_content[1])));

			if (!$post_content) {
				// This is for feeds that put content in description
				preg_match('|<description>(.*?)</description>|is', $post, $post_content);
				$post_content = $wpdb->escape($this->unhtmlentities(trim($post_content[1])));
			}

			// Clean up content
			$post_content = preg_replace('|<(/?[A-Z]+)|e', "'<' . strtolower('$1')", $post_content);
			$post_content = str_replace('<br>', '<br />', $post_content);
			$post_content = str_replace('<hr>', '<hr />', $post_content);

			$post_author = 1;
			$post_status = 'publish';
			$post_date_gmt = $post_date; // FIXME
			$this->posts[$index] = compact('post_author', 'post_date', 'post_date_gmt', 'post_content', 'post_title', 'post_status', 'guid', 'categories');
			$index++;
		}
	}

	function import_posts() {
		echo '<ol>';

		foreach ($this->posts as $post) {
			echo "<li>".__('Importing post...');

			extract($post);

			if ($post_id = post_exists($post_title, $post_content, $post_date)) {
				echo __('Post already imported');
			} else {
				$post_id = wp_insert_post($post);
				if (!$post_id)
					die(__("Couldn't get post ID"));
	
				if (0 != count($categories))
					wp_create_categories($categories, $post_id);
				echo __('Done !');
			}
			echo '</li>';
		}

		echo '</ol>';

	}

	function import() {
		// FIXME:  Don't die
		if ('' == RSSFILE)
			die("You must edit the RSSFILE line as described on the <a href='import-mt.php'>previous page</a> to continue.");

		if (!file_exists(RSSFILE))
			die("The file you specified does not seem to exist. Please check the path you've given.");

		$this->get_posts();
		$this->import_posts();
		echo '<h3>All done. <a href="' . get_option('home') . '">Have fun!</a></h3>';
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
				$this->import();
				break;
		}
	}

	function RSS_Import() {
		// Nothing.	
	}
}

$rss_import = new RSS_Import();

register_importer('rss', 'RSS', 'Import posts from and RSS feed', array ($rss_import, 'dispatch'));
?>