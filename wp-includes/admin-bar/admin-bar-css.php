<?php 
	header( 'Content-type: text/css' );
	$proto = ( empty( $_GET['p'] ) ) ? 'http://' : 'https://';
	$text_direction =  $_GET['td'];
	if ( 'ltr' == $text_direction || empty( $_GET['td'] ) )
		$sprite = $_GET['inc'] . 'images/admin-bar-sprite.png?d=08102010';
	else
		$sprite = $_GET['inc'] . 'images/admin-bar-sprite-rtl.png?d=08102010';
?>

#wpadminbar { direction:ltr; background:#666 url(<?php echo $sprite; ?>) 0 -222px repeat-x; color:#ddd; font:12px Arial, Helvetica, sans-serif; height:28px; left:0; margin:0; position:fixed; top:0; width:100%; z-index:99999; min-width: 960px; }
#wpadminbar ul, #wpadminbar ul li { position: relative; z-index: 99999; }
#wpadminbar ul li img { vertical-align: middle !important; margin-right: 8px !important; border: none !important; padding: 0 !important; }
#wpadminbar .quicklinks > ul > li > a { border-right: 1px solid #686868; border-left: 1px solid #808080; }
#wpadminbar .quicklinks > ul > li.ab-subscriptions > a, #wpadminbar .quicklinks > ul > li:last-child > a { border-right: none; }
#wpadminbar .quicklinks > ul > li.hover > a { border-left-color: #707070; }
#wpadminbar a { outline: none; }
#wpadminbar .avatar {border:1px solid #999 !important;padding:0 !important;margin:-3px 5px 0 0 !important;vertical-align:middle;float:none;display:inline !important; }
#wpadminbar .menupop ul li a {color:#555 !important;text-shadow:none;font-weight:normal;white-space:nowrap;}
#wpadminbar .menupop a > span {background:url(<?php echo $sprite; ?>) 100% 100.4% no-repeat;padding-right:.8em;line-height: 28px;}
#wpadminbar .menupop ul li a > span { display: block; background:url(<?php echo $sprite; ?>) 100% 97.2% no-repeat;padding-right:1.5em;line-height: 28px;}
#wpadminbar .menupop ul li a span#awaiting-mod { display: inline; background: #aaa; color: #fff; padding: 1px 5px; font-size: 10px; font-family: verdana; -moz-border-radius: 5px; -webkit-border-radius: 5px; border-radius: 5px; }
#wpadminbar .menupop ul li a:hover span#awaiting-mod { background: #fff; color: #888; }
#wpadminbar .menupop ul {-moz-box-shadow:0 4px 8px rgba(0,0,0,0.1);-webkit-box-shadow:0 4px 8px rgba(0,0,0,0.1);background:#fff;display:none;position:absolute;border:1px solid #dfdfdf;border-top:none !important;float:none}
html>body #wpadminbar .menupop ul {background:rgba(255,255,255,0.97);border-color:rgba(0,0,0,0.1);}
#wpadminbar .menupop.ab-my-account ul, #wpadminbar .menupop.ab-my-dash ul, #wpadminbar .menupop.ab-new-post ul {min-width:140px}
#wpadminbar .menupop li {float:none;margin:0;padding:0;background-image:none;}
#wpadminbar .quicklinks a {border:none;color:#ddd !important;text-shadow:#555 0px -1px 0px;display:block;font:13px Arial, Helvetica, sans-serif;font-weight:normal;letter-spacing:normal;padding:0 0.85em;line-height:28px;text-decoration:none !important;}
#wpadminbar .quicklinks a:hover {text-shadow:#333 0px -1px 0px;}
#wpadminbar li.ab-privacy { float: right; background: #333; }
#wpadminbar li.ab-privacy > a > span { background: none; padding: 0;  }
#wpadminbar li.ab-privacy span#priv-icon { display: block; text-indent: -999em; background:url(<?php echo $sprite; ?>) 40% 59.7% no-repeat; padding: 0; width: 13px; margin-right: -3px; }

