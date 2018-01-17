<form class="neck-bar-search">
    <div class="form-group input-group">
        <input type="text" name="s" id="search" value="<?php the_search_query(); ?>" placeholder="Search" class="form-control" />
        <input type="hidden" name="cat" value="<?php get_category_root_id(the_category_ID(false)); ?>" />
        <button type="submit" class="input-group-addon"><i class="glyphicon glyphicon-search"></i></button>
    </div>
</form>