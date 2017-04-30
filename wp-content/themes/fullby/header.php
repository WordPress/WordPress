<!DOCTYPE html>
<html  <?php language_attributes();?>>
  <head>
    <meta charset="utf-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?php wp_title('&raquo;','true','right'); ?><?php bloginfo('name'); ?></title>
    <meta name="description" content="<?php echo get_option('fullby_description'); ?>" />
    
    <!-- Favicon -->
    <link rel="icon" href="<?php bloginfo('stylesheet_directory'); ?>/img/favicon.png" type="image/x-icon"> 

    <!-- Bootstrap core CSS -->
    <link href="<?php echo get_stylesheet_directory_uri(); ?>/css/bootstrap.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo get_stylesheet_directory_uri(); ?>/font-awesome/css/font-awesome.min.css">

    <!-- Custom styles for this template -->
    <link href="<?php echo get_stylesheet_directory_uri(); ?>/style.css" rel="stylesheet">
  
    
    <!-- Google web Font -->
    <link href='http://fonts.googleapis.com/css?family=Lato:300,400,700,900,100' rel='stylesheet' type='text/css'>

    <!-- Just for debugging purposes. Don't actually copy this line! -->
    <!--[if lt IE 9]><script src="../../docs-assets/js/ie8-responsive-file-warning.js"></script><![endif]-->

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
    
    <!-- Analitics -->
	<?php if (get_option('fullby_analytics') <> "") { echo get_option('fullby_analytics'); } ?>
    
	<?php wp_head(); ?> 
	
</head>
<body <?php body_class(); ?>>

    <div class="navbar navbar-inverse navbar-fixed-top">
     
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#mainmenu">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <h1><a class="navbar-brand" href="<?php echo home_url(); ?>"><i class="fa fa-chevron-right"></i><i class="fa fa-chevron-right arrow"></i> <?php bloginfo('name'); ?></a></h1>
        </div>
        
        <div id="mainmenu" class="collapse navbar-collapse">
          <?php /* Primary navigation */
			wp_nav_menu( array(
			  'menu' => 'main',
			  'depth' => 2,
			  'container' => false,
			  'menu_class' => 'nav navbar-nav navbar-right',
			  //Process nav menu using our custom nav walker
			  'walker' => new wp_bootstrap_navwalker())
			);
			?>
        </div><!--/.nav-collapse -->
    
    </div>
    
    <?php if (is_home()) { ?>
    
    	 <?php if (!is_paged()){ ?> 
    
	    	 <div class="row featured">
    
					<?php
					$specialPosts = new WP_Query();
					$specialPosts->query('tag=featured&showposts=3');
					?>
					
					<?php if ($specialPosts->have_posts()) : while($specialPosts->have_posts()) : $specialPosts->the_post(); ?>
			  
					    <div class="col-sm-4 col-md-4 item-featured">
					    
					    	
							<a href="<?php the_permalink(); ?>">
				
					    		<div class="caption">
						    		<div class="cat"><span><?php $category = get_the_category(); echo $category[0]->cat_name; ?></span></div>
						    		<div class="date"><i class="fa fa-clock-o"></i> <?php the_time('j M , Y') ?> &nbsp;
						    		
						    			<?php 
										$video = get_post_meta($post->ID, 'fullby_video', true );
										
										if($video != '') { ?>
						             			
						             		<i class="fa fa-video-camera"></i> Video
						             			
						             	<?php } else if (strpos($post->post_content,'[gallery') !== false) { ?>
						             			
						             		<i class="fa fa-th"></i> Gallery
			
					             		<?php } else {?>
		
					             		<?php } ?>

						    		
						    		</div>
						    		
						    		<h2 class="title"><?php the_title(); ?></h2>
						    		
					    		</div>

				                <?php $video = get_post_meta($post->ID, 'fullby_video', true );
					  
								if($video != '') {?>
					
									 <img class="yt-featured" src="http://img.youtube.com/vi/<?php echo $video ?>/hqdefault.jpg" class="grid-cop"/>
										
								<?php 				                 
		                   
				             	} else if ( has_post_thumbnail() ) { ?>
			
			                        <?php the_post_thumbnail('quad', array('class' => 'quad')); ?>
			                        				   
			                    <?php } ?>
						    	
						    </a>
						
						</div>
					
					<?php endwhile;  else : ?>
			
						<p>Sorry, no posts matched your criteria.</p>

					<?php endif; ?>	
				 		
				</div>	
				
			<?php } else { ?>
			
				<div class="row spacer"></div>	
			
			<?php } // end if(!is_paged) ?>
				
	<?php } else { ?>	
	
		<div class="row spacer"></div>		   
			
	<?php  } // end if(is_home) ?>
	
	<div class="navbar navbar-inverse navbar-sub">
     
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#submenu">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          
        </div>
        
        <div  id="submenu" class="collapse navbar-collapse">
          <?php /* Secondary navigation */
			wp_nav_menu( array(
			  'menu' => 'sub',
			  'depth' => 2,
			  'container' => false,
			  'menu_class' => 'nav navbar-nav',
			  //Process nav menu using our custom nav walker
			  'walker' => new wp_bootstrap_navwalker())
			);
			?>
			
			<div class="col-sm-3 col-md-3 pull-right search-cont">
		        <form class="navbar-form" role="search" method="get" action="<?php echo home_url() ; ?>">
			        <div class="input-group">
			            <input type="text" class="form-control" placeholder="Search" name="s" id="srch-term">
			            <div class="input-group-btn">
			                <button class="btn btn-default" type="submit"><i class="glyphicon glyphicon-search"></i></button>
			            </div>
			        </div>
		        </form>
	        </div>
	        
        </div><!--/.nav-collapse -->

	</div>