<?php if (!defined('W3TC')) die(); ?>
<?php
    if (! isset($title)) {
        $title = 'Untitled';
    }

    if (! isset($errors)) {
        $errors = array();
    }

    if (! isset($notes)) {
        $notes =array();
    }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php do_action('admin_xml_ns'); ?> <?php language_attributes(); ?>>
	<head>
		<link rel="stylesheet" type="text/css" href="<?php echo plugins_url('pub/css/popup.css?ver=' . W3TC_VERSION, W3TC_FILE); ?>" />
		<script type="text/javascript" src="<?php echo site_url('wp-includes/js/jquery/jquery.js?ver=' . W3TC_VERSION); ?>"></script>
		<script type="text/javascript" src="<?php echo plugins_url('pub/js/metadata.js?ver=' . W3TC_VERSION, W3TC_FILE); ?>"></script>
		<script type="text/javascript" src="<?php echo plugins_url('pub/js/popup.js?ver=' . W3TC_VERSION, W3TC_FILE); ?>"></script>
		<title><?php echo htmlspecialchars($title); ?> - W3 Total Cache</title>
		<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php echo get_option('blog_charset'); ?>" />
	</head>
	<body>
		<div id="content">
			<h1><?php echo htmlspecialchars($title); ?></h1>

        	<?php if (count($errors)): ?>
            <div class="error">
            	<?php foreach ($errors as $error): ?>
            	<p><?php echo $error; ?></p>
            	<?php endforeach; ?>
            </div>
        	<?php endif; ?>

        	<?php if (count($notes)): ?>
            <div class="updated fade">
            	<?php foreach ($notes as $note): ?>
            	<p><?php echo $note; ?></p>
            	<?php endforeach; ?>
            </div>
        	<?php endif; ?>
