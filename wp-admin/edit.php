<?php
require_once('admin.php');

$title = __('Posts');
$parent_file = 'edit.php';
require_once('admin-header.php');

$_GET['m'] = (int) $_GET['m'];

get_currentuserinfo();

$drafts = $wpdb->get_results("SELECT ID, post_title FROM $wpdb->posts WHERE post_status = 'draft' AND post_author = $user_ID");
if (1 < $user_level) {
	$editable = $wpdb->get_col("SELECT ID FROM $wpdb->users WHERE user_level <= '$user_level' AND ID != $user_ID");
	if( is_array( $editable ) == false )
			$other_drafts = '';
	else {
		$editable = join(',', $editable);
		$other_drafts = $wpdb->get_results("SELECT ID, post_title FROM $wpdb->posts WHERE post_status = 'draft' AND post_author IN ($editable) ");
	}
} else {
	$other_drafts = false;
}

if ($drafts || $other_drafts) {
?> 
<div class="wrap">
<?php if ($drafts) { ?>
    <p><strong><?php _e('Your Drafts:') ?></strong> 
    <?php
	$i = 0;
	foreach ($drafts as $draft) {
		if (0 != $i)
			echo ', ';
		$draft->post_title = stripslashes($draft->post_title);
		if ($draft->post_title == '')
			$draft->post_title = sprintf(__('Post #%s'), $draft->ID);
		echo "<a href='post.php?action=edit&amp;post=$draft->ID' title='" . __('Edit this draft') . "'>$draft->post_title</a>";
		++$i;
		}
	?> 
    .</p> 
<?php } ?>

<?php if ($other_drafts) { ?> 
    <p><strong><?php _e('Other&#8217;s Drafts:') ?></strong> 
    <?php
	$i = 0;
	foreach ($other_drafts as $draft) {
		if (0 != $i)
			echo ', ';
		$draft->post_title = stripslashes($draft->post_title);
		if ($draft->post_title == '')
			$draft->post_title = sprintf(__('Post #%s'), $draft->ID);
		echo "<a href='post.php?action=edit&amp;post=$draft->ID' title='" . __('Edit this draft') . "'>$draft->post_title</a>";
		++$i;
		}
	?> 
    .</p> 

<?php } ?>

</div>
<?php } ?>

<div class="wrap">
<h2>
<?php
if ( $_GET['m'] ) {
	echo $month[substr( $_GET['m'], 4, 2 )] . ' ' . substr( $_GET['m'], 0, 4 );
} elseif ( isset( $_GET['s'] ) ) {
	printf(__('Search for &#8220;%s&#8221;'), wp_specialchars($_GET['s']) );
} else {
	_e('Last 15 Posts');
}
?>
</h2>

<form name="searchform" action="" method="get" style="float: left; width: 16em; margin-right: 3em;"> 
  <fieldset> 
  <legend><?php _e('Search Posts&hellip;') ?></legend> 
  <input type="text" name="s" value="<?php if (isset($s)) echo $s; ?>" size="17" /> 
  <input type="submit" name="submit" value="<?php _e('Search') ?>"  /> 
  </fieldset>
</form>

<form name="viewarc" action="" method="get" style="float: left; width: 20em; margin-bottom: 1em;">
	<fieldset>
	<legend><?php _e('Browse Month&hellip;') ?></legend>
    <select name='m'>
	<?php
		$arc_result=$wpdb->get_results("SELECT DISTINCT YEAR(post_date) AS yyear, MONTH(post_date) AS mmonth FROM $wpdb->posts ORDER BY post_date DESC");
		foreach ($arc_result as $arc_row) {			
			$arc_year  = $arc_row->yyear;
			$arc_month = $arc_row->mmonth;
			
			if( isset($_GET['m']) && $arc_year . zeroise($arc_month, 2) == (int) $_GET['m'] )
				$default = 'selected="selected"';
			else
				$default = null;
			
			echo "<option $default value=\"" . $arc_year.zeroise($arc_month, 2) . '">';
			echo $month[zeroise($arc_month, 2)] . " $arc_year";
			echo "</option>\n";
		}
	?>
	</select>
		<input type="submit" name="submit" value="<?php _e('Show Month') ?>"  /> 
	</fieldset>
</form>

<br style="clear:both;" />

<?php

// define the columns to display, the syntax is 'internal name' => 'display name'
$posts_columns = array(
  'id'         => __('ID'),
  'date'       => __('When'),
  'title'      => __('Title'),
  'categories' => __('Categories'),
  'comments'   => __('Comments'),
  'author'     => __('Author')
);
$posts_columns = apply_filters('manage_posts_columns', $posts_columns);

// you can not edit these at the moment
$posts_columns['control_view']   = '';
$posts_columns['control_edit']   = '';
$posts_columns['control_delete'] = '';

?>

<table width="100%" cellpadding="3" cellspacing="3"> 
	<tr>

<?php foreach($posts_columns as $column_display_name) { ?>
	<th scope="col"><?php echo $column_display_name; ?></th>
<?php } ?>

	</tr>
