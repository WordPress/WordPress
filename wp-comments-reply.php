<?php 
/* Don't remove these lines. */
$single = 1;
$id = (int) $_GET['post_'];
require ('wp-blog-header.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><?php echo get_settings('blogname'); ?> &raquo; Comments on "<?php the_title() ?>"</title>

	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo get_settings('blog_charset'); ?>" />
	<style type="text/css" media="screen">
		@import url( wp-layout.css );
		body { margin: 3px; padding: 10px}
	</style>

</head>
<body id="commentspopup">

<h1 id="header"><a href="<?php echo get_settings('home'); ?>" title="<?php echo get_settings('blogname'); ?>"><?php echo get_settings('blogname'); ?></a></h1>

<h2 id="comments">Replying to Comment:</h2>
<?php
	if (($withcomments) or ($single)) {

        if (!empty($post->post_password)) { // if there's a password
            if ($_COOKIE['wp-postpass_'.$cookiehash] != $post->post_password) {  // and it doesn't match the cookie
                echo("<p>Enter your password to view comments.<p>");
                return;
            }
        }

 		$comment_author = (isset($_COOKIE['comment_author_'.$cookiehash])) ? trim($_COOKIE['comment_author_'.$cookiehash]) : '';
        $comment_author_email = (isset($_COOKIE['comment_author_email_'.$cookiehash])) ? trim($_COOKIE['comment_author_email_'.$cookiehash]) : '';
 		$comment_author_url = (isset($_COOKIE['comment_author_url_'.$cookiehash])) ? trim($_COOKIE['comment_author_url_'.$cookiehash]) : '';
        $comments = $wpdb->get_results("SELECT * FROM $tablecomments WHERE comment_post_ID = '$id' AND comment_approved = '1' AND comment_ID = '$comment_reply_ID' ORDER BY comment_date");
?>

<!-- You can start editing here. -->


<?php if ($comments) { ?>
<ol id="commentlist">
<?php foreach ($comments as $comment) { ?>
	<li id="comment-<?php comment_ID() ?>">
	<?php comment_text() ?>
	<p><cite><?php comment_type(); ?> by <?php comment_author_link() ?> &#8212; <?php comment_date() ?> @ <a href="#comment-<?php comment_ID() ?>"><?php comment_time() ?></a></cite> <?php edit_comment_link('Edit This', ' |'); ?></p>
	</li>

<?php } // end for each comment ?>
</ol>
<?php } else { // this is displayed if there are no comments so far ?>
	<p>No comments yet.</p>
<?php } ?>
<h2 id="postcomment">Leave a Comment:</h2>
<?php if ('open' == $post->comment_status) { ?>
<p>Line and paragraph breaks automatic, email address never displayed, <acronym title="Hypertext Markup Language">HTML</acronym> allowed: <code><?php echo allowed_tags(); ?></code></p>

<form action="<?php echo get_settings('siteurl'); ?>/wp-comments-post.php" method="post" id="commentform">
	<p>
	  <input type="text" name="author" id="author" class="textarea" value="<?php echo $comment_author; ?>" size="28" tabindex="1" />
	   <label for="author">Name</label>
	<input type="hidden" name="comment_post_ID" value="<?php echo $id; ?>" />
	<input type="hidden" name="redirect_to" value="<?php echo get_settings('siteurl')."/index.php?p=".$id ?>" />
	<input type="hidden" name="comment_reply_ID" value="<?php echo $comment_reply_ID; ?>" />
	</p>

	<p>
	  <input type="text" name="email" id="email" value="<?php echo $comment_author_email; ?>" size="28" tabindex="2" />
	   <label for="email">Email</label>
	</p>

	<p>
	  <input type="text" name="url" id="url" value="<?php echo $comment_author_url; ?>" size="28" tabindex="3" />
	   <label for="url"><acronym title="Uniform Resource Identifier">URI</acronym></label>
	</p>

	<p>
	  <label for="comment">Your Comment</label>
	<br />
	  <textarea name="comment" id="comment" cols="70" rows="4" tabindex="4"></textarea>
	</p>

	<p>
	  <input name="submit" type="submit" tabindex="5" value="Say it!" />
	</p>
</form>
<?php } else { // comments are closed ?>
<p>Sorry, the comment form is closed at this time.</p>
<?php } ?>

<?php // if you delete this the sky will fall on your head
}
?>
