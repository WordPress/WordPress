<?php

class LJ_Import {

	var $posts = array ();
	var $file;

	function header() {
		echo '<div class="wrap">';
		echo '<h2>'.__('Import LiveJournal').'</h2>';
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
		echo '<p>'.__('Howdy! This importer allows you to extract posts from LiveJournal XML export file into your blog.  Pick a LiveJournal file to upload and click Import.').'</p>';
		wp_import_upload_form("admin.php?import=livejournal&amp;step=1");
	}

	function get_posts() {
		global $wpdb, $current_user;
		
		set_magic_quotes_runtime(0);
		$datalines = file($this->file); // Read the file into an array
		$importdata = implode('', $datalines); // squish it
		$importdata = str_replace(array ("\r\n", "\r"), "\n", $importdata);

		preg_match_all('|<entry>(.*?)</entry>|is', $importdata, $this->posts);
		$this->posts = $this->posts[1];
		$index = 0;
		foreach ($this->posts as $post) {
			preg_match('|<subject>(.*?)</subject>|is', $post, $post_title);
			$post_title = $wpdb->escape(trim($post_title[1]));
			if ( empty($post_title) ) {
				preg_match('|<eventid>(.*?)</eventid>|is', $post, $post_title);
				$post_title = $wpdb->escape(trim($post_title[1]));
			}
	
			preg_match('|<eventtime>(.*?)</eventtime>|is', $post, $post_date);

			$post_date = strtotime($post_date[1]);
			$post_date = gmdate('Y-m-d H:i:s', $post_date);

			preg_match('|<event>(.*?)</event>|is', $post, $post_content);
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

			$post_author = $current_user->ID;
			$post_status = 'publish';
			$this->posts[$index] = compact('post_author', 'post_date', 'post_content', 'post_title', 'post_status');
			$index++;
		}
	}

	function import_posts() {
		echo '<ol>';

		foreach ($this->posts as $post) {
			echo "<li>".__('Importing post...');

			extract($post);

			if ($post_id = post_exists($post_title, $post_content, $post_date)) {
				_e('Post already imported');
			} else {
				$post_id = wp_insert_post($post);
				if (!$post_id) {
					_e("Couldn't get post ID");
					return;
				}

				_e('Done !');
			}
			echo '</li>';
		}

		echo '</ol>';

	}

	function import() {
		$file = wp_import_handle_upload();
		if ( isset($file['error']) ) {
			echo $file['error'];
			return;
		}

		$this->file = $file['file'];
		$this->get_posts();
		$this->import_posts();
		wp_import_cleanup($file['id']);
		
		echo '<h3>';
		printf(__('All done. <a href="%s">Have fun!</a>'), get_option('home'));
		echo '</h3>';
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
				$this->import();
				break;
		}
		
		$this->footer();
	}

	function LJ_Import() {
		// Nothing.	
	}
}

$livejournal_import = new LJ_Import();

//register_importer('livejournal', 'LiveJournal', __('Import posts from LiveJournal'), array ($livejournal_import, 'dispatch'));
?>
