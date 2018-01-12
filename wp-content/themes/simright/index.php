<?php get_header(); ?>
<section class="main-box video-list">
    <div class="banner">
        <h2 class="text-center">ABOUT US</h2>
        <P>Simulation in your browser</P>
        <P>Accessible,Cost-efficient,For everyone</P>
    </div>
    <section class="contain-box">
        <div class="slide-bar">
            <form class="neck-bar-search">
                <div class="form-group input-group">
                    <input type="text" class="form-control" placeholder="Search">
                    <button type="submit" class="input-group-addon"><i class="glyphicon glyphicon-search"></i></button>
                </div>
            </form>
            <?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('Viedeo_list_classification') ) : ?>
                <ul>
                    <?php wp_list_categories('depth=1&title_li=&orderby=id&show_count=1&hide_empty=1&child_of=0'); ?>
                </ul>
            <?php endif; ?>
        </div>
        <div class="contain-body">
            <section class="list-item">
                <h2>LATEST</h2>
                <hr/>
                <div class="list-contain">
                    <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
                    <div class="video-item">
                        <a href="<?php the_permalink(); ?>">
                            <span><?php the_title(); ?></span>
                            <p><?php the_time('Y-m-d') ?></p>
                        </a>
                    </div>
                    <?php endwhile; ?>
                    <?php else : ?>
                        <h3 class="title"><a href="#" rel="bookmark">NOT FOUND</a></h3>
                    <?php endif; ?>
                </div>
            </section>
        </div>
    </section>
</section>
<?php get_footer(); ?>