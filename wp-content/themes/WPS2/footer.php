<!-- Global Footer -->
<?php 
	require_once(get_stylesheet_directory() . '/ssi/scripts.html');
?> 

<!-- Version 4 Styling --> 
<?php if(strpos(get_page_template_slug( $post->ID), 'v4') !== False) : ?>

<script> 	  	
	if(document.getElementById('et_pb_ca_banner')){
		var decoration = document.createElement('div');
      		decoration.className = 'header-decoration';
		var banner = document.getElementById('et_pb_ca_banner');
		var banParent = banner.parentElement.parentElement;
		
		document.getElementById('header').appendChild(banner);
      		document.getElementById('header').appendChild(decoration);
      		var div = document.getElementsByClassName('explore-invite')[0].outerHTML = '';
		
		banParent.remove();

	}else{
		document.getElementById('header').removeAttribute("style");
		document.getElementById('header').setAttribute("style","min-height: 0px;");


	}
</script>
<style type="text/css"> 
	div#et_pb_ca_banner {  
		height: 100% !important;
	}
</style > 
<?php else: ?>
<style type="text/css"> 
.et_pb_row {
                position: relative;
                width: 100%;
                max-width: 100%;
                margin: auto;
}
</style>
<?php endif; ?> 


<footer id="footer" class="global-footer"> 
  <div class="container"> <div class="row"> 
    <div class="three-quarters"> 
      <ul class="footer-links"> 
        <li><a href="#skip-to-content">Back to Top</a></li>
         <?php 
      			// if there is a footer menu 
				// loop thru and create a link (parent nav item only)

      			if ( has_nav_menu( 'footer-menu')) {
                  $menu_name = 'footer-menu';
                  $locations = get_nav_menu_locations();
                  $menu = wp_get_nav_menu_object( $locations[ $menu_name]);
                  $menuitems = wp_get_nav_menu_items( $menu->term_id, array( 'order'=> 'DESC'));
                  foreach ( $menuitems as $item) {
                      if($item->menu_item_parent == 0) {
                          print '<li><a href="' . $item->url. '">' . $item->title;
                          print '</a></li>';
                      }
                  }
				}
			?> 
      </ul> 
    </div>
   <div class="quarter text-right"> 
     <ul class="socialsharer-container"> 
       	<li><a href="https://www.flickr.com/groups/californiagovernment">
          <span class="ca-gov-icon-flickr" aria-hidden="true"></span>
          <span class="sr-only">Flickr</span></a></li> 
       <li><a href="https://www.pinterest.com/cagovernment/">
         <span class="ca-gov-icon-pinterest" aria-hidden="true"></span>
         <span class="sr-only">Pinterest</span></a></li> 
       <li><a href="https://twitter.com/cagovernment">
         <span class="ca-gov-icon-twitter" aria-hidden="true"></span>
         <span class="sr-only">Twitter</span></a></li>
       <li><a href="https://www.youtube.com/user/californiagovernment">
         <span class="ca-gov-icon-youtube" aria-hidden="true"></span>
         <span class="sr-only">YouTube</span></a></li> 
     </ul> 
    </div> 
    </div>
  </div> <!-- Copyright Statement --> 
  <div class="copyright"> 
    <div class="container"> Copyright &copy;
<script>document.write(new Date().getFullYear())</script> State of California </div> </div> </footer> <!-- Extra Decorative Content --> <div class="decoration-last">&nbsp;
</div>