#wpadminbar li.ab-sadmin { float: right; background: #555 }
#wpadminbar li.ab-sadmin ul, #wpadminbar li.ab-privacy ul { right: 0; float: right; }
#wpadminbar li.ab-sadmin > a { font-size: 11px !important; padding: 0 7px !important; border: none !important; border-left: 1px solid #666 !important; }

#wpadminbar li.ab-sadmin ul a, #wpadminbar li.ab-privacy a { border-right: none !important; border-left: none !important; }
#wpadminbar li.ab-sadmin ul li { right: 0; float: right; text-align: left; width: 100%; }
#wpadminbar li.ab-sadmin ul li a { padding-left: 1.75em; } 
#wpadminbar li.ab-sadmin ul li a > span { background:url(<?php echo $sprite; ?>) 0% 101.8% no-repeat;padding-left: 1.25em; margin-left: -1.25em; line-height: 28px; padding-right: 0 !important; }
#wpadminbar li a.loading { background: url(</ajax-loader.gif) 10px 50% no-repeat !important; padding-left: 29px; }
#wpadminbar li.subscribed a strong { background:url(<?php echo $sprite; ?>) 32% 59.8% no-repeat !important; text-indent: -999em; overflow: hidden; padding: 0 16px 0 0; height: 28px; display: block; float: left; margin-right: 2px; }

#wpadminbar li:hover {background: #555 url(<?php echo $sprite; ?>) 0 -282px repeat-x;}
#wpadminbar li li:hover { color:#fff !important; background: #888 url(<?php echo $sprite; ?>) 0 -222px repeat-x !important;text-shadow: #666 0px -1px 0px;}
#wpadminbar li li:hover > a { color:#fff !important; }
.quicklinks ul {list-style:none;margin:0;padding:0;text-align:left}
.quicklinks ul li {float:left;margin:0}

#adminbarlogin {float:left;display:inline;}

#adminbarsearch {float:right; }
#adminbarsearch {height: 18px;padding: 3px;}
#adminbarsearch * {color: #555;font-size:12px;}
#adminbarsearch label, #adminbarsearch a { height: 28px; color: #ccc; display:block;float:left;padding:3px 4px;text-shadow:0px -1px 0px #444;}
#adminbarsearch a {text-decoration:underline;}
#adminbarsearch a:hover {color:#fff;}

#wpadminbar li.ab-me:hover, #wpadminbar li.ab-blog:hover { background:none;}
#wpadminbar li.ab-me > a, #wpadminbar li.ab-blog > a { line-height: 18px !important; border: none !important; background:url(<?php echo $sprite; ?>) 100% 59.8% no-repeat; height: 28px; padding: 0 1.15em 0 0.7em; }
#wpadminbar li.ab-me > a.hover, #wpadminbar li.ab-blog > a.hover { background-position: 67% 59.8%; }
#wpadminbar li.ab-me img.avatar, #wpadminbar li.ab-blog img.avatar { margin: 4px 0 0 0 !important; vertical-align: middle; background: #eee; width: 16px !important; height: 16px !important; }
#wpadminbar li.ab-my-account a, #wpadminbar li.ab-bloginfo a { border-left: none !important; padding-left: 0.7em !important; margin-top: 0 !important; }
#wpadminbar li.ab-my-account > ul, #wpadminbar li.ab-bloginfo > ul { left: -7px; }
#wpadminbar ul li img { width: 16px !important; height: 16px !important; }

#wpadminbar ul li a strong.count { text-shadow: none; background: #ddd; color: #555; margin-left: 5px; padding: 1px 6px; top: -1px; position: relative; font-size: 9px; -moz-border-radius: 7px; -webkit-border-radius: 7px; border-radius: 7px; font-weight: normal }

