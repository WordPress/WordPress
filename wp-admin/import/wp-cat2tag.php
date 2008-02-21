<?php

class WP_Categories_to_Tags {
	var $categories_to_convert = array();
	var $all_categories = array();

	function header() {
		echo '<div class="wrap">';
		echo '<h2>' . __('Convert Categories to Tags') . '</h2>';
	}

	function footer() {
		echo '</div>';
	}

	function populate_all_categories() {
		global $wpdb;

		$categories = get_categories('get=all');
		foreach ( $categories as $category ) {
			if ( !tag_exists($wpdb->escape($category->name)) )
				$this->all_categories[] = $category;
		}
	}

	function welcome() {
		$this->populate_all_categories();

		echo '<div class="narrow">';

		if (count($this->all_categories) > 0) {
			echo '<p>' . __('Hey there. Here you can selectively converts existing categories to tags. To get started, check the categories you wish to be converted, then click the Convert button.') . '</p>';
			echo '<p>' . __('Keep in mind that if you convert a category with child categories, the children become top-level orphans.') . '</p>';

			$this->categories_form();
		} else {
			echo '<p>'.__('You have no categories to convert!').'</p>';
		}

		echo '</div>';
	}

	function categories_form() {
?>
<script type="text/javascript">
<!--
var checkflag = "false";
function check_all_rows() {
	field = document.formlist;
	if ( 'false' == checkflag ) {
		for ( i = 0; i < field.length; i++ ) {
			if ( 'cats_to_convert[]' == field[i].name )
				field[i].checked = true;
		}
		checkflag = 'true';
		return '<?php _e('Uncheck All') ?>';
	} else {
		for ( i = 0; i < field.length; i++ ) {
			if ( 'cats_to_convert[]' == field[i].name )
				field[i].checked = false;
		}
		checkflag = 'false';
		return '<?php _e('Check All') ?>';
	}
}

//  -->
</script>
<?php
		echo '<form name="formlist" id="formlist" action="admin.php?import=wp-cat2tag&amp;step=2" method="post">
		<p><input type="button" class="button-secondary" value="' . __('Check All') . '"' . ' onClick="this.value=check_all_rows()"></p>';
		wp_nonce_field('import-cat2tag');
		echo '<ul style="list-style:none">';

		$hier = _get_term_hierarchy('category');

		foreach ($this->all_categories as $category) {
			$category = sanitize_term( $category, 'category', 'display' );

			if ((int) $category->parent == 0) {
				echo '<li><label><input type="checkbox" name="cats_to_convert[]" value="' . intval($category->term_id) . '" /> ' . $category->name . ' (' . $category->count . ')</label>';

				if (isset($hier[$category->term_id])) {
					$this->_category_children($category, $hier);
				}

				echo '</li>';
			}
		}

		echo '</ul>';

		echo '<p class="submit"><input type="submit" name="submit" class="button" value="' . __('Convert Tags') . '" /></p>';

		echo '</form>';
	}

	function _category_children($parent, $hier) {
		echo '<ul style="list-style:none">';

		foreach ($hier[$parent->term_id] as $child_id) {
			$child =& get_category($child_id);

			echo '<li><label><input type="checkbox" name="cats_to_convert[]" value="' . intval($child->term_id) . '" /> ' . $child->name . ' (' . $child->count . ')</label>';

			if (isset($hier[$child->term_id])) {
				$this->_category_children($child, $hier);
			}

			echo '</li>';
		}

		echo '</ul>';
	}

	function _category_exists($cat_id) {
		$cat_id = (int) $cat_id;

		$maybe_exists = category_exists($cat_id);

		if ( $maybe_exists ) {
			return true;
		} else {
			return false;
		}
	}

