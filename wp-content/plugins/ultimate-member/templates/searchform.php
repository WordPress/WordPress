<form role="search" method="get" class="search-form um-search-form" action="<?php echo  esc_url( um_get_core_page( 'members' ) ); ?>">
	<input type="hidden" name="um_search" value="1">
    <div class="um-search-area">
        <span class="screen-reader-text"><?php echo _x( 'Search for:', 'label' ); ?></span>
        <input type="search" class="um-search-field search-field" placeholder="<?php echo esc_attr_x( 'Search &hellip;', 'placeholder' ); ?>" value="<?php echo um_get_search_query(); ?>" name="search" title="<?php echo esc_attr_x( 'Search for:', 'label' ); ?>" />
        <a href="javascript: void(0);" id="um-search-button" class="um-search-icon um-faicon um-faicon-search"></a>
    </div>
</form>
