<?php
require_once('admin.php');

$title = __('Posts');
$parent_file = 'edit.php';
$list_js = true;
require_once('admin-header.php');

$_GET['m'] = (int) $_GET['m'];

$drafts = get_users_drafts( $user_ID );
$other_drafts = get_others_drafts( $user_ID);

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

wp('what_to_show=posts&posts_per_page=15&posts_per_archive_page=-1');

if ( is_month() ) {
	single_month_title(' ');
} elseif ( is_search() ) {
	printf(__('Search for &#8220;%s&#8221;'), wp_specialchars($_GET['s']) );
} else {
	if ( is_single() )
		printf(__('Comments on %s'), $post->post_title);
	elseif ( ! is_paged() || get_query_var('paged') == 1 )
		_e('Last 15 Posts');
	else
		_e('Previous Posts');
}
?>
</h2>

<form name="searchform" action="" method="get" style="float: left; width: 16em; margin-right: 3em;"> 
  <fieldset> 
  <legend><?php _e('Search Posts&hellip;') ?></legend> 
  <input type="text" name="s" value="<?php if (isset($s)) echo wp_specialchars($s, 1); ?>" size="17" /> 
  <input type="submit" name="submit" value="<?php _e('Search') ?>"  /> 
  </fieldset>
</form>

<?php $arc_result = $wpdb->get_results("SELECT DISTINCT YEAR(post_date) AS yyear, MONTH(post_date) AS mmonth FROM $wpdb->posts WHERE post_type = 'post' ORDER BY post_date DESC");

if ( count($arc_result) ) { ?>

<form name="viewarc" action="" method="get" style="float: left; width: 20em; margin-bottom: 1em;">
	<fieldset>
	<legend><?php _e('Browse Month&hellip;') ?></legend>
    <select name='m'>
	<?php
		foreach ($arc_result as $arc_row) {
			$arc_year  = $arc_row->yyear;
			$arc_month = $arc_row->mmonth;
			$arc_month = zeroise($arc_month, 2);

			if( isset($_GET['m']) && $arc_year . $arc_month == (int) $_GET['m'] )
				$default = 'selected="selected"';
			else
				$default = null;

			echo "<option $default value=\"" . $arc_year . $arc_month . '">';
			echo $wp_locale->get_month($arc_month) . " $arc_year";
			echo "</option>\n";
		}
	?>
	</select>
		<input type="submit" name="submit" value="<?php _e('Show Month') ?>"  /> 
	</fieldset>
</form>

<?php } ?>

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
	<thead>
	<tr>

<?php foreach($posts_columns as $column_display_name) { ?>
	<th scope="col"><?php echo $column_display_name; ?></th>
<?php } ?>

	</tr>
	</thead>
	<tbody id="the-list">
<?php
if ($posts) {
$bgcolor = '';
foreach ($posts as $post) { start_wp();
add_filter('the_title','wp_specialchars');
$class = ('alternate' == $class) ? '' : 'alternate';
?> 
	<tr id='post-<?php echo $id; ?>' class='<?php echo $class; ?>'>

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
		<td><?php if ( current_user_can('edit_post',$post->ID) ) { echo "<a href='post.php?action=edit&amp;post=$id' class='edit'>" . __('Edit') . "</a>"; } ?></td>
		<?php
		break;

	case 'control_delete':
		?>
		<td><?php if ( current_user_can('delete_post',$post->ID) ) { echo "<a href='post.php?action=delete&amp;post=$id' class='delete' onclick=\"return deleteSomething( 'post', " . $id . ", '" . sprintf(__("You are about to delete this post &quot;%s&quot;.\\n&quot;OK&quot; to delete, &quot;Cancel&quot; to stop."), addslashes(wp_specialchars(get_the_title(),'double')) ) . "' );\">" . __('Delete') . "</a>"; } ?></td>
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
	</tbody>
</table>

<div id="ajax-response"></div>

<div class="navigation">
<div class="alignleft"><?php next_posts_link(__('&laquo; Previous Entries')) ?></div>
<div class="alignright"><?php previous_posts_link(__('Next Entries &raquo;')) ?></div>
</div>

<?php
if ( 1 == count($posts) ) {

	$comments = $wpdb->get_results("SELECT * FROM $wpdb->comments WHERE comment_post_ID = $id AND comment_approved != 'spam' ORDER BY comment_date");
	if ($comments) {
	?> 
<h3 id="comments"><?php _e('Comments') ?></h3> 
<ol id="the-list"> 
<?php
$i = 0;
foreach ($comments as $comment) {
$class = ( ++$i % 2 ) ? array('alternate') : array();
if ( 'unapproved' == $comment_status = wp_get_comment_status($comment->comment_ID) )
	$class[] = 'unapproved';
?> 

<li id='comment-<?php echo $comment->comment_ID; ?>'<?php if ( $class ) echo " class='" . join(' ', $class) . "'"; ?>>
  <?php comment_date('Y-n-j') ?> 
  @
  <?php comment_time('g:m:s a') ?> 
  <?php 
			if ( current_user_can('edit_post', $post->ID) ) {
				echo "[ <a href='post.php?action=editcomment&amp;comment=".$comment->comment_ID."\'>" .  __('Edit') . '</a>';
				echo ' - <a href="post.php?action=deletecomment&amp;p=' . $post->ID . '&amp;comment=' . $comment->comment_ID . '" onclick="return deleteSomething( \'comment\', ' . $comment->comment_ID . ', \'' . sprintf(__("You are about to delete this comment by &quot;%s&quot;.\\n&quot;Cancel&quot; to stop, &quot;OK&quot; to delete."), wp_specialchars($comment->comment_author, 1)) . "' );\">" . __('Delete') . '</a> ';
				if ( ('none' != $comment_status) && ( current_user_can('moderate_comments') ) ) {
					echo '<span class="unapprove"> - <a href="comment.php?action=unapprovecomment&amp;p=' . $post->ID . '&amp;comment=' . $comment->comment_ID . '" onclick="return dimSomething( \'comment\', ' . $comment->comment_ID . ', \'unapproved\' );">' . __('Unapprove') . '</a> </span>';
					echo '<span class="approve"> - <a href="comment.php?action=approvecomment&amp;p=' . $post->ID . '&amp;comment=' . $comment->comment_ID . '" onclick="return dimSomething( \'comment\', ' . $comment->comment_ID . ', \'unapproved\' );">' . __('Approve') . '</a> </span>';
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