	function convert_them() {
		global $wpdb;

		if ( (!isset($_POST['cats_to_convert']) || !is_array($_POST['cats_to_convert'])) && empty($this->categories_to_convert)) {
			echo '<div class="narrow">';
			echo '<p>' . sprintf(__('Uh, oh. Something didn&#8217;t work. Please <a href="%s">try again</a>.'), 'admin.php?import=wp-cat2tag') . '</p>';
			echo '</div>';
			return;
		}


		if ( empty($this->categories_to_convert) )
			$this->categories_to_convert = $_POST['cats_to_convert'];
		$hier = _get_term_hierarchy('category');

		echo '<ul>';

		foreach ( (array) $this->categories_to_convert as $cat_id) {
			$cat_id = (int) $cat_id;

			echo '<li>' . sprintf(__('Converting category #%s ... '),  $cat_id);

			if (!$this->_category_exists($cat_id)) {
				_e('Category doesn\'t exist!');
			} else {
				$category =& get_category($cat_id);

				if ( tag_exists($wpdb->escape($category->name)) ) {
					_e('Category is already a tag.');
					echo '</li>';
					continue;
				}

				// If the category is the default, leave category in place and create tag.
				if ( get_option('default_category') == $category->term_id ) {
					$id = wp_insert_term($category->name, 'post_tag', array('slug' => $category->slug));
					$id = $id['term_taxonomy_id'];
					$posts = get_objects_in_term($category->term_id, 'category');
					foreach ( $posts as $post ) {
						if ( !$wpdb->get_var("SELECT object_id FROM $wpdb->term_relationships WHERE object_id = '$post' AND term_taxonomy_id = '$id'") )
							$wpdb->query("INSERT INTO $wpdb->term_relationships (object_id, term_taxonomy_id) VALUES ('$post', '$id')");
						clean_post_cache($post);
					}
				} else {
					$tt_ids = $wpdb->get_col("SELECT term_taxonomy_id FROM $wpdb->term_taxonomy WHERE term_id = '{$category->term_id}' AND taxonomy = 'category'");
					if ( $tt_ids ) {
						$posts = $wpdb->get_col("SELECT object_id FROM $wpdb->term_relationships WHERE term_taxonomy_id IN (" . join(',', $tt_ids) . ") GROUP BY object_id");
						foreach ( (array) $posts as $post )
							clean_post_cache($post);
					}

					// Change the category to a tag.
					$wpdb->query("UPDATE $wpdb->term_taxonomy SET taxonomy = 'post_tag' WHERE term_id = '{$category->term_id}' AND taxonomy = 'category'");

					$terms = $wpdb->get_col("SELECT term_id FROM $wpdb->term_taxonomy WHERE parent = '{$category->term_id}' AND taxonomy = 'category'");
					foreach ( (array) $terms as $term )
						clean_category_cache($term);

					// Set all parents to 0 (root-level) if their parent was the converted tag
					$wpdb->query("UPDATE $wpdb->term_taxonomy SET parent = 0 WHERE parent = '{$category->term_id}' AND taxonomy = 'category'");
				}
				// Clean the cache
				clean_category_cache($category->term_id);

				_e('Converted successfully.');
			}

			echo '</li>';
		}

		echo '</ul>';
		echo '<p>' . sprintf( __('We&#8217;re all done here, but you can always <a href="%s">convert more</a>.'), 'admin.php?import=wp-cat2tag' ) . '</p>';
	}

	function init() {

		$step = (isset($_GET['step'])) ? (int) $_GET['step'] : 1;

		$this->header();

		if (!current_user_can('manage_categories')) {
			echo '<div class="narrow">';
			echo '<p>' . __('Cheatin&#8217; uh?') . '</p>';
			echo '</div>';
		} else {
			if ( $step > 1 )
				check_admin_referer('import-cat2tag');

			switch ($step) {
				case 1 :
					$this->welcome();
				break;

				case 2 :
					$this->convert_them();
				break;
			}
		}

		$this->footer();
	}

	function WP_Categories_to_Tags() {
		// Do nothing.
	}
}

$wp_cat2tag_importer = new WP_Categories_to_Tags();

register_importer('wp-cat2tag', __('Categories to Tags Converter'), __('Convert existing categories to tags, selectively.'), array(&$wp_cat2tag_importer, 'init'));

?>