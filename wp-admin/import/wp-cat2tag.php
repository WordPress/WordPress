<?php
/**
 * WordPress Categories to Tags Converter.
 *
 * @package WordPress
 * @subpackage Importer
 */

/**
 * WordPress categories to tags converter class.
 *
 * Will convert WordPress categories to tags, removing the category after the
 * process is complete and updating all posts to switch to the tag.
 *
 * @since unknown
 */
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
			<div class="tablenav"><p style="margin:4px"><a style="display:inline;" class="button-secondary" href="admin.php?import=wp-cat2tag"><?php _e( "Categories to Tags" ); ?></a>
			<a style="display:inline;" class="button-secondary" href="admin.php?import=wp-cat2tag&amp;step=3"><?php _e( "Tags to Categories" ); ?></a></p></div>
<?php	}
	}

	function footer() {
		echo '</div>';
	}

	function populate_cats() {

		$categories = get_categories('get=all');
		foreach ( $categories as $category ) {
			$this->all_categories[] = $category;
			if ( is_term( $category->slug, 'post_tag' ) )
				$this->hybrids_ids[] = $category->term_id;
		}
	}

	function populate_tags() {

		$tags = get_terms( array('post_tag'), 'get=all' );
		foreach ( $tags as $tag ) {
			$this->all_tags[] = $tag;
			if ( is_term( $tag->slug, 'category' ) )
				$this->hybrids_ids[] = $tag->term_id;
		}
	}

	function categories_tab() {
		$this->populate_cats();
		$cat_num = count($this->all_categories);

		echo '<br class="clear" />';

		if ( $cat_num > 0 ) {
			screen_icon();
			echo '<h2>' . sprintf( __ngettext( 'Convert Category to Tag.', 'Convert Categories (%d) to Tags.', $cat_num ), $cat_num ) . '</h2>';
			echo '<div class="narrow">';
			echo '<p>' . __('Hey there. Here you can selectively convert existing categories to tags. To get started, check the categories you wish to be converted, then click the Convert button.') . '</p>';
			echo '<p>' . __('Keep in mind that if you convert a category with child categories, the children become top-level orphans.') . '</p></div>';

			$this->categories_form();
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
<p><input type="button" class="button-secondary" value="<?php _e('Check All'); ?>" onclick="this.value=check_all_rows()" />
<?php wp_nonce_field('import-cat2tag'); ?></p>
<ul style="list-style:none">

<?php	$hier = _get_term_hierarchy('category');

		foreach ($this->all_categories as $category) {
			$category = sanitize_term( $category, 'category', 'display' );

			if ( (int) $category->parent == 0 ) { ?>

	<li><label><input type="checkbox" name="cats_to_convert[]" value="<?php echo intval($category->term_id); ?>" /> <?php echo $category->name . ' (' . $category->count . ')'; ?></label><?php

				 if ( in_array( intval($category->term_id),  $this->hybrids_ids ) )
				 	echo ' <a href="#note"> * </a>';

				if ( isset($hier[$category->term_id]) )
					$this->_category_children($category, $hier); ?></li>
<?php		}
		} ?>
</ul>

<?php	if ( ! empty($this->hybrids_ids) )
			echo '<p><a name="note"></a>' . __('* This category is also a tag. Converting it will add that tag to all posts that are currently in the category.') . '</p>'; ?>

<p class="submit"><input type="submit" name="submit" class="button" value="<?php _e('Convert Categories to Tags'); ?>" /></p>
</form>

<?php }

	function tags_tab() {
		$this->populate_tags();
		$tags_num = count($this->all_tags);

		echo '<br class="clear" />';

		if ( $tags_num > 0 ) {
			screen_icon();
			echo '<h2>' . sprintf( __ngettext( 'Convert Tag to Category.', 'Convert Tags (%d) to Categories.', $tags_num ), $tags_num ) . '</h2>';
			echo '<div class="narrow">';
			echo '<p>' . __('Here you can selectively converts existing tags to categories. To get started, check the tags you wish to be converted, then click the Convert button.') . '</p>';
			echo '<p>' . __('The newly created categories will still be associated with the same posts.') . '</p></div>';

			$this->tags_form();
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
<p><input type="button" class="button-secondary" value="<?php _e('Check All'); ?>" onclick="this.value=check_all_tagrows()" />
<?php wp_nonce_field('import-cat2tag'); ?></p>
<ul style="list-style:none">

<?php	foreach ( $this->all_tags as $tag ) { ?>
	<li><label><input type="checkbox" name="tags_to_convert[]" value="<?php echo intval($tag->term_id); ?>" /> <?php echo attribute_escape($tag->name) . ' (' . $tag->count . ')'; ?></label><?php if ( in_array( intval($tag->term_id),  $this->hybrids_ids ) ) echo ' <a href="#note"> * </a>'; ?></li>

<?php	} ?>
</ul>

<?php	if ( ! empty($this->hybrids_ids) )
			echo '<p><a name="note"></a>' . __('* This tag is also a category. When converted, all posts associated with the tag will also be in the category.') . '</p>'; ?>

<p class="submit"><input type="submit" name="submit_tags" class="button" value="<?php _e('Convert Tags to Categories'); ?>" /></p>
</form>

<?php }

	function _category_children($parent, $hier) { ?>

		<ul style="list-style:none">
<?php	foreach ($hier[$parent->term_id] as $child_id) {
			$child =& get_category($child_id); ?>
		<li><label><input type="checkbox" name="cats_to_convert[]" value="<?php echo intval($child->term_id); ?>" /> <?php echo $child->name . ' (' . $child->count . ')'; ?></label><?php

			if ( in_array( intval($child->term_id), $this->hybrids_ids ) )
				echo ' <a href="#note"> * </a>';

			if ( isset($hier[$child->term_id]) )
				$this->_category_children($child, $hier); ?></li>
<?php	} ?>
		</ul><?php
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

		if ( (!isset($_POST['cats_to_convert']) || !is_array($_POST['cats_to_convert'])) && empty($this->categories_to_convert)) { ?>
			<div class="narrow">
			<p><?php printf(__('Uh, oh. Something didn&#8217;t work. Please <a href="%s">try again</a>.'), 'admin.php?import=wp-cat2tag'); ?></p>
			</div>
<?php		return;
		}

		if ( empty($this->categories_to_convert) )
			$this->categories_to_convert = $_POST['cats_to_convert'];

		$hier = _get_term_hierarchy('category');
		$hybrid_cats = $clear_parents = $parents = false;
		$clean_term_cache = $clean_cat_cache = array();
		$default_cat = get_option('default_category');

		echo '<ul>';

		foreach ( (array) $this->categories_to_convert as $cat_id) {
			$cat_id = (int) $cat_id;

			if ( ! $this->_category_exists($cat_id) ) {
				echo '<li>' . sprintf( __('Category %s doesn\'t exist!'),  $cat_id ) . "</li>\n";
			} else {
				$category =& get_category($cat_id);
				echo '<li>' . sprintf(__('Converting category <strong>%s</strong> ... '),  $category->name);

				// If the category is the default, leave category in place and create tag.
				if ( $default_cat == $category->term_id ) {

					if ( ! ($id = is_term( $category->slug, 'post_tag' ) ) )
						$id = wp_insert_term($category->name, 'post_tag', array('slug' => $category->slug));

					$id = $id['term_taxonomy_id'];
					$posts = get_objects_in_term($category->term_id, 'category');
					$term_order = 0;

					foreach ( $posts as $post ) {
						$values[] = $wpdb->prepare( "(%d, %d, %d)", $post, $id, $term_order);
						clean_post_cache($post);
					}

					if ( $values ) {
						$wpdb->query("INSERT INTO $wpdb->term_relationships (object_id, term_taxonomy_id, term_order) VALUES " . join(',', $values) . " ON DUPLICATE KEY UPDATE term_order = VALUES(term_order)");

						$wpdb->query( $wpdb->prepare("UPDATE $wpdb->term_taxonomy SET count = %d WHERE term_id = %d AND taxonomy = 'post_tag'", $category->count, $category->term_id) );
					}

					echo __('Converted successfully.') . "</li>\n";
					continue;
				}

				// if tag already exists, add it to all posts in the category
				if ( $tag_ttid = $wpdb->get_var( $wpdb->prepare("SELECT term_taxonomy_id FROM $wpdb->term_taxonomy WHERE term_id = %d AND taxonomy = 'post_tag'", $category->term_id) ) ) {
					$objects_ids = get_objects_in_term($category->term_id, 'category');
					$tag_ttid = (int) $tag_ttid;
					$term_order = 0;

					foreach ( $objects_ids as $object_id )
						$values[] = $wpdb->prepare( "(%d, %d, %d)", $object_id, $tag_ttid, $term_order);

					if ( $values ) {
						$wpdb->query("INSERT INTO $wpdb->term_relationships (object_id, term_taxonomy_id, term_order) VALUES " . join(',', $values) . " ON DUPLICATE KEY UPDATE term_order = VALUES(term_order)");

						$count = $wpdb->get_var( $wpdb->prepare("SELECT COUNT(*) FROM $wpdb->term_relationships WHERE term_taxonomy_id = %d", $tag_ttid) );
						$wpdb->query( $wpdb->prepare("UPDATE $wpdb->term_taxonomy SET count = %d WHERE term_id = %d AND taxonomy = 'post_tag'", $count, $category->term_id) );
					}
					echo __('Tag added to all posts in this category.') . " *</li>\n";

					$hybrid_cats = true;
					$clean_term_cache[] = $category->term_id;
					$clean_cat_cache[] = $category->term_id;

					continue;
				}

				$tt_ids = $wpdb->get_col( $wpdb->prepare("SELECT term_taxonomy_id FROM $wpdb->term_taxonomy WHERE term_id = %d AND taxonomy = 'category'", $category->term_id) );
				if ( $tt_ids ) {
					$posts = $wpdb->get_col("SELECT object_id FROM $wpdb->term_relationships WHERE term_taxonomy_id IN (" . join(',', $tt_ids) . ") GROUP BY object_id");
					foreach ( (array) $posts as $post )
						clean_post_cache($post);
				}

				// Change the category to a tag.
				$wpdb->query( $wpdb->prepare("UPDATE $wpdb->term_taxonomy SET taxonomy = 'post_tag' WHERE term_id = %d AND taxonomy = 'category'", $category->term_id) );

				// Set all parents to 0 (root-level) if their parent was the converted tag
				$parents = $wpdb->query( $wpdb->prepare("UPDATE $wpdb->term_taxonomy SET parent = 0 WHERE parent = %d AND taxonomy = 'category'", $category->term_id) );

				if ( $parents ) $clear_parents = true;
				$clean_cat_cache[] = $category->term_id;
				echo __('Converted successfully.') . "</li>\n";
			}
		}
		echo '</ul>';

		if ( ! empty($clean_term_cache) ) {
			$clean_term_cache = array_unique(array_values($clean_term_cache));
			foreach ( $clean_term_cache as $id )
				wp_cache_delete($id, 'post_tag');
		}

		if ( ! empty($clean_cat_cache) ) {
			$clean_cat_cache = array_unique(array_values($clean_cat_cache));
			foreach ( $clean_cat_cache as $id )
				wp_cache_delete($id, 'category');
		}

		if ( $clear_parents ) delete_option('category_children');

		if ( $hybrid_cats )
			echo '<p>' . sprintf( __('* This category is also a tag. The converter has added that tag to all posts currently in the category. If you want to remove it, please confirm that all tags were added successfully, then delete it from the <a href="%s">Manage Categories</a> page.'), 'categories.php') . '</p>';
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

		if ( empty($this->tags_to_convert) )
			$this->tags_to_convert = $_POST['tags_to_convert'];

		$hybrid_tags = $clear_parents = false;
		$clean_cat_cache = $clean_term_cache = array();
		$default_cat = get_option('default_category');
		echo '<ul>';

		foreach ( (array) $this->tags_to_convert as $tag_id) {
			$tag_id = (int) $tag_id;

			if ( $tag = get_term( $tag_id, 'post_tag' ) ) {
				printf('<li>' . __('Converting tag <strong>%s</strong> ... '),  $tag->name);

				if ( $cat_ttid = $wpdb->get_var( $wpdb->prepare("SELECT term_taxonomy_id FROM $wpdb->term_taxonomy WHERE term_id = %d AND taxonomy = 'category'", $tag->term_id) ) ) {
					$objects_ids = get_objects_in_term($tag->term_id, 'post_tag');
					$cat_ttid = (int) $cat_ttid;
					$term_order = 0;

					foreach ( $objects_ids as $object_id ) {
						$values[] = $wpdb->prepare( "(%d, %d, %d)", $object_id, $cat_ttid, $term_order);
						clean_post_cache($object_id);
					}

					if ( $values ) {
						$wpdb->query("INSERT INTO $wpdb->term_relationships (object_id, term_taxonomy_id, term_order) VALUES " . join(',', $values) . " ON DUPLICATE KEY UPDATE term_order = VALUES(term_order)");

						if ( $default_cat != $tag->term_id ) {
							$count = $wpdb->get_var( $wpdb->prepare("SELECT COUNT(*) FROM $wpdb->term_relationships WHERE term_taxonomy_id = %d", $tag->term_id) );
							$wpdb->query( $wpdb->prepare("UPDATE $wpdb->term_taxonomy SET count = %d WHERE term_id = %d AND taxonomy = 'category'", $count, $tag->term_id) );
						}
					}

					$hybrid_tags = true;
					$clean_term_cache[] = $tag->term_id;
					$clean_cat_cache[] = $tag->term_id;
					echo __('All posts were added to the category with the same name.') . " *</li>\n";

					continue;
				}

				// Change the tag to a category.
				$parent = $wpdb->get_var( $wpdb->prepare("SELECT parent FROM $wpdb->term_taxonomy WHERE term_id = %d AND taxonomy = 'post_tag'", $tag->term_id) );
				if ( 0 == $parent || (0 < (int) $parent && $this->_category_exists($parent)) ) {
					$reset_parent = '';
					$clear_parents = true;
				} else
					$reset_parent = ", parent = '0'";

				$wpdb->query( $wpdb->prepare("UPDATE $wpdb->term_taxonomy SET taxonomy = 'category' $reset_parent WHERE term_id = %d AND taxonomy = 'post_tag'", $tag->term_id) );

				$clean_term_cache[] = $tag->term_id;
				$clean_cat_cache[] = $cat['term_id'];
				echo __('Converted successfully.') . "</li>\n";

			} else {
				printf( '<li>' . __('Tag #%s doesn\'t exist!') . "</li>\n",  $tag_id );
			}
		}

		if ( ! empty($clean_term_cache) ) {
			$clean_term_cache = array_unique(array_values($clean_term_cache));
			foreach ( $clean_term_cache as $id )
				wp_cache_delete($id, 'post_tag');
		}

		if ( ! empty($clean_cat_cache) ) {
			$clean_cat_cache = array_unique(array_values($clean_cat_cache));
			foreach ( $clean_cat_cache as $id )
				wp_cache_delete($id, 'category');
		}

		if ( $clear_parents ) delete_option('category_children');

		echo '</ul>';
		if ( $hybrid_tags )
			echo '<p>' . sprintf( __('* This tag is also a category. The converter has added all posts from it to the category. If you want to remove it, please confirm that all posts were added successfully, then delete it from the <a href="%s">Manage Tags</a> page.'), 'edit-tags.php') . '</p>';
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
