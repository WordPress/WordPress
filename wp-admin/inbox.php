<?php
require_once('admin.php');

$title = __('Inbox');
$parent_file = 'inbox.php';

require_once('admin-header.php');

if ( !empty($_GET['doaction']) ) :

?>

<div class="updated">
	<p>This feature isn't enabled in this prototype.</p>
</div>

<?php

endif;

?>
<div class="wrap">
<form id="inbox-filter" action="" method="get">
<ul class="subsubsub">
<li><a href="#" class="current"><?php _e('Messages') ?></a> | </li><li><a href="#"><?php echo sprintf(__('Archived') . ' (%s)', '42'); ?></a></li>
</ul>
<div class="tablenav">
<div class="alignleft">
<select name="action">
<option value="" selected><?php _e('Actions'); ?></option>
<option value="archive"><?php _e('Archive'); ?></option>
</select>
<input type="submit" value="<?php _e('Apply'); ?>" name="doaction" id="doaction" class="button-secondary action" />
</div>
<br class="clear" />
</div>
<br class="clear" />
<table class="widefat">
	<thead>
	<tr>
	<th scope="col" class="check-column"><input type="checkbox" /></th>
	<th scope="col"><?php _e('Message'); ?></th>
	<th scope="col"><?php _e('Date'); ?></th>
	<th scope="col"><?php _e('From'); ?></th>
	</tr>
	</thead>
	<tbody>

<?php $crazy_posts = array( '', 'some post', 'a post', 'my cool post' ); foreach ( wp_get_inbox_items() as $k => $item ) : // crazyhorse ?>
	
	<tr id="message-<?php echo $k; ?>">
		<th scope="col" class="check-column"><input type="checkbox" name="messages[]" value="<?php echo $k; ?>" /></th>
		<td><?php
			if ( $item->href )
				echo "<a href='$item->href' class='no-crazy'>";
			echo wp_specialchars( $item->text );
			if ( strlen( $item->text ) > 180 ) // crazyhorse
				echo '<br/><span class="inbox-more">more&hellip;</span>';
			if ( $item->href )
				echo '</a>';
		?></td>
		<td><a href="#link-to-comment" class="no-crazy"><abbr title="<?php echo "$item->date at $item->time"; ?>"><?php echo $item->date; ?></abbr></a></td>
		<td><?php
			echo $item->from;
			if ( 'comment' == $item->type ) // crazyhorse
				echo "<br/>on &quot;<a href='#' class='no-crazy'>{$crazy_posts[$item->parent]}</a>&quot;";
		?></td>
	</tr>

<?php endforeach; ?>

</table>
</form>

<div class="tablenav"></div>
<br class="clear"/>
</div>
<?php include('admin-footer.php'); ?>
