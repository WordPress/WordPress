<?php
$_wp_installing = 1;

require_once('../wp-config.php');
require_once('wp-install-helper.php');

$step = intval($HTTP_GET_VARS['step']);
if (!$step) $step = 0;
if (!step) $step = 0;
$file = basename(__FILE__);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<title>WordPress >Database upgrade for comment moderation hack</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<style media="screen" type="text/css">
	body {
		font-family: Georgia, "Times New Roman", Times, serif;
		margin-left: 15%;
		margin-right: 15%;
	}
	#logo {
		margin: 0;
		padding: 0;
		background-image: url(http://wordpress.org/images/wordpress.gif);
		background-repeat: no-repeat;
		height: 72px;
		border-bottom: 4px solid #333;
	}
	#logo a {
		display: block;
		height: 72px;
	}
	#logo a span {
		display: none;
	}
	p {
		line-height: 140%;
	}
	</style>
</head>
<body>
<h1 id="logo"><a href="http://wordpress.org"><span>WordPress</span></a></h1>

<?php

switch($step) {
    case 0:
?>

<p>This will upgrade your database in order to be able to use otaku42's comment
moderation hack.</p>
<p>First of all: <strong>backup your database!</strong> This script will make
changes to it and it could happen that things aren't going the way they should.
You have been warned.</p>
<p>What this hack does is simple: it introduces a new option for comment moderation.
Comment moderation means that new comments won't show up in your blog until they
have been approved. Approval happens either manually or automatically (not implemented
yet). This all is a first step towards comment spam prevention.
<br /> You will have a simple panel in the admin section that shows you waiting 
comments. You can either approve or delete them, or hold them further for approval.</p>
<p>The procedure is easy: click on the next button and see if there
are any warnings popping up. If so, please report the problem(s) to me 
(<a href="mailto:mrenzmann@otaku42.de">mrenzmann@otaku42.de</a>) so that I can
fix it/them.</p>
<p>The following passage (grey text) is of interest for you only if you are familiar
with WordPress development:</p>
<span style="color: #888888;">
<p>In order to have the patch working we need to extend the comment table with a
field that indicates whether the comment has been approved or not (<em>comment_approved</em>).
Its default value will be <em>1</em> so that comments are auto-approved when comment
moderation has been turned off by the admin.</p>
<p>The next thing is that we need an option to turn comment moderation on/off. It will
be named <em>comment_moderation</em> and can be found in <em>General blog
settings</em>.</p>
<p>Another option that gets inserted is <em>moderation_notify</em>. If turned on, a mail
will be sent to the admin to inform about the new (and possibly other) comment that is/are
waiting for his approval.</p>
</p>This upgrade procedure tries to be as save as possible by not relying on any hardcoded
values. For example it retrieves the id for option group <em>general blog settings</em>
rather than assuming it has the same id as in my own blog.</p>
</span>
<p>Ready? 

<?php
	echo "<a href=\"$file?step=1\">Let's go!</a></p>\n";
	break; // end case 0
    
    case 1:
	$result = "";
	$error_count = 0;
	$continue = true;

	// insert new column "comment_approved" to $tablecomments
	if ($continue) {
	    $tablename = $tablecomments;
	    $tablecol = "comment_approved";
	    $ddl = "ALTER TABLE $tablecomments ADD COLUMN $tablecol ENUM('0','1') DEFAULT '1' NOT NULL";
	    $result .= "Adding column $tablecol to table $tablename: ";
	    if (maybe_add_column($tablename, $tablecol, $ddl)) {
	        $result .= "ok<br />\n";
		$result .= "Indexing new column $tablecol: ";

		$wpdb->query("ALTER TABLE $tablename ADD INDEX ($tablecol)");
		$results = $wpdb->get_results("SHOW INDEX FROM $tablecomments");
		foreach ($results as $row) {
		    if ($row->Key_name == $tablecol) {
			$index=1;
		    }
		}

		if (1 == $index) {
		    $result .= "ok";
		    $continue = true;
		} else {
	    	    $result .= "error";
		    ++$error_count;
		    $continue = false;
		}
	    } else {
		$result .= "error (couldn't add column $tablecol)";
		++$error_count;
		$continue = false;
	    }
	    $result .= "<br />\n";
	}

	// insert new option "comment_moderation" to settings	
	if ($continue) {
	    $option = "comment_moderation";
	    $tablename = $tableoptions;
	    $ddl = "INSERT INTO $tablename "
		 . "(option_id, blog_id, option_name, option_can_override, option_type, "
		 . "option_value, option_width, option_height, option_description, "
		 . "option_admin_level) "
		 . "VALUES "
		 . "('0','0','$option','Y','5','none',20,8,'if enabled, comments will only be shown after they have been approved by you',8)";
	    $result .= "Adding new option $option to settings: ";
	    if ($wpdb->query($ddl)) {
		$result .= "ok";
		$continue = true;
	    } else {
		$result .= "error";
		++$error_count;
		$continue = false;
	    }
	    $result .= "<br />\n";
	}

	// attach option to group "General blog settings"
	if ($continue) {
	    // we take over here $option and $tablename from above
	    $group = "General blog settings";
	    $result .= "Inserting new option $option to settings group '$group': ";
	    
	    $oid = $wpdb->get_var("SELECT option_id FROM $tablename WHERE option_name='$option'");	    
	    $gid = $wpdb->get_var("SELECT group_id FROM $tableoptiongroups WHERE group_name='$group'");
	    
	    if (0 != $gid && 0 != $oid) {
		$continue = true;
	    } else {
		$result .= "error (couldn't determine option_id and/or group_id)";
		++$error_count;
		$continue = false;
	    }
	}
	
	if ($continue) {
	    $seq = $wpdb->get_var("SELECT MAX(seq) FROM $tableoptiongroup_options WHERE group_id='$gid'");
	    
	    if (0 != $seq) {
		$continue = true;
	    } else {
		$result .= "error (couldn't determine sequence)";
		++$error_count;
		$continue = false;
	    }
	}
	
	if ($continue) {
	    ++$seq;
	    $ddl = "INSERT INTO $tableoptiongroup_options (group_id, option_id, seq) "
		 . "VALUES ('$gid','$oid','$seq')";
	    if ($wpdb->query($ddl)) {
		$result .= "ok";
	    } else {
		$result .= "error";
		++$error_count;
		$continue = false;
	    }
	    $result .= "<br />\n";
	}

	// insert option values for new option "comment_moderation"
	if ($continue) {
	    $tablename = $tableoptionvalues;
	    $result .= "Inserting option values for new option $option: ";

	    $ddl = array();	    
	    $ddl[] = "INSERT INTO $tablename (option_id, optionvalue, optionvalue_desc, optionvalue_max, optionvalue_min, optionvalue_seq) "
		   . "VALUES ('$oid','none','None',NULL,NULL,1)";
	    $ddl[] = "INSERT INTO $tablename (option_id, optionvalue, optionvalue_desc, optionvalue_max, optionvalue_min, optionvalue_seq) "
		   . "VALUES ('$oid','manual','Manual',NULL,NULL,2)";
	    $ddl[] = "INSERT INTO $tablename (option_id, optionvalue, optionvalue_desc, optionvalue_max, optionvalue_min, optionvalue_seq) "
		   . "VALUES ('$oid','auto','Automatic',NULL,NULL,3)";
		   
	    for ($i = 0; $i < count($ddl); $i++) {
		if ($wpdb->query($ddl[$i])) {
		    $success = true;
		    continue;
		} else {
		    $success = false;
		    break;
		}
	    }
	    
	    if ($success) {
		$result .= "ok";
	    } else {
		$result .= "error";
		++$error_count;
		$continue = false;
	    }
	    $result .= "<br />\n";
	}

	// insert new option "moderation_notify" to settings	
	if ($continue) {
	    $option = "moderation_notify";
	    $tablename = $tableoptions;
	    $ddl = "INSERT INTO $tablename "
		 . "(option_id, blog_id, option_name, option_can_override, option_type, "
		 . "option_value, option_width, option_height, option_description, "
		 . "option_admin_level) "
		 . "VALUES "
		 . "('0','0','$option','Y','2','1',20,8,'set this to true if you want to be notified about new comments that wait for approval',8)";
	    $result .= "Adding new option $option to settings: ";
	    if ($wpdb->query($ddl)) {
		$result .= "ok";
		$continue = true;
	    } else {
		$result .= "error";
		++$error_count;
		$continue = false;
	    }
	    $result .= "<br />\n";
	}

	// attach option to group "General blog settings"
	if ($continue) {
	    // we take over here $option and $tablename from above
	    $group = "General blog settings";
	    $result .= "Inserting new option $option to settings group '$group': ";
	    
	    $oid = $wpdb->get_var("SELECT option_id FROM $tablename WHERE option_name='$option'");	    
	    $gid = $wpdb->get_var("SELECT group_id FROM $tableoptiongroups WHERE group_name='$group'");
	    
	    if (0 != $gid && 0 != $oid) {
		$continue = true;
	    } else {
		$result .= "error (couldn't determine option_id and/or group_id)";
		++$error_count;
		$continue = false;
	    }
	}
	
	if ($continue) {
	    $seq = $wpdb->get_var("SELECT MAX(seq) FROM $tableoptiongroup_options WHERE group_id='$gid'");
	    
	    if (0 != $seq) {
		$continue = true;
	    } else {
		$result .= "error (couldn't determine sequence)";
		++$error_count;
		$continue = false;
	    }
	}
	
	if ($continue) {
	    ++$seq;
	    $ddl = "INSERT INTO $tableoptiongroup_options (group_id, option_id, seq) "
		 . "VALUES ('$gid','$oid','$seq')";
	    if ($wpdb->query($ddl)) {
		$result .= "ok";
	    } else {
		$result .= "error";
		++$error_count;
		$continue = false;
	    }
	    $result .= "<br />\n";
	}

	echo $result;
	
	if ($error_count > 0) {
?>

<p>Hmmm... there was some kind of error. If you cannot figure out
from the output above how to correct the problems please
contact me at <a href="mailto:mrenzmann@otaku42.de">mrenzmann@otaku42.de</a>
and report your problem.</p>
 
<?php
	} else {
?>

<p>Seems that everything went fine. Great!</p>
<p>Now you have two new options in your settings section <em>General blog settings</em>:
<ol><li><em>comment_moderation</em> controls whether you want to use the new comment
moderation functionality at all. If set to <em>manual</em>, you need to approve each
new comment by hand either in the comment moderation panel or when editing the comments
for a post. Choose <em>automatic</em> currently equals <em>manual</em>, but in the near
future this will allow the application of filtering functions (such as URL blacklisting,
keyword filtering, bayesian filtering and similar stuff). To approve awaiting comments
go to <em>Moderate</em> in the admin menu, where all waiting comments will be listed.</li>
<li><em>moderation_notify</em> will decide if you get notified by e-mail as soon as a
new comment has been posted and is waiting for approval (in other words: this setting
only takes effect, if <em>comment_moderation</em> is either set to <em>manual</em> or
<em>automatic</em>. The notification message will contain direct links that allow to
approve or delete a comment, or to jump to the moderation panel.</li></ol>
<p>Have fun!</p>

<?php
	}
    
	break; // end case 1
}
?>

</body>
</html>
