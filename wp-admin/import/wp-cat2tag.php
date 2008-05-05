<?php

class WP_Categories_to_Tags {
	var $categories_to_convert = array();
	var $all_categories = array();
	var $tags_to_convert = array();
	var $all_tags = array();
	var $hybrids_ids = array();

	function header() {
		echo '<div class="wrap">';
		if ( ! current_user_can('manage_categories') ) {
			echo '<div class="narrow">';
			echo '<p>' . __('Cheatin&#8217; uh?') . '</p>';
			echo '</div>';
		} else { ?>
			<div class="tablenav"><p style="margin:4px"><a style="display:inline;" class="button-secondary" href="admin.php?import=wp-cat2tag">Categories to Tags</a>
			<a style="display:inline;" class="button-secondary" href="admin.php?import=wp-cat2tag&amp;step=3">Tags to Categories</a></p></div>
<?php	}
	}

	function footer() {
		echo '</div>';
	}

	function populate_cats() {
		global $wpdb;

		$categories = get_categories('get=all');
		foreach ( $categories as $category ) {
			$this->all_categories[] = $category;
			if ( tag_exists( $wpdb->escape($category->name) ) )
				$this->hybrids_ids[] = $category->term_id;
		}
	}

	function populate_tags() {
		global $wpdb;

		$tags = get_terms( array('post_tag'), 'get=all' );
		foreach ( $tags as $tag ) {
			$this->all_tags[] = $tag;
			if ( $this->_category_exists($tag->term_id) )
				$this->hybrids_ids[] = $tag->term_id;
		}
	}

	function categories_tab() {
		$this->populate_cats();
		$cat_num = count($this->all_categories);

		echo '<br class="clear" />';

		if ( $cat_num > 0 ) {
			echo '<h2>Convert Categories (' . $cat_num . ') to Tags.</h2>';
			echo '<div class="narrow">';
			echo '<p>' . __('Hey there. Here you can selectively converts existing categories to tags. To get started, check the categories you wish to be converted, then click the Convert button.') . '</p>';
			echo '<p>' . __('Keep in mind that if you convert a category with child categories, the children become top-level orphans.') . '</p></div>';

			$this->categories_form();
		} elseif ( $hyb_num > 0 ) {
			 echo '<p>' . __('You have no categories that can be converted. However some of your categories are both a tag and a category.') . '</p>';
		} else {
			echo '<p>'.__('You have no categories to convert!').'</p>';
		}
	}

