<?php
   // This sample file provided by Loyd Goodbar http://www.blackrobes.net/
   $blog=1;
   include("./blog.header.php");
   include_once("./links.php");
   $query = "SELECT cat_id, cat_name FROM $tablelinkcategories ORDER BY cat_name";
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <meta http-equiv="Expires" content="0" />
  <meta http-equiv="Pragma" content="no-cache" />
  <meta http-equiv="Cache-control" content="no-cache" />
  <title><?php bloginfo('name') ?> :: Links</title>
  <link rel="stylesheet" type="text/css" href="/layout2b.css" />
</head>
<body>

<div id="header">
  <a href="" title="<?php bloginfo('name'); ?>"><?php bloginfo('name'); ?> :: Links</a>
</div>

<table align="center" columns="1" width="600">
  <tr>
    <td>
<?php
  /* motor for link categories, then show all links therein */
  $result = mysql_query($query) or die("Could not retrieve list of link categories.");
  while ($row = mysql_fetch_array($result)) {
?>
      <h2><?php echo $row['cat_name']; ?></h2>
      <p class="storyContent"><?php get_links($row['cat_id'],'','<br />',' ',true,'name',true,false); ?></p>
<?php
  }
?>
    </td>
  </tr>
</table>

<p>
<?php
  if (isset($HTTP_GET_VARS['popup'])) {
    echo('<a href="javascript:window.close()">Close this window</a>');
  } else {
    echo('<a href="'.$siteurl.'/'.$blogfilename.'">Return home</a>');
  }
?>
</p>

</body>
</html>