#wpadminbar #q {
	line-height:normal !important;
	width:140px !important;
	margin-top:0px !important;
}
.adminbar-input {
	display:block !important;
	float:left !important;
	font:12px Arial,Helvetica,sans-serif !important;
	border:1px solid #626262 !important;
	padding:2px 3px !important;
	margin-right:3px !important;
	background:#ddd url(<?php echo $sprite; ?>) top left no-repeat !important;
	-webkit-border-radius:0 !important;
	-khtml-border-radius:0 !important;
	-moz-border-radius:0 !important;
	border-radius:0 !important;
	outline:none;
	text-shadow:0 1px 0 #fff;
}
button.adminbar-button {
	position:relative;
	border:0;
	cursor:pointer;
	overflow:visible;
	margin:0 !important;
	float:left;
	background:url(<?php echo $sprite; ?>) right -107px no-repeat;
	padding:0 14px 0 0;
	text-align:center;
}
button.adminbar-button span {
	position:relative;
	display:block;
	white-space:nowrap;
	height:19px;
	background:url(<?php echo $sprite; ?>) left -69px no-repeat;
	padding:3px 0 0 14px;
	font:12px Arial,Helvetica,sans-serif !important;
	font-weight:bold !important;
	color:#444 !important;
	text-shadow:0px 1px 0px #eee !important;
}
button.adminbar-button:active {
	background-position:right -184px !important;
	text-shadow:0px 1px 0px #eee !important;
}
button.adminbar-button:hover span {
	color:#000 !important;
}
button.adminbar-button:active span {
	background-position:left -146px !important;
}
button.adminbar-button::-moz-focus-inner {
	border:none;
}
@media screen and (-webkit-min-device-pixel-ratio:0) {
	button.adminbar-button span {
		margin-top: -1px;
	}
}

<?php if ( 'rtl' == $text_direction ) : ?>
	#wpadminbar {
		direction:rtl;
		font-family: Tahoma, Arial ,sans-serif;
		right:0;
		left:auto;
	}
	#wpadminbar div, #wpadminbar ul, #wpadminbar ul li {
		min-height: 0;
	}
	#wpadminbar ul li img {  margin-left: 8px !important; margin-right: 0 !important; }
	#wpadminbar .quicklinks > ul > li > a { border-left: 1px solid #686868; border-right: 1px solid #808080;}
	#wpadminbar .quicklinks > ul > li.ab-subscriptions > a, #wpadminbar .quicklinks > ul > li:last-child > a { border-left: none; border-right: 1px solid #808080;}
	#wpadminbar .quicklinks > ul > li.hover > a { border-right-color: #707070; border-left-color: #686868; }
	#wpadminbar .avatar {margin: -3px  0 0 5px !important; float:none;  }
	#wpadminbar .menupop a > span {background-position: 0 100.4%; padding-left:.8em;}
	#wpadminbar .menupop ul li a > span { background-position: 0% 97.2%; padding-right:0;padding-left:1.5em }
	#wpadminbar .menupop ul {right: 0; width:100%; min-width:150px;}
	#wpadminbar .ab-my-account ul { width:200px;}
	#wpadminbar .ab-my-blogs ul { width:300px;}
	#wpadminbar .ab-my-blogs ul ul { width:200px;}
	#wpadminbar .ab-subscribe ul { width:150px;}
	#wpadminbar .ab-bloginfo ul { width:200px;}
	#wpadminbar .ab-subscribe ul { width:150px;}
	#wpadminbar .ab-subscriptions ul { width:200px;}
	#wpadminbar .menupop ul li {width:auto}
	#wpadminbar .quicklinks a {font-family: Tahoma, Arial, Helvetica, sans-serif;}
	#wpadminbar li.ab-privacy { float: left; }
	#wpadminbar li.ab-privacy span#priv-icon { text-indent: 999em; background-position: 60% 59.7%; padding: 0; margin-right: 0; margin-left: -3px;}

	#wpadminbar li.ab-sadmin { float: left;  }
	#wpadminbar li.ab-sadmin ul, #wpadminbar li.ab-privacy ul { right: auto; left: 0; float: left; }
	#wpadminbar li.ab-sadmin > a { border-right: 1px solid #666 !important; border-left:none !important;}

	#wpadminbar li.ab-sadmin ul a, #wpadminbar li.ab-privacy a { border-right: none !important; border-left: none !important; }
	#wpadminbar li.ab-sadmin ul li { left: 0; right:auto; float: left; text-align: right;  }


	#wpadminbar li.ab-sadmin ul li a { padding-right: 1.75em; padding-left: 0 } 
	#wpadminbar li.ab-sadmin ul li a > span { background-position: 100% 101.8%; padding-right: 1.25em !important; padding-left: 0 !important; margin-right: -1.25em; margin-left: 0; }
	#wpadminbar li a.loading { background-position: right 50% !important; padding-right: 29px; padding-left: 0;}
	#wpadminbar li.subscribed a strong { background-position: 68% 59.8% !important;  padding: 0 0 0 16px; float: right; margin-left: 2px; }


	.quicklinks ul {text-align:right}
	.quicklinks ul li {float:right;}

	#adminbarlogin {float:right;}

	#adminbarsearch {display:none;}
	#adminbarsearch label, #adminbarsearch a { float:right;}

	#wpadminbar li.ab-me > a, #wpadminbar li.ab-blog > a { background-position:0% 59.8%; padding: 0 0.7em 0 1.15em; }
	#wpadminbar li.ab-me > a.hover, #wpadminbar li.ab-blog > a.hover { background-position: 33% 59.8%; }
	#wpadminbar li.ab-my-account a, #wpadminbar li.ab-bloginfo a { border-right: none !important; padding-right: 0.7em !important;  }
	#wpadminbar li.ab-my-account > ul, #wpadminbar li.ab-bloginfo > ul { right: -7px; left:auto;}

	#wpadminbar ul li a strong.count { margin-right: 5px; margin-left: 0; position:static}


	.adminbar-input {
		float:right !important;
		font-family: Tahoma, Arial,Helvetica,sans-serif !important;
		margin-right:3px !important;
		margin-left:0 !important;
		background-position: right top !important;
	}
	button.adminbar-button {
		float:right;
		background-position: left -107px;
		padding:0 0 0 14px;
	}
	button.adminbar-button span {
		background-position: right -69px;
		padding:3px 14px 0 0;
		font-family: Tahoma, Arial,Helvetica,sans-serif !important;
	}
	button.adminbar-button:active {
		background-position:left -184px !important;
	}
	button.adminbar-button:active span {
		background-position:right -146px !important;
	}
