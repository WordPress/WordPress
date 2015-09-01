<!DOCTYPE html>
<html class="no-js">
<head>
	<script>(function(H){H.className=H.className.replace(/\bno-js\b/,'js')})(document.documentElement)</script>
    <meta charset="utf-8">
	<meta name="viewport" content="width=device-width,initial-scale=1">
	<title><?php is_front_page() ? bloginfo('description') : wp_title(''); ?></title>
	
	<link rel='stylesheet' href='<?php echo THEME_URL; ?>/style.css?v=1' type='text/css' media='all' />

    <script>
      var _gaq=[['_setAccount','UA-XXXXX-X'],['_trackPageview']];
      (function(d,t){var g=d.createElement(t),s=d.getElementsByTagName(t)[0];
      g.src=('https:'==location.protocol?'//ssl':'//www')+'.google-analytics.com/ga.js';
      s.parentNode.insertBefore(g,s)}(document,'script'));
    </script>

    <?php wp_head(); ?> 
    
</head>

<body <?php body_class(); ?>>