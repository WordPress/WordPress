<?php
/**
 * The template for displaying Archive pages.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package Twenty Minutes
 */

get_header(); ?>

<div class="container">
   <div id="content" class="contentsecwrap">
      <header class="page-header">
        <?php
            the_archive_title( '<h1 class="entry-title">', '</h1>' );
            the_archive_description( '<div class="taxonomy-description">', '</div>' );
            ?>
        <span><?php twenty_minutes_the_breadcrumb(); ?></span>
      </header>
         <?php
         $twenty_minutes_sidebar_layout = get_theme_mod( 'twenty_minutes_sidebar_post_layout','right');
         if($twenty_minutes_sidebar_layout == 'left'){ ?>
         <div class="row m-0">
            <div class="col-lg-4 col-md-4" id="sidebar"><?php get_sidebar();?></div>
            <div class="col-lg-8 col-md-8">
               <div class="postsec-list">
                  <?php if ( have_posts() ) : ?>                        
                     <div class="postsec-list">
                        <?php /* Start the Loop */ ?>
                               <?php while ( have_posts() ) : the_post(); ?>
                                   <?php get_template_part( 'template-parts/post/content' ); ?>
                               <?php endwhile; ?>
                           </div>
                           <?php the_posts_pagination(); ?>
                       <?php else : ?>
                           <?php get_template_part( 'no-results', 'archive' ); ?>
                       <?php endif; ?>
                  <div class="clearfix"></div>
               </div>
            </div>
         </div>
         <div class="clearfix"></div>
         <?php }else if($twenty_minutes_sidebar_layout == 'right'){ ?>
            <div class="row m-0">
               <div class="col-lg-8 col-md-8">
                  <div class="postsec-list">
                     <?php if ( have_posts() ) : ?>                        
                        <div class="postsec-list">
                           <?php /* Start the Loop */ ?>
                              <?php while ( have_posts() ) : the_post(); ?>
                                <?php get_template_part( 'template-parts/post/content' ); ?>
                              <?php endwhile; ?>
                        </div>
                        <?php the_posts_pagination(); ?>
                        <?php else : ?>
                        <?php get_template_part( 'no-results', 'archive' ); ?>
                     <?php endif; ?>
                  </div>
               </div>
               <div class="col-lg-4 col-md-4" id="sidebar"><?php get_sidebar();?></div>
            </div>
         <?php }else if($twenty_minutes_sidebar_layout == 'full'){ ?>
            <div class="full">
              <div class="postsec-list">
                 <?php if ( have_posts() ) : ?>                        
                    <div class="postsec-list">
                       <?php /* Start the Loop */ ?>
                       <?php while ( have_posts() ) : the_post(); ?>
                       <?php get_template_part( 'template-parts/post/content' ); ?>
                       <?php endwhile; ?>
                    </div>
                    <?php the_posts_pagination(); ?>
                      <?php else : ?>
                       <?php get_template_part( 'no-results', 'archive' ); ?>
                      <?php endif; ?>
                 <div class="clearfix"></div>
              </div>
            </div>
         <?php }else if($twenty_minutes_sidebar_layout == 'three-column'){ ?>
            <div class="row m-0">
               <div class="col-lg-3 col-md-3" id="sidebar"><?php dynamic_sidebar('sidebar-1');?></div>
               <div class="col-lg-6 col-md-6">
                  <div class="postsec-list">
                     <?php if ( have_posts() ) : ?>                        
                           <div class="postsec-list">
                              <?php /* Start the Loop */ ?>
                                     <?php while ( have_posts() ) : the_post(); ?>
                                         <?php get_template_part( 'template-parts/post/content' ); ?>
                                     <?php endwhile; ?>
                                 </div>
                                 <?php the_posts_pagination(); ?>
                             <?php else : ?>
                                 <?php get_template_part( 'no-results', 'archive' ); ?>
                             <?php endif; ?>
                     <div class="clearfix"></div>
                  </div>
               </div>
               <div class="col-lg-3 col-md-3" id="sidebar"><?php dynamic_sidebar('sidebar-2');?></div>
            </div>
         <?php }else if($twenty_minutes_sidebar_layout == 'four-column'){ ?>
            <div class="row m-0">
               <div class="col-lg-3 col-md-3" id="sidebar"><?php dynamic_sidebar('sidebar-1');?></div>
               <div class="col-lg-3 col-md-3">
                  <div class="postsec-list four-col">
                     <?php if ( have_posts() ) : ?>                        
                           <div class="postsec-list">
                              <?php /* Start the Loop */ ?>
                                     <?php while ( have_posts() ) : the_post(); ?>
                                         <?php get_template_part( 'template-parts/post/content' ); ?>
                                     <?php endwhile; ?>
                                 </div>
                                 <?php the_posts_pagination(); ?>
                             <?php else : ?>
                                 <?php get_template_part( 'no-results', 'archive' ); ?>
                             <?php endif; ?>
                  <div class="clearfix"></div>
                  </div>
               </div>
               <div class="col-lg-3 col-md-3" id="sidebar"><?php dynamic_sidebar('sidebar-2');?></div>
               <div class="col-lg-3 col-md-3" id="sidebar"><?php dynamic_sidebar('sidebar-3');?></div>
            </div>
         <?php }else if($twenty_minutes_sidebar_layout == 'grid'){ ?>
            <div class="row m-0">
               <div class="col-lg-9 col-md-9">
                  <div class="row">
                      <?php if ( have_posts() ) : ?>    
                        <?php /* Start the Loop */ ?>
                          <?php while ( have_posts() ) : the_post(); ?>
                              <?php get_template_part( 'template-parts/post/content-grid' ); ?>
                          <?php endwhile; ?>
                          <?php the_posts_pagination(); ?>
                            <?php else : ?>
                            <?php get_template_part( 'no-results', 'archive' ); ?>
                      <?php endif; ?>
                  </div>
                </div>
                <div class="col-lg-3 col-md-3" id="sidebar"><?php get_sidebar();?></div>
            </div>
         <?php }else {?>
            <div class="row m-0">
               <div class="col-lg-8 col-md-8">
                  <div class="postsec-list">
                     <?php if ( have_posts() ) : ?>                        
                        <div class="postsec-list">
                           <?php /* Start the Loop */ ?>
                                  <?php while ( have_posts() ) : the_post(); ?>
                                      <?php get_template_part( 'template-parts/post/content' ); ?>
                                  <?php endwhile; ?>
                              </div>
                              <?php the_posts_pagination(); ?>
                          <?php else : ?>
                              <?php get_template_part( 'no-results', 'archive' ); ?>
                          <?php endif; ?>
                     <div class="clearfix"></div>
                  </div>
               </div>
               <div class="col-lg-4 col-md-4" id="sidebar"><?php get_sidebar();?></div>
            </div>
         <?php } ?>
   </div>
</div>
<?php get_footer();