<?php
endif;

$current_theme = str_replace( '+', ' ', $_GET['t'] );
$is_admin = $_GET['a'];
$is_super_admin = $_GET['sa'];

if ( ( empty($_GET['nobump']) || $is_admin ) && !strpos( $_SERVER['REQUEST_URI'], 'media-upload.php' ) ) : ?>
	body { padding-top: 28px !important; }
<?php endif; ?>

<?php if ( in_array( $current_theme, array('H3', 'H4', 'The Journalist v1.9') ) ) { ?>
	body { padding-top: 28px; background-position: 0px 28px; }
<?php } ?>

<?php if ( $is_super_admin ) : ?>
	#querylist {
		font-family: Arial, Tahoma, sans-serif;
		display: none;
		position: absolute;
		top: 50px;
		left: 50px;
		right: 50px;
		background: #fff;
		padding: 20px;
		-moz-box-shadow: 0 0 15px #888;
		-webkit-box-shadow: 0 0 15px #888;
		box-shadow: 0 0 15px #888;
		z-index: 99999;
		border: 10px solid #f0f0f0;
		color: #000;
		line-height: 150% !important;
	}
	#querylist pre {
		font-size: 12px;
		padding: 10px;
	}
	
	#querylist h1 {
		font-family: georgia, times, serif;
		text-align: center;
		font-size: 24px;
		padding: 20px 5px;
		background: #eee;
		color: #555;
		margin: 0;
	}
	#querylist div#debug-status {
		background: #ccc;
		color: #fff;
		overflow: hidden;
		height: 21px;
		font-size: 14px;
		font-family: georgia, times, serif;
		padding: 7px 15px;
	}
	#querylist .left { float: left; }
	#querylist .right { float: right; }
	
	#querylist h1, #querylist h2, #querylist h3 {
		font-weight: normal;
	}
	
	#querylist ul.debug-menu-links {
		clear: left;
		background: #ccc;
		padding: 10px 15px 0;
		overflow: hidden;
		list-style: none;
		margin: 0;
		padding: 0 0 0 15px;
	}
		#querylist ul.debug-menu-links li {
			float: left;
			margin-right: 10px;
			margin-bottom: 0 !important;
		}
		
		#querylist ul.debug-menu-links li a {
			outline: none;
			display: block;
			padding: 5px 9px;
			margin-right: 0;
			background: #bbb;
			color: #fff !important;
			text-decoration: none !important;
			font-weight: normal;
			font-size: 12px;
			color: #555;
			-webkit-border-top-right-radius: 4px;			
			-webkit-border-top-left-radius: 4px;
			-moz-border-radius-topright: 4px;
			-moz-border-radius-topleft: 4px;
		}
			#querylist ul.debug-menu-links li.current a {
				background: #fff;
				color: #555 !important;
			}
	
	#querylist h2 {
		float: left;
		min-width: 150px;
		border: 1px solid #eee;
		padding: 5px 10px 15px;
		clear: none; important;
		text-align: center;
		font-family: georgia, times, serif;
		font-size: 28px;
		margin: 15px 10px 15px 0 !important;
	}
		#querylist h2 span {
			font-size: 12px;
			color: #888;
			text-transform: uppercase;
			white-space: nowrap;
			display: block;
			margin-bottom: 5px;
		}

	#object-cache-stats h2 {
		border: none;
		float: none;
		text-align: left;
		font-size: 22px;
		margin-bottom: 0;
	}

	#object-cache-stats ul.debug-menu-links {
		padding: 0;
		margin: 0;
		background: none;
	}
		#object-cache-stats ul.debug-menu-links li {
			float: left;
			margin-bottom: 10px !important;
			background: none !important;
			border: 1px solid #eee !important;
			color: #555 !important;
		}
			#object-cache-stats ul.debug-menu-links li.current a {
				background: #ccc !important;
				color: #fff !important;
				-webkit-border-top-right-radius: 0;			
				-webkit-border-top-left-radius: 0;
				-moz-border-radius-topright: 0;
				-moz-border-radius-topleft: 0;
			}
			
			#object-cache-stats ul.debug-menu-links li a {
				background: none;
				color: #555 !important;
				overflow: hidden;
			}
	
	#querylist h3 {
		margin-bottom: 15px;
	}

	#querylist ol#wpd-queries {
		padding: 0 !important;
		margin: 0 !important;
		list-style: none;
		clear: left;
	}
		#querylist ol#wpd-queries li {
			padding: 10px;
			background: #f0f0f0;
			margin: 0 0 10px 0;
		}
			#querylist ol#wpd-queries li div.qdebug {
				background: #e8e8e8;
				margin: 10px -10px -10px -10px;
				padding: 5px 150px 5px 5px;
				font-size: 11px;
				position: relative;
				min-height: 20px;
			}
			
			#querylist ol#wpd-queries li div.qdebug span {
				position: absolute;
				right: 10px;
				top: 5px;
				white-space: nowrap;
			}
	
	#querylist a {
		text-decoration: underline !important;
		color: blue !important;
	}
	#querylist a:hover {
		text-decoration: none !important;
	}
	#querylist .debug-menu-target {
		display: none;
	}
	
	#querylist ol {
		font: 12px Monaco, "Courier New", Courier, Fixed !important;
		line-height: 180% !important;
	}
	
	#wpadminbar #admin-bar-micro ul li {
		list-style-type: none;
		position: relative;
		margin: 0;
		padding: 0;
	}
	#wpadminbar #admin-bar-micro ul ul, #wpadminbar #admin-bar-micro #awaiting-mod, #wpadminbar .ab-sadmin .count-0 {
		display: none !important;
	}
	#wpadminbar #admin-bar-micro ul li:hover > ul {
		display: block;
		position: absolute;
		top: -1px;
		left: 100%;
	}
	#wpadminbar #admin-bar-micro li a {
		display: block;
		text-decoration: none;
	}
	#wpadminbar #admin-bar-micro li li a {
		background: #ddd;
	}
	#wpadminbar #admin-bar-micro li li li a {
		background: #fff;
	}
	
	<?php if ( 'rtl' == $text_direction ) : ?>
	
		#querylist {
			direction: ltr;
		}
		
		#wpadminbar #admin-bar-micro ul li:hover > ul {
			left: auto;
			right: 100%;
		}
	<?php endif; ?>
<?php endif; ?>