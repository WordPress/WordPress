<h1 id="wphead"><a href="http://wordpress.org" rel="external"><span>WordPress</span></a></h1> 
<ul id="adminmenu">
  <li><a href="b2edit.php"><strong>Post / Edit</strong></a></li>
  <li><a href="javascript:profile(<?php echo $user_ID ?>)">My Profile</a></li>
  <li><a href="b2team.php">Team</a></li>
    <?php

if ($pagenow != "b2profile.php") {

$menu = file("./b2menutop.txt");
$i=0;
$j=$menu[0];
while ($j != "") {
	$k = explode("\t",$j);
	if ($user_level >= $k[0]) {
		echo "\n<li><a href='".$k[1]."'>".trim($k[2]).'</a></li>';
	}
	$i=$i+1;
	$j=$menu[$i];
	if (trim($j) == "***")
		$j="";
}

}
?>
<li><a href="<?php echo $siteurl."/".$blogfilename; ?>">View site</a></li>
<li><a href="<?php echo $siteurl ?>/b2login.php?action=logout">Logout</a></li>
</ul>

<h2><?php echo $title; ?></h2>
