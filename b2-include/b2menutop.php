<img src="b2-img/blank.gif" width="1" height="5" alt="" border="0" />
<table width="100%" border="0" cellpadding="0" cellspacing="0">
<tr height="15">

<td height="15" width="20"><img src="b2-img/blank.gif" width="1" height="1" alt="" /></td>

<td rowspan="3" width="50" valign="top"><a href="http://cafelog.com/"><img src="b2-img/b2minilogo.png" width="50" height="50" border="0" alt="visit b2's website" style="border-width:1px; border-color: #999999; border-style: dashed" /></a></td>

<td><img src="b2-img/blank.gif" width="1" height="1" alt="" /></td>
<td width="150" style="text-align: right; padding-rightt: 6px;">
<span style="color: #b0b0b0; font-family: verdana, arial, helvetica; font-size: 10px;">logged in as : <b><?php echo $user_login; ?></b></span>
</td>

</tr>
<tr>

<td class="menutop" width="20">&nbsp;
</td>

<td class="menutop"<?php if ($is_NS4) { echo " width=\"500\""; } ?>>
<div class="menutop"<?php if ($is_NS4) { echo " width=\"500\""; } ?>>


<?php if ($is_NS4) { echo $HTTP_USER_AGENT; } ?>


<?php $sep = "&nbsp;&nbsp;|&nbsp;&nbsp;"; ?>
&nbsp;<a href="b2edit.php" class="menutop" style="-font-weight: bold;">Post / Edit</a><?php echo $sep ?><a href="javascript:profile(<?php echo $user_ID ?>)" class="menutop">My Profile</a><?php echo $sep ?><a href="b2team.php" class="menutop">Team</a><?php

if ($pagenow != "b2profile.php") {

$menu = file($b2inc."/b2menutop.txt");
$i=0;
$j=$menu[0];
while ($j != "") {
	$k = explode("\t",$j);
	if ($user_level >= $k[0]) {
		echo "$sep<a href=\"".$k[1]."\" class=\"menutop\">".trim($k[2])."</a>";
	}
	$i=$i+1;
	$j=$menu[$i];
	if (trim($j) == "***")
		$j="";
}

}
?>

</div>
</td>

<td width="150" class="menutop" align="right" bgcolor="#FF9900">
<a href="<?php echo $siteurl."/".$blogfilename; ?>" class="menutop">View site</a>
<?php echo $sep; ?>
<a href="b2login.php?action=logout" class="menutop">Logout</a>
</td>

</tr>
<tr>

<td>&nbsp;</td>
<td style="padding-left: 6px;"><span class="menutoptitle">:: <?php echo $title; ?></span></td>
<td>&nbsp;</td>

</tr>

</table>