	function categories_form() { ?>

<script type="text/javascript">
/* <![CDATA[ */
var checkflag = "false";
function check_all_rows() {
	field = document.catlist;
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
/* ]]> */
</script>

<form name="catlist" id="catlist" action="admin.php?import=wp-cat2tag&amp;step=2" method="post">
<p><input type="button" class="button-secondary" value="<?php _e('Check All'); ?>" onclick="this.value=check_all_rows()">
<?php wp_nonce_field('import-cat2tag'); ?></p>
<ul style="list-style:none">
<?php
		$hier = _get_term_hierarchy('category');

		foreach ($this->all_categories as $category) {
			$category = sanitize_term( $category, 'category', 'display' );

			if ( (int) $category->parent == 0 ) {
				if ( in_array( intval($category->term_id),  $this->hybrids_ids ) ) {
?>
	<li style="color:#777;padding-left:1.3em;"><?php echo $category->name . ' (' . $category->count . ') *'; ?></li>
<?php
				} else {
?>
	<li><label><input type="checkbox" name="cats_to_convert[]" value="<?php echo intval($category->term_id); ?>" /> <?php echo $category->name . ' (' . $category->count . ')'; ?></label><?php

					if ( isset($hier[$category->term_id]) )
						$this->_category_children($category, $hier);
?></li>
<?php
				}
			}
		}
?>
</ul>

<?php
		if ( ! empty($this->hybrids_ids) ) {
			echo '<p>' . __('* This category is also a tag. It cannot be convert again.') . '</p>';
		}
?>
<p class="submit"><input type="submit" name="submit" class="button" value="<?php _e('Convert Categories to Tags'); ?>" /></p>
</form>

<?php
	}

	function tags_tab() {
		$this->populate_tags();
		$tags_num = count($this->all_tags);

		echo '<br class="clear" />';

		if ( $tags_num > 0 ) {
			echo '<h2>Convert Tags (' . $tags_num . ') to Categories.</h2>';
			echo '<div class="narrow">';
			echo '<p>' . __('Here you can selectively converts existing tags to categories. To get started, check the tags you wish to be converted, then click the Convert button.') . '</p>';
			echo '<p>' . __('The newly created categories will still be associated with the same posts.') . '</p></div>';
			
			
			$this->tags_form();
		} elseif ( $hyb_num > 0 ) {
			 echo '<p>' . __('You have no tags that can be converted. However some of your tags are both a tag and a category.') . '</p>';
		} else {
			echo '<p>'.__('You have no tags to convert!').'</p>';
		}
	}

	function tags_form() { ?>

<script type="text/javascript">
/* <![CDATA[ */
var checktags = "false";
function check_all_tagrows() {
	field = document.taglist;
	if ( 'false' == checktags ) {
		for ( i = 0; i < field.length; i++ ) {
			if ( 'tags_to_convert[]' == field[i].name )
				field[i].checked = true;
		}
		checktags = 'true';
		return '<?php _e('Uncheck All') ?>';
	} else {
		for ( i = 0; i < field.length; i++ ) {
			if ( 'tags_to_convert[]' == field[i].name )
				field[i].checked = false;
		}
		checktags = 'false';
		return '<?php _e('Check All') ?>';
	}
}
/* ]]> */
</script>

<form name="taglist" id="taglist" action="admin.php?import=wp-cat2tag&amp;step=4" method="post">
<p><input type="button" class="button-secondary" value="<?php _e('Check All'); ?>" onclick="this.value=check_all_tagrows()">
<?php wp_nonce_field('import-cat2tag'); ?></p>
<ul style="list-style:none">
<?php
		foreach ( $this->all_tags as $tag ) {
			if ( in_array( intval($tag->term_id),  $this->hybrids_ids ) ) {
?>
	<li style="color:#777;padding-left:1.3em;"><?php echo attribute_escape($tag->name) . ' (' . $tag->count . ') *'; ?></li>
<?php
			} else {
?>
	<li><label><input type="checkbox" name="tags_to_convert[]" value="<?php echo intval($tag->term_id); ?>" /> <?php echo attribute_escape($tag->name) . ' (' . $tag->count . ')'; ?></label></li>

<?php
			}
		}
?>
</ul>

<?php 	
		if ( ! empty($this->hybrids_ids) )
			echo '<p>' . __('* This tag is also a category. It cannot be converted again.') . '</p>';
?>

<p class="submit"><input type="submit" name="submit_tags" class="button" value="<?php _e('Convert Tags to Categories'); ?>" /></p>
</form>

<?php
	}

	function _category_children($parent, $hier) {
?>

		<ul style="list-style:none">
<?php
		foreach ($hier[$parent->term_id] as $child_id) {
			$child =& get_category($child_id);
			
			if ( in_array( intval($child->term_id), $this->hybrids_ids ) ) {
?>
		<li style="color:#777;padding-left:1.3em;"><?php echo $child->name . ' (' . $child->count . ') *'; ?></li>
<?php
			} else {
?>
		<li><label><input type="checkbox" name="cats_to_convert[]" value="<?php echo intval($child->term_id); ?>" /> <?php echo $child->name . ' (' . $child->count . ')'; ?></label>
<?php

			if ( isset($hier[$child->term_id]) )
				$this->_category_children($child, $hier);
?>		</li>
<?php
			}
		}
?>
		</ul>
<?php
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

	function convert_categories() {
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
						if ( !$wpdb->get_var( $wpdb->prepare("SELECT object_id FROM $wpdb->term_relationships WHERE object_id = %d AND term_taxonomy_id = %d", $post, $id) ) )
							$wpdb->query( $wpdb->prepare("INSERT INTO $wpdb->term_relationships (object_id, term_taxonomy_id) VALUES (%d, %d)", $post, $id) );
						clean_post_cache($post);
					}
				} else {
					$tt_ids = $wpdb->get_col( $wpdb->prepare("SELECT term_taxonomy_id FROM $wpdb->term_taxonomy WHERE term_id = %d AND taxonomy = 'category'", $category->term_id) );
					if ( $tt_ids ) {
						$posts = $wpdb->get_col("SELECT object_id FROM $wpdb->term_relationships WHERE term_taxonomy_id IN (" . join(',', $tt_ids) . ") GROUP BY object_id");
						foreach ( (array) $posts as $post )
							clean_post_cache($post);
					}

					// Change the category to a tag.
					$wpdb->query( $wpdb->prepare("UPDATE $wpdb->term_taxonomy SET taxonomy = 'post_tag' WHERE term_id = %d AND taxonomy = 'category'", $category->term_id) );

					$terms = $wpdb->get_col( $wpdb->prepare("SELECT term_id FROM $wpdb->term_taxonomy WHERE parent = %d AND taxonomy = 'category'", $category->term_id) );
					foreach ( (array) $terms as $term )
						clean_category_cache($term);

					// Set all parents to 0 (root-level) if their parent was the converted tag
					$wpdb->query( $wpdb->prepare("UPDATE $wpdb->term_taxonomy SET parent = 0 WHERE parent = %d AND taxonomy = 'category'", $category->term_id) );
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

	function convert_tags() {
		global $wpdb;

		if ( (!isset($_POST['tags_to_convert']) || !is_array($_POST['tags_to_convert'])) && empty($this->tags_to_convert)) {
			echo '<div class="narrow">';
			echo '<p>' . sprintf(__('Uh, oh. Something didn&#8217;t work. Please <a href="%s">try again</a>.'), 'admin.php?import=wp-cat2tag&amp;step=3') . '</p>';
			echo '</div>';
			return;
		}

		if ( empty($this->categories_to_convert) )
			$this->tags_to_convert = $_POST['tags_to_convert'];

		$clean_cache = array();
		echo '<ul>';

		foreach ( (array) $this->tags_to_convert as $tag_id) {
			$tag_id = (int) $tag_id;

			echo '<li>' . sprintf(__('Converting tag #%s ... '),  $tag_id);

			if ( ! is_term($tag_id, 'post_tag') ) {
				_e('Tag doesn\'t exist!');
			} else {

				if ( is_term($tag_id, 'category') ) {
					_e('This Tag is already a Category.');
					echo '</li>';
					continue;
				}

				$tt_ids = $wpdb->get_col( $wpdb->prepare("SELECT term_taxonomy_id FROM $wpdb->term_taxonomy WHERE term_id = %d AND taxonomy = 'post_tag'", $tag_id) );
				if ( $tt_ids ) {
						$posts = $wpdb->get_col("SELECT object_id FROM $wpdb->term_relationships WHERE term_taxonomy_id IN (" . join(',', $tt_ids) . ") GROUP BY object_id");
					foreach ( (array) $posts as $post )
						clean_post_cache($post);
				}

				// Change the tag to a category.
				$parent = $wpdb->get_var( $wpdb->prepare("SELECT parent FROM $wpdb->term_taxonomy WHERE term_id = %d AND taxonomy = 'post_tag'", $tag_id) );
				if ( 0 == $parent || (0 < (int) $parent && $this->_category_exists($parent)) )
					$reset_parent = '';
				else $reset_parent = ", parent = '0'";

				$wpdb->query( $wpdb->prepare("UPDATE $wpdb->term_taxonomy SET taxonomy = 'category' $reset_parent WHERE term_id = %d AND taxonomy = 'post_tag'", $tag_id) );

				// Clean the cache
				$clean_cache[] = $tag_id;

				_e('Converted successfully.');
			}

			echo '</li>';
		}

		clean_term_cache( $clean_cache, 'post_tag' );
		delete_option('category_children');
		
		echo '</ul>';
		echo '<p>' . sprintf( __('We&#8217;re all done here, but you can always <a href="%s">convert more</a>.'), 'admin.php?import=wp-cat2tag&amp;step=3' ) . '</p>';
	}

	function init() {

		$step = (isset($_GET['step'])) ? (int) $_GET['step'] : 1;

		$this->header();

		if ( current_user_can('manage_categories') ) {

			switch ($step) {
				case 1 :
					$this->categories_tab();
				break;

				case 2 :
					check_admin_referer('import-cat2tag');
					$this->convert_categories();
				break;
				
				case 3 :
					$this->tags_tab();
				break;
				
				case 4 :
					check_admin_referer('import-cat2tag');
					$this->convert_tags();
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

register_importer('wp-cat2tag', __('Categories and Tags Converter'), __('Convert existing categories to tags or tags to categories, selectively.'), array(&$wp_cat2tag_importer, 'init'));

?>