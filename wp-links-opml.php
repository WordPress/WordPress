<?php 
$blog = 1; // Your blog's ID
$doing_rss = 1;
header('Content-type: text/xml', true);
require('wp-blog-header.php');

$link_cat = $HTTP_GET_VARS['link_cat'];
if ((empty($link_cat)) || ($link_cat == 'all') || ($link_cat == '0')) {
    $sql_cat = '';
} else { // be safe
    $link_cat = ''.urldecode($link_cat).'';
    $link_cat = addslashes_gpc($link_cat);
    $link_cat = intval($link_cat);
    if ($link_cat != 0) {
        $sql_cat = "AND $tablelinks.link_category = $link_cat";
        $cat_name = $wpdb->get_var("SELECT $tablelinkcategories.cat_name FROM $tablelinkcategories WHERE $tablelinkcategories.cat_id = $link_cat");
        if (!empty($cat_name)) {
            $cat_name = ": category $cat_name";
        }
    }
}
?><?php echo "<?xml version=\"1.0\"?".">\n"; ?>
<!-- generator="wordpress/<?php echo $wp_version ?>" -->
<opml version="1.0">
    <head>
        <title>Links for <?php echo get_bloginfo('name').$cat_name ?></title>
        <ownerName><?php echo antispambot(get_bloginfo('admin_email')) ?></ownerName>
        <ownerEmail><?php echo antispambot(get_bloginfo('admin_email')) ?></ownerEmail>
        <dateCreated><?php echo gmdate("D, d M Y H:i:s"); ?> GMT</dateCreated>
    </head>
    <body>
<?php $sql = "SELECT $tablelinks.link_url, link_rss, $tablelinks.link_name, $tablelinks.link_category, $tablelinkcategories.cat_name 
FROM $tablelinks 
 LEFT JOIN $tablelinkcategories on $tablelinks.link_category = $tablelinkcategories.cat_id
 $sql_cat
 ORDER BY $tablelinkcategories.cat_name, $tablelinks.link_name \n";
 //echo("<!-- $sql -->");
 $prev_cat_id = 0;
 $results = $wpdb->get_results($sql);
 if ($results) {
     foreach ($results as $result) {
         if ($result->link_category != $prev_cat_id) { // new category
             if ($prev_cat_id != 0)  { // not first time
?>
        </outline>
<?php
             } // end if not first time
?>
        <outline type="category" title="<?php echo(htmlspecialchars(stripslashes($result->cat_name))) ?>">
<?php
             $prev_cat_id = $result->link_category;
        } // end if new category
?>
            <outline title="<?php echo(htmlspecialchars(stripslashes($result->link_name))) ?>" type="link" xmlUrl="<?php echo $result->link_rss; ?>" htmlUrl="<?php echo($result->link_url) ?>"/>
<?php
        } // end foreach
    } // end if
?>
        </outline>
    </body>
</opml>