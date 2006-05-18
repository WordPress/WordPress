<?php

/* By Shayne Sweeney - http://www.theshayne.com/ */

class BW_Import {

	var $file;

	function header() {
		echo '<div class="wrap">';
		echo '<h2>'.__('Import Blogware').'</h2>';
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
		echo '<p>'.__('Howdy! This importer allows you to extract posts from Blogware XML export file into your blog.  Pick a Blogware file to upload and click Import.').'</p>';
		wp_import_upload_form("admin.php?import=blogware&amp;step=1");
	}

	function import_posts() {
		global $wpdb, $current_user;
		
		set_magic_quotes_runtime(0);
		$importdata = file($this->file); // Read the file into an array
		$importdata = implode('', $importdata); // squish it
		$importdata = str_replace(array ("\r\n", "\r"), "\n", $importdata);

		preg_match_all('|(<item[^>]+>(.*?)</item>)|is', $importdata, $posts);
		$posts = $posts[1];
		unset($importdata);
		echo '<ol>';		
		foreach ($posts as $post) {
			flush();
			preg_match('|<item type=\"(.*?)\">|is', $post, $post_type);
			$post_type = $post_type[1];
			if($post_type == "photo") {
				preg_match('|<photoFilename>(.*?)</photoFilename>|is', $post, $post_title);
			} else {
				preg_match('|<title>(.*?)</title>|is', $post, $post_title);
			}
			$post_title = $wpdb->escape(trim($post_title[1]));

			preg_match('|<pubDate>(.*?)</pubDate>|is', $post, $post_date);
			$post_date = strtotime($post_date[1]);
			$post_date = gmdate('Y-m-d H:i:s', $post_date);

			preg_match_all('|<category>(.*?)</category>|is', $post, $categories);
			$categories = $categories[1];

			$cat_index = 0;
			foreach ($categories as $category) {
				$categories[$cat_index] = $wpdb->escape($this->unhtmlentities($category));
				$cat_index++;
			}

			if(strcasecmp($post_type, "photo") === 0) {
				preg_match('|<sizedPhotoUrl>(.*?)</sizedPhotoUrl>|is', $post, $post_content);
				$post_content = '<img src="'.trim($post_content[1]).'" />';
				$post_content = $this->unhtmlentities($post_content);
			} else {
				preg_match('|<body>(.*?)</body>|is', $post, $post_content);
				$post_content = str_replace(array ('<![CDATA[', ']]>'), '', trim($post_content[1]));
				$post_content = $this->unhtmlentities($post_content);
			}

			// Clean up content
			$post_content = preg_replace('|<(/?[A-Z]+)|e', "'<' . strtolower('$1')", $post_content);
			$post_content = str_replace('<br>', '<br />', $post_content);
			$post_content = str_replace('<hr>', '<hr />', $post_content);
			$post_content = $wpdb->escape($post_content);

			$post_author = $current_user->ID;
			preg_match('|<postStatus>(.*?)</postStatus>|is', $post, $post_status);
			$post_status = trim($post_status[1]);

			echo '<li>';
			if ($post_id = post_exists($post_title, $post_content, $post_date)) {
				printf(__('Post <i>%s</i> already exists.'), stripslashes($post_title));
			} else {
				printf(__('Importing post <i>%s</i>...'), stripslashes($post_title));
				$postdata = compact('post_author', 'post_date', 'post_content', 'post_title', 'post_status');
				$post_id = wp_insert_post($postdata);
				if (!$post_id) {
					_e("Couldn't get post ID");
					echo '</li>';
					break;
				}
				if(0 != count($categories))
					wp_create_categories($categories, $post_id);
			}

			preg_match_all('|<comment>(.*?)</comment>|is', $post, $comments);
			$comments = $comments[1];
			
			if ( $comments ) {
				$comment_post_ID = $post_id;
				$num_comments = 0;
				foreach ($comments as $comment) {
					preg_match('|<body>(.*?)</body>|is', $comment, $comment_content);
					$comment_content = str_replace(array ('<![CDATA[', ']]>'), '', trim($comment_content[1]));
					$comment_content = $this->unhtmlentities($comment_content);

					// Clean up content
					$comment_content = preg_replace('|<(/?[A-Z]+)|e', "'<' . strtolower('$1')", $comment_content);
					$comment_content = str_replace('<br>', '<br />', $comment_content);
					$comment_content = str_replace('<hr>', '<hr />', $comment_content);
					$comment_content = $wpdb->escape($comment_content);

					preg_match('|<pubDate>(.*?)</pubDate>|is', $comment, $comment_date);
					$comment_date = trim($comment_date[1]);
					$comment_date = date('Y-m-d H:i:s', strtotime($comment_date));

					preg_match('|<author>(.*?)</author>|is', $comment, $comment_author);
					$comment_author = $wpdb->escape(trim($comment_author[1]));

					$comment_author_email = NULL;

					$comment_approved = 1;
					// Check if it's already there
					if (!comment_exists($comment_author, $comment_date)) {
						$commentdata = compact('comment_post_ID', 'comment_author', 'comment_author_email', 'comment_date', 'comment_content', 'comment_approved');
						$commentdata = wp_filter_comment($commentdata);
						wp_insert_comment($commentdata);
						$num_comments++;
					}
				}
			}
			if ( $num_comments ) {
				echo ' ';
				printf(__('(%s comments)'), $num_comments);
			}
			echo '</li>';
			flush();
			ob_flush();
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

	function BW_Import() {
		// Nothing.	
	}
}

$blogware_import = new BW_Import();

register_importer('blogware', 'Blogware', __('Import posts from Blogware'), array ($blogware_import, 'dispatch'));
?>
