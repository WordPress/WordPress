<?php
$title = 'Categories';
/* <Categories> */

function add_magic_quotes($array) {
    foreach ($array as $k => $v) {
        if (is_array($v)) {
            $array[$k] = add_magic_quotes($v);
        } else {
            $array[$k] = addslashes($v);
        }
    }
    return $array;
}

if (!get_magic_quotes_gpc()) {
    $HTTP_GET_VARS    = add_magic_quotes($HTTP_GET_VARS);
    $HTTP_POST_VARS   = add_magic_quotes($HTTP_POST_VARS);
    $HTTP_COOKIE_VARS = add_magic_quotes($HTTP_COOKIE_VARS);
}

$wpvarstoreset = array('action','standalone','cat');
for ($i=0; $i<count($wpvarstoreset); $i += 1) {
    $wpvar = $wpvarstoreset[$i];
    if (!isset($$wpvar)) {
        if (empty($HTTP_POST_VARS["$wpvar"])) {
            if (empty($HTTP_GET_VARS["$wpvar"])) {
                $$wpvar = '';
            } else {
                $$wpvar = $HTTP_GET_VARS["$wpvar"];
            }
        } else {
            $$wpvar = $HTTP_POST_VARS["$wpvar"];
        }
    }
}

switch($action) {

case 'addcat':

    $standalone = 1;
    require_once('admin-header.php');
    
    if ($user_level < 3)
        die ('Cheatin&#8217; uh?');
    
    $cat_name= addslashes(stripslashes(stripslashes($HTTP_POST_VARS['cat_name'])));
    $category_nicename = sanitize_title($cat_name);
    $category_description = addslashes(stripslashes(stripslashes($HTTP_POST_VARS['category_description'])));
    
    $wpdb->query("INSERT INTO $tablecategories (cat_ID, cat_name, category_nicename, category_description, category_parent) VALUES ('0', '$cat_name', '$category_nicename', '$category_description', $cat)");
    
    header('Location: categories.php');

break;

case 'Delete':

    $standalone = 1;
    require_once('admin-header.php');

    $cat_ID = intval($HTTP_GET_VARS["cat_ID"]);
    $cat_name = get_catname($cat_ID);
    $cat_name = addslashes($cat_name);

    if (1 == $cat_ID)
        die("Can't delete the <strong>$cat_name</strong> category: this is the default one");

    if ($user_level < 3)
        die ('Cheatin&#8217; uh?');

    $wpdb->query("DELETE FROM $tablecategories WHERE cat_ID = $cat_ID");
    $wpdb->query("UPDATE $tablepost2cat SET category_id='1' WHERE category_id='$cat_ID'");

    header('Location: categories.php');

break;

case 'edit':

    require_once ('admin-header.php');
    $category = $wpdb->get_row("SELECT * FROM $tablecategories WHERE cat_ID = " . $HTTP_GET_VARS['cat_ID']);
    $cat_name = stripslashes($category->cat_name);
    ?>

<div class="wrap">
    <h2>Edit Category</h2>
    <form name="editcat" action="categories.php" method="post">
        <input type="hidden" name="action" value="editedcat" />
        <input type="hidden" name="cat_ID" value="<?php echo $HTTP_GET_VARS['cat_ID'] ?>" />
        <p>Category name:<br />
        <input type="text" name="cat_name" value="<?php echo $cat_name; ?>" /></p>
        <p>Category parent:<br />
        <?php dropdown_cats(FALSE, '', 'name', 'asc', FALSE, FALSE, FALSE, TRUE, $category->category_parent, $HTTP_GET_VARS['cat_ID']); ?></p>
        <p>Description:<br />
        <textarea name="category_description" rows="5" cols="50" style="width: 97%;"><?php echo htmlentities($category->category_description); ?></textarea></p>
        <p><input type="submit" name="submit" value="Edit it!" class="search" /></p>
    </form>
</div>

    <?php

break;

case 'editedcat':

    $standalone = 1;
    require_once('admin-header.php');

    if ($user_level < 3)
        die ('Cheatin&#8217; uh?');
    
    $cat_name = addslashes(stripslashes(stripslashes($HTTP_POST_VARS['cat_name'])));
    $cat_ID = addslashes($HTTP_POST_VARS['cat_ID']);
    $category_nicename = sanitize_title($cat_name);
    $category_description = $HTTP_POST_VARS['category_description'];

    $wpdb->query("UPDATE $tablecategories SET cat_name = '$cat_name', category_nicename = '$category_nicename', category_description = '$category_description', category_parent = $cat WHERE cat_ID = $cat_ID");
    
    header('Location: categories.php');

break;

default:

    $standalone = 0;
    require_once ('admin-header.php');
    if ($user_level < 3) {
        die("You have no right to edit the categories for this blog.<br />Ask for a promotion to your <a href='mailto:$admin_email'>blog admin</a>. :)");
    }
    ?>

<div class="wrap">
    <h2>Current Categories</h2>
    <table width="100%" cellpadding="3" cellspacing="3">
    <tr>
        <th scope="col">Name</th>
        <th scope="col">Parent</th>
        <th scope="col">Description</th>
        <th scope="col"># Posts</th>
        <th colspan="2">Action</th>
    </tr>
    <?php
    $categories = $wpdb->get_results("SELECT * FROM $tablecategories ORDER BY cat_name");
    foreach ($categories as $category) {
        $parent = "None";
        if ($category->category_parent) $parent = $wpdb->get_var("SELECT cat_name FROM $tablecategories WHERE cat_ID = $category->category_parent");
        $count = $wpdb->get_var("SELECT COUNT(post_id) FROM $tablepost2cat WHERE category_id = $category->cat_ID");
        $bgcolor = ('#eee' == $bgcolor) ? 'none' : '#eee';
        echo "<tr style='background-color: $bgcolor'><td>$category->cat_name</td>
        <td>$parent</td>
        <td>$category->category_description</td>
        <td>$count</td>
        <td><a href='categories.php?action=edit&amp;cat_ID=$category->cat_ID' class='edit'>Edit</a></td><td><a href='categories.php?action=Delete&amp;cat_ID=$category->cat_ID' onclick=\"return confirm('You are about to delete the category \'". addslashes($category->cat_name) ."\' and all its posts will go to the default category.\\n  \'OK\' to delete, \'Cancel\' to stop.')\" class='delete'>Delete</a></td>
        </tr>";
    }
    ?>
    </table>

</div>
<div class="wrap">
    <h2>Add New Category</h2>
    <form name="addcat" action="categories.php" method="post">
        
        <p>Name:<br />
        <input type="text" name="cat_name" value="" /></p>
        <p>Category parent:<br />
        <?php dropdown_cats(FALSE, '', 'name', 'asc', FALSE, FALSE, FALSE, TRUE); ?></p>
        <p>Description: (optional) <br />
        <textarea name="category_description" rows="5" cols="50" style="width: 97%;"></textarea></p>
        <p><input type="hidden" name="action" value="addcat" /><input type="submit" name="submit" value="Add" class="search" /></p>
    </form>
</div>


<div class="wrap">
  <p><strong>Note:</strong><br />
    Deleting a category does not delete posts from that category, it will just
    set them back to the default category <strong><?php echo get_catname(1) ?></strong>.
  </p>
</div>

    <?php
break;
}

/* </Categories> */
include('admin-footer.php');
?> 