<?php
$what_to_show = 'posts';
if ( empty($_GET['m']) || 0 == $_GET['m'] && empty($_GET['s']) ) {
  $showposts = 15;
} else {
  $nopaging = true;
}

include(ABSPATH.'wp-blog-header.php');

if ($posts) {
$bgcolor = '';
foreach ($posts as $post) { start_wp();
$class = ('alternate' == $class) ? '' : 'alternate';
?> 
	<tr class='<?php echo $class; ?>'>

<?php

foreach($posts_columns as $column_name=>$column_display_name) {

	switch($column_name) {
	
	case 'id':
		?>
		<th scope="row"><?php echo $id ?></th>
		<?php
		break;

	case 'date':
		?>
		<td><?php the_time('Y-m-d \<\b\r \/\> g:i:s a'); ?></td>
		<?php
		break;
	case 'title':
		?>
		<td><?php the_title() ?>
		<?php if ('private' == $post->post_status) _e(' - <strong>Private</strong>'); ?></td>
		<?php
		break;

	case 'categories':
		?>
		<td><?php the_category(','); ?></td>
		<?php
		break;

	case 'comments':
		?>
		<td><a href="edit.php?p=<?php echo $id ?>&amp;c=1"> 
      <?php comments_number(__('0'), __('1'), __('%')) ?> 
      </a></td>
		<?php
		break;

	case 'author':
		?>
		<td><?php the_author() ?></td>
		<?php
		break;

	case 'control_view':
		?>
		<td><a href="<?php the_permalink(); ?>" rel="permalink" class="edit"><?php _e('View'); ?></a></td>
		<?php
		break;

	case 'control_edit':
		?>
		<td><?php if ( user_can_edit_user($user_level,$authordata->user_level) ) { echo "<a href='post.php?action=edit&amp;post=$id' class='edit'>" . __('Edit') . "</a>"; } ?></td>
		<?php
		break;

	case 'control_delete':
		?>
		<td><?php if ( user_can_edit_user($user_level,$authordata->user_level) ) { echo "<a href='post.php?action=delete&amp;post=$id' class='delete' onclick=\"return confirm('" . sprintf(__("You are about to delete this post \'%s\'\\n  \'OK\' to delete, \'Cancel\' to stop."), wp_specialchars(get_the_title('', ''), 1) ) . "')\">" . __('Delete') . "</a>"; } ?></td>
		<?php
		break;

	default:
		?>
		<td><?php do_action('manage_posts_custom_column', $column_name, $id); ?></td>
		<?php
		break;
	}
}
?>
	</tr> 
<?php
}
} else {
?>
  <tr style='background-color: <?php echo $bgcolor; ?>'> 
    <td colspan="8"><?php _e('No posts found.') ?></td> 
  </tr> 
<?php
} // end if ($posts)
?> 
</table> 
<?php
if ( 1 == count($posts) ) {

	$comments = $wpdb->get_results("SELECT * FROM $wpdb->comments WHERE comment_post_ID = $id AND comment_approved != 'spam' ORDER BY comment_date");
	if ($comments) {
	?> 
<h3><?php _e('Comments') ?></h3> 
<ol id="comments"> 
<?php
foreach ($comments as $comment) {
$comment_status = wp_get_comment_status($comment->comment_ID);
?> 

<li <?php if ("unapproved" == $comment_status) echo "class='unapproved'"; ?> >
  <?php comment_date('Y-n-j') ?> 
  @
  <?php comment_time('g:m:s a') ?> 
  <?php 
			if (($user_level > $authordata->user_level) or ($user_login == $authordata->user_login)) {
				echo "[ <a href=\"post.php?action=editcomment&amp;comment=".$comment->comment_ID."\">" .  __('Edit') . "</a>";
				echo " - <a href=\"post.php?action=deletecomment&amp;p=".$post->ID."&amp;comment=".$comment->comment_ID."\" onclick=\"return confirm('" . sprintf(__("You are about to delete this comment by \'%s\'\\n  \'OK\' to delete, \'Cancel\' to stop."), $comment->comment_author) . "')\">" . __('Delete') . "</a> ";
				if ( ('none' != $comment_status) && ($user_level >= 3) ) {
					if ('approved' == wp_get_comment_status($comment->comment_ID)) {
						echo " - <a href=\"post.php?action=unapprovecomment&amp;p=".$post->ID."&amp;comment=".$comment->comment_ID."\">" . __('Unapprove') . "</a> ";
					} else {
						echo " - <a href=\"post.php?action=approvecomment&amp;p=".$post->ID."&amp;comment=".$comment->comment_ID."\">" . __('Approve') . "</a> ";
					}
				}
				echo "]";
			} // end if any comments to show
			?> 
  <br /> 
  <strong> 
  <?php comment_author() ?> 
  (
  <?php comment_author_email_link() ?> 
  /
  <?php comment_author_url_link() ?> 
  )</strong> (IP:
  <?php comment_author_IP() ?> 
  )
  <?php comment_text() ?> 

</li> 
<!-- /comment --> 
<?php //end of the loop, don't delete
		} // end foreach
	echo '</ol>';
	}//end if comments
	?>
<?php } ?> 
</div> 
<?php 
 include('admin-footer.php');
?> 
