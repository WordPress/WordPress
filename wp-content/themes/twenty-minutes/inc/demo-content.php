<div class="theme-offer">
   <?php
        // Check if the demo import has been completed
        $twenty_minutes_demo_import_completed = get_option('twenty_minutes_demo_import_completed', false);

        // If the demo import is completed, display the "View Site" button
        if ($twenty_minutes_demo_import_completed) {
            echo '<br>';
            echo '<div class="success">Demo Import Successful</div>';
            echo '<br>';
            echo '<hr>';
            echo '<br>';
            echo '<span>' . esc_html__( 'You can now visit your site or customize it further.', 'twenty-minutes' ) . '</span>';
            echo '<br>';
            echo '<br>';
            echo '<br>';
            echo '<div class="view-site-btn">';
            echo '<a href="' . esc_url(home_url()) . '" class="button button-primary button-large" style="margin-top: 10px;" target="_blank">View Site</a>';
            echo '<a href="' . esc_url( admin_url('customize.php') ) . '" class="button button-primary button-large" style="margin-top: 10px;" target="_blank">Customize Demo Content</a>';
            echo '</div>';
        }
    if ( isset( $_POST['submit'] ) ) {

        echo '<div class="plugin-notice">';
            // Check if Classic Blog Grid plugin is installed
            if (!is_plugin_active('classic-blog-grid/classic-blog-grid.php')) {
                // Plugin slug and file path for Classic Blog Grid
                $twenty_minutes_plugin_slug = 'classic-blog-grid';
                $twenty_minutes_plugin_file = 'classic-blog-grid/classic-blog-grid.php';
            
                // Check if Classic Blog Grid is installed and activated
                if ( ! is_plugin_active( $twenty_minutes_plugin_file ) ) {
            
                    // Check if Classic Blog Grid is installed
                    $twenty_minutes_installed_plugins = get_plugins();
                    if ( ! isset( $twenty_minutes_installed_plugins[ $twenty_minutes_plugin_file ] ) ) {
            
                        // Include necessary files to install plugins
                        include_once( ABSPATH . 'wp-admin/includes/plugin-install.php' );
                        include_once( ABSPATH . 'wp-admin/includes/file.php' );
                        include_once( ABSPATH . 'wp-admin/includes/misc.php' );
                        include_once( ABSPATH . 'wp-admin/includes/class-wp-upgrader.php' );
            
                        // Download and install Classic Blog Grid
                        $twenty_minutes_upgrader = new Plugin_Upgrader();
                        $twenty_minutes_upgrader->install( 'https://downloads.wordpress.org/plugin/classic-blog-grid.latest-stable.zip' );
                    }
            
                    // Activate the Classic Blog Grid plugin after installation (if needed)
                    activate_plugin( $twenty_minutes_plugin_file );
                }
            }
        echo '</div>';
        
        // ------- Create Main Menu --------
        $twenty_minutes_menuname = 'Primary Menu';
        $twenty_minutes_bpmenulocation = 'primary';
        $twenty_minutes_menu_exists = wp_get_nav_menu_object( $twenty_minutes_menuname );
    
        if ( !$twenty_minutes_menu_exists ) {
            $twenty_minutes_menu_id = wp_create_nav_menu( $twenty_minutes_menuname );

            // Create Home Page
            $twenty_minutes_home_title = 'Home';
            $twenty_minutes_home = array(
                'post_type'    => 'page',
                'post_title'   => $twenty_minutes_home_title,
                'post_content' => '',
                'post_status'  => 'publish',
                'post_author'  => 1,
                'post_slug'    => 'home'
            );
            $twenty_minutes_home_id = wp_insert_post($twenty_minutes_home);
            // Assign Home Page Template
            add_post_meta($twenty_minutes_home_id, '_wp_page_template', '/template-home-page.php');
            // Update options to set Home Page as the front page
            update_option('page_on_front', $twenty_minutes_home_id);
            update_option('show_on_front', 'page');
            // Add Home Page to Menu
            wp_update_nav_menu_item($twenty_minutes_menu_id, 0, array(
                'menu-item-title' => __('Home', 'twenty-minutes'),
                'menu-item-classes' => 'home',
                'menu-item-url' => home_url('/'),
                'menu-item-status' => 'publish',
                'menu-item-object-id' => $twenty_minutes_home_id,
                'menu-item-object' => 'page',
                'menu-item-type' => 'post_type'
            ));

            // Create a new Page 
            $twenty_minutes_pages_title = 'Pages';
            $twenty_minutes_pages_content = '<p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry standard dummy text ever since the 1500, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960 with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.</p>';
            $twenty_minutes_pages = array(
                'post_type'    => 'page',
                'post_title'   => $twenty_minutes_pages_title,
                'post_content' => $twenty_minutes_pages_content,
                'post_status'  => 'publish',
                'post_author'  => 1,
                'post_slug'    => 'pages'
            );
            $twenty_minutes_pages_id = wp_insert_post($twenty_minutes_pages);
            // Add Pages Page to Menu
            wp_update_nav_menu_item($twenty_minutes_menu_id, 0, array(
                'menu-item-title' => __('Pages', 'twenty-minutes'),
                'menu-item-classes' => 'pages',
                'menu-item-url' => home_url('/pages/'),
                'menu-item-status' => 'publish',
                'menu-item-object-id' => $twenty_minutes_pages_id,
                'menu-item-object' => 'page',
                'menu-item-type' => 'post_type'
            ));

            // Create About Us Page 
            $twenty_minutes_about_title = 'About Us';
            $twenty_minutes_about_content = '<p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry standard dummy text ever since the 1500, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960 with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.</p>';
            $twenty_minutes_about = array(
                'post_type'    => 'page',
                'post_title'   => $twenty_minutes_about_title,
                'post_content' => $twenty_minutes_about_content,
                'post_status'  => 'publish',
                'post_author'  => 1,
                'post_slug'    => 'about-us'
            );
            $twenty_minutes_about_id = wp_insert_post($twenty_minutes_about);
            // Add About Us Page to Menu
            wp_update_nav_menu_item($twenty_minutes_menu_id, 0, array(
                'menu-item-title' => __('About Us', 'twenty-minutes'),
                'menu-item-classes' => 'about-us',
                'menu-item-url' => home_url('/about-us/'),
                'menu-item-status' => 'publish',
                'menu-item-object-id' => $twenty_minutes_about_id,
                'menu-item-object' => 'page',
                'menu-item-type' => 'post_type'
            ));

            // Assign the menu to the primary location if not already set
            if ( ! has_nav_menu( $twenty_minutes_bpmenulocation ) ) {
                $twenty_minutes_locations = get_theme_mod( 'nav_menu_locations' );
                if ( empty( $twenty_minutes_locations ) ) {
                    $twenty_minutes_locations = array();
                }
                $twenty_minutes_locations[ $twenty_minutes_bpmenulocation ] = $twenty_minutes_menu_id;
                set_theme_mod( 'nav_menu_locations', $twenty_minutes_locations );
            }
        }

        //Header Section 
        set_theme_mod( 'twenty_minutes_topbar', true);
        set_theme_mod( 'twenty_minutes_the_custom_logo', esc_url( get_template_directory_uri().'/images/Logo.png'));
        set_theme_mod( 'twenty_minutes_phone_number', '+1 23 852 7854');
        set_theme_mod( 'twenty_minutes_email_address', 'info@youremailhere.com');
        
        //Social Media Section
        set_theme_mod( 'twenty_minutes_fb_link', '#'); 
        set_theme_mod( 'twenty_minutes_twitt_link', '#');
        set_theme_mod( 'twenty_minutes_linked_link', '#');
        set_theme_mod( 'twenty_minutes_insta_link', '#');
        set_theme_mod( 'twenty_minutes_youtube_link', '#');

        //Slider Section
        set_theme_mod( 'twenty_minutes_hide_categorysec', true);
        set_theme_mod( 'twenty_minutes_button_text', 'Read More');
        set_theme_mod( 'twenty_minutes_button_link_slider', '#');

        // Create the 'Slider' category and retrieve its ID
        $twenty_minutes_slider_category_id = wp_create_category('Slider');

        // Set the category in theme mods for the slider section
        if (!is_wp_error($twenty_minutes_slider_category_id)) {
            set_theme_mod('twenty_minutes_slidersection', $twenty_minutes_slider_category_id); 
        
            $twenty_minutes_titles = array(
                'Simply dummy text Lorem Ipsum industrys standard',   
                'Lorem Ipsum is simply dummy text of the printing',  
                'Sed ut perspiciatis unde omnis iste natus error sit'      
            );
        
            $twenty_minutes_content = 'Morbi praesent nascetur maecenas ligula habitasse tellus duis quisque efficitur sollicitudin senectus.';
        
            for ($twenty_minutes_i = 0; $twenty_minutes_i < 3; $twenty_minutes_i++) {
                set_theme_mod('twenty_minutes_title' . ($twenty_minutes_i + 1), $twenty_minutes_titles[$twenty_minutes_i]);
        
                $twenty_minutes_my_post = array(
                    'post_title'    => wp_strip_all_tags($twenty_minutes_titles[$twenty_minutes_i]),
                    'post_content'  => $twenty_minutes_content,
                    'post_status'   => 'publish',
                    'post_type'     => 'post',
                    'post_category' => array($twenty_minutes_slider_category_id),
                );
        
                $twenty_minutes_post_id = wp_insert_post($twenty_minutes_my_post);
        
                if (!is_wp_error($twenty_minutes_post_id)) {
                    error_log('Created Post ID: ' . $twenty_minutes_post_id);
        
                    $slider_image = 'slider' . ($twenty_minutes_i + 1) . '.png';
                    $twenty_minutes_image_url = get_template_directory_uri() . '/images/' . $slider_image;
                    
                    error_log('Image URL for post ID ' . $twenty_minutes_post_id . ': ' . $twenty_minutes_image_url);
        
                    // Set featured image
                    $twenty_minutes_image_id = media_sideload_image($twenty_minutes_image_url, $twenty_minutes_post_id, null, 'id');
                    
                    if (!is_wp_error($twenty_minutes_image_id)) {
                        set_post_thumbnail($twenty_minutes_post_id, $twenty_minutes_image_id);
                        error_log('Thumbnail set for Post ID: ' . $twenty_minutes_post_id);
                    } else {
                        error_log('Failed to set post thumbnail for Post ID: ' . $twenty_minutes_post_id);
                    }
                } else {
                    error_log('Failed to create post: ' . print_r($twenty_minutes_post_id, true));
                }
            }
        }        
  
       //Services Section
       set_theme_mod( 'twenty_minutes_show_serv_sec', true);
       set_theme_mod( 'twenty_minutes_section_text', 'WHO AM I'); 
       set_theme_mod( 'twenty_minutes_section_title', 'Our Services'); 

      // Include WordPress Filesystem API
      require_once( ABSPATH . 'wp-admin/includes/file.php' );

      // Initialize the filesystem
      global $wp_filesystem;
      if (false === ($twenty_minutes_creds = request_filesystem_credentials('', '', false, false, null))) {
          return; // Exit if credentials are needed
      }

      if (!WP_Filesystem($twenty_minutes_creds)) {
          return; // Exit if filesystem could not be initialized
      }

      $twenty_minutes_services_category_id = wp_create_category('Challenges');
      set_theme_mod('twenty_minutes_services_section', $twenty_minutes_services_category_id);
      $twenty_minutes_service_titles = array('Development', 'Digital Marketing', 'Online Consulting');

      // Define the image URLs for each service
      $twenty_minutes_image_urls = array(
          esc_url(get_template_directory_uri() . '/images/minutes-services/minutes-service1.png'),
          esc_url(get_template_directory_uri() . '/images/minutes-services/minutes-service2.png'),
          esc_url(get_template_directory_uri() . '/images/minutes-services/minutes-service3.png'),
      );

      // Loop to create posts for each service
        for ($twenty_minutes_i = 0; $twenty_minutes_i < count($twenty_minutes_service_titles); $twenty_minutes_i++) {
              $twenty_minutes_title = $twenty_minutes_service_titles[$twenty_minutes_i];
  
              // Create the post object
              $twenty_minutes_service_post = array(
                  'post_title'    => wp_strip_all_tags($twenty_minutes_title),
                  'post_status'   => 'publish',
                  'post_type'     => 'post',
                  'post_category' => array($twenty_minutes_services_category_id),
              );
  
              // Insert the post into the database
              $twenty_minutes_service_id = wp_insert_post($twenty_minutes_service_post);
  
              // If the post was successfully created
              if ($twenty_minutes_service_id && !is_wp_error($twenty_minutes_service_id)) {
                  // Set the theme mod for selecting this post
                  set_theme_mod('twenty_minutes_select_post' . ($twenty_minutes_i + 1), $twenty_minutes_service_id);
  
                  // Fetch image data from the corresponding URL
                  $twenty_minutes_image_data = file_get_contents($twenty_minutes_image_urls[$twenty_minutes_i]);
  
                  if ($twenty_minutes_image_data !== false) {
                      // Prepare file information
                      $twenty_minutes_upload_dir = wp_upload_dir();
                      if (!file_exists($twenty_minutes_upload_dir['path'])) {
                          error_log("Upload directory does not exist: " . $twenty_minutes_upload_dir['path']);
                          continue; // Skip to the next service
                      }
  
                      // Use a consistent file name for the image
                      $twenty_minutes_image_name = 'minutes-service' . ($twenty_minutes_i + 1) . '.png'; // Example: service1.png
                      $twenty_minutes_file = $twenty_minutes_upload_dir['path'] . '/' . $twenty_minutes_image_name;
  
                      // Use WordPress Filesystem API to write the image data
                      if (!$wp_filesystem->exists($twenty_minutes_file)) {
                          if ($wp_filesystem->put_contents($twenty_minutes_file, $twenty_minutes_image_data, FS_CHMOD_FILE) === false) {
                              error_log("Failed to write image to file: $twenty_minutes_file");
                              continue; // Skip to the next service
                          }
                      }
  
                      // Check the file type
                      $twenty_minutes_wp_filetype = wp_check_filetype($twenty_minutes_image_name, null);
                      if (!$twenty_minutes_wp_filetype['type']) {
                          error_log("Failed to determine MIME type for file: $twenty_minutes_image_name");
                          continue; // Skip to the next service
                      }
  
                      // Prepare attachment data
                      $twenty_minutes_attachment = array(
                          'post_mime_type' => $twenty_minutes_wp_filetype['type'],
                          'post_title'     => sanitize_file_name($twenty_minutes_image_name),
                          'post_content'   => '',
                          'post_status'    => 'inherit',
                      );
  
                      // Insert the attachment into the media library
                      $twenty_minutes_attach_id = wp_insert_attachment($twenty_minutes_attachment, $twenty_minutes_file, $twenty_minutes_service_id);
                      if (is_wp_error($twenty_minutes_attach_id)) {
                          error_log("Failed to insert attachment: " . $twenty_minutes_attach_id->get_error_message());
                          continue; // Skip to the next service
                      }
  
                      // Generate attachment metadata
                      $twenty_minutes_attach_data = wp_generate_attachment_metadata($twenty_minutes_attach_id, $twenty_minutes_file);
                      if (!$twenty_minutes_attach_data) {
                          error_log("Failed to generate attachment metadata for ID: $twenty_minutes_attach_id");
                          continue; // Skip to the next service
                      }
  
                      // Update attachment metadata
                      wp_update_attachment_metadata($twenty_minutes_attach_id, $twenty_minutes_attach_data);
  
                      // Set the attachment as the post's featured image
                      if (!set_post_thumbnail($twenty_minutes_service_id, $twenty_minutes_attach_id)) {
                          error_log("Failed to set featured image for post ID: $twenty_minutes_service_id");
                      } else {
                          error_log("Successfully set featured image for post ID: $twenty_minutes_service_id");
                      }
                  } else {
                      error_log("Failed to fetch image data from URL: " . $twenty_minutes_image_urls[$twenty_minutes_i]);
                  }
              } else {
                  error_log("Failed to create post: " . print_r($twenty_minutes_service_id, true));
              }
        }

        // Show success message and the "View Site" button
        update_option('twenty_minutes_demo_import_completed', true);
        echo '<br>';
        echo '<div class="success">Demo Import Successful</div>';
        echo '<br>';
        echo '<hr>';
        echo '<br>';
        echo '<span>' . esc_html__( 'You can now visit your site or customize it further.', 'twenty-minutes' ) . '</span>';
        echo '<br>';
    }
     ?>
    <ul>
        <li>
        <?php 
        // Check if the form is submitted
        if ( !isset( $_POST['submit'] ) ) : ?>
            <!-- Show demo importer form only if it's not submitted -->
            <?php if (!get_option('twenty_minutes_demo_import_completed')) : ?>
                <span><?php echo esc_html( 'Click on the below content to get demo content installed.', 'twenty-minutes' ); ?></span>
                <br><br>
                <hr><br>
                <b class="note"><?php echo esc_html('Note :', 'twenty-minutes' ); ?></b><br><br>
                <small><b><?php echo esc_html('Please take a backup if your website is already live with data. This importer will overwrite existing data.', 'twenty-minutes' ); ?></b></small><br><br>
                <form id="demo-importer-form" action="" method="POST" onsubmit="return runDemoImport();">
                    <input type="submit" name="submit" value="<?php echo esc_attr('Run Importer','twenty-minutes'); ?>" class="button button-primary button-large">
                </form>
                <script type="text/javascript">
                    function runDemoImport() {
                        if (confirm('Do you really want to do this?')) {
                            document.getElementById('demo-import-loader').style.display = 'block';
                            return true;
                        }
                        return false;
                    }
                </script>
             <?php endif; ?>
         <?php 
        endif; 

        // Show "View Site" button after form submission
        if ( isset( $_POST['submit'] ) ) {
        echo '<div class="view-site-btn">';
        echo '<a href="' . esc_url(home_url()) . '" class="button button-primary button-large" style="margin-top: 10px;" target="_blank">View Site</a>';
        echo '<a href="' . esc_url( admin_url('customize.php') ) . '" class="button button-primary button-large" style="margin-top: 10px;" target="_blank">Customize Demo Content</a>';
        echo '</div>';
        }
        ?>
        </li>
    </ul>
 </div>