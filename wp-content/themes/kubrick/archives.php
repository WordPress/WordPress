<?php /*
			 Template Name: Archives
			*/
?>
<?php include "header.php"; ?>

<div id="content" class="widecolumn">

<?php include "searchform.php"; ?>

<h2>Archives by Month:</h2>
  <ul>
    <?php wp_get_archives('type=monthly'); ?>
  </ul>

<h2>Archives by Subject:</h2>
  <ul>
     <?php wp_list_cats(); ?>
  </ul>

</div>	

<?php include "footer.php"; ?>