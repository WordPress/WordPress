<?php
/*
Plugin Name: Category Order and Taxonomy Terms Order
Plugin URI: http://www.nsp-code.com
Description: Order Categories and all custom taxonomies terms (hierarchically) and child terms using a Drag and Drop Sortable javascript capability. 
Version: 1.5.2.2
Author: Nsp-Code
Author URI: http://www.nsp-code.com
Author Email: electronice_delphi@yahoo.com
Text Domain: taxonomy-terms-order
Domain Path: /languages/ 
*/


    define('TOPATH',    plugin_dir_path(__FILE__));
    define('TOURL',     plugins_url('', __FILE__));

    register_deactivation_hook(__FILE__, 'TO_deactivated');
    register_activation_hook(__FILE__, 'TO_activated');

    function TO_activated() 
        {
            global $wpdb;
            
            //check if the menu_order column exists;
            $query = "SHOW COLUMNS FROM $wpdb->terms 
                        LIKE 'term_order'";
            $result = $wpdb->query($query);
            
            if ($result == 0)
                {
                    $query = "ALTER TABLE $wpdb->terms ADD `term_order` INT( 4 ) NULL DEFAULT '0'";
                    $result = $wpdb->query($query); 
                }

        }
        
    function TO_deactivated() 
        {
            
        }

    include_once(TOPATH . '/include/functions.php');
        
    add_action( 'plugins_loaded', 'to_load_textdomain'); 
    function to_load_textdomain() 
        {
            load_plugin_textdomain('taxonomy-terms-order', FALSE, dirname( plugin_basename( __FILE__ ) ) . '/languages');
        }
        
    add_action('admin_print_scripts', 'TO_admin_scripts');
    function TO_admin_scripts()
        {
            wp_enqueue_script('jquery');
            
            wp_enqueue_script('jquery-ui-sortable');
            
            $myJsFile = TOURL . '/js/to-javascript.js';
            wp_register_script('to-javascript.js', $myJsFile);
            wp_enqueue_script( 'to-javascript.js');
               
        }
        
    add_action('admin_print_styles', 'TO_admin_styles');
    function TO_admin_styles()
        {
            $myCssFile = TOURL . '/css/to.css';
            wp_register_style('to.css', $myCssFile);
            wp_enqueue_style( 'to.css');
        } 
        
    add_action('admin_menu', 'TOPluginMenu', 99);

    function TOPluginMenu() 
        {
            include (TOPATH . '/include/interface.php');
            include (TOPATH . '/include/terms_walker.php');
            
            include (TOPATH . '/include/options.php'); 
            add_options_page('Taxonomy Terms Order', '<img class="menu_tto" src="'. TOURL .'/images/menu-icon.png" alt="" />' . __('Taxonomy Terms Order', 'taxonomy-terms-order'), 'manage_options', 'to-options', 'to_plugin_options');
                    
            $options = tto_get_settings();
            
            if(isset($options['capability']) && !empty($options['capability']))
                $capability = $options['capability'];
            else if (is_numeric($options['level']))
                {
                    //maintain the old user level compatibility
                    $capability = tto_userdata_get_user_level();
                }
                else
                    {
                        $capability = 'manage_options';  
                    } 
                    
             //put a menu within all custom types if apply
            $post_types = get_post_types();
            foreach( $post_types as $post_type) 
                {
                        
                    //check if there are any taxonomy for this post type
                    $post_type_taxonomies = get_object_taxonomies($post_type);
                    
                    foreach ($post_type_taxonomies as $key => $taxonomy_name)
                        {
                            $taxonomy_info = get_taxonomy($taxonomy_name);  
                            if (empty($taxonomy_info->hierarchical) ||  $taxonomy_info->hierarchical !== TRUE) 
                                unset($post_type_taxonomies[$key]);
                        }
                        
                    if (count($post_type_taxonomies) == 0)
                        continue;                
                    
                    if ($post_type == 'post')
                        add_submenu_page('edit.php', __('Taxonomy Order', 'taxonomy-terms-order'), __('Taxonomy Order', 'taxonomy-terms-order'), $capability, 'to-interface-'.$post_type, 'TOPluginInterface' );
                        elseif ($post_type == 'attachment')
                        add_submenu_page('upload.php', __('Taxonomy Order', 'taxonomy-terms-order'), __('Taxonomy Order', 'taxonomy-terms-order'), $capability, 'to-interface-'.$post_type, 'TOPluginInterface' );   
                        else
                        add_submenu_page('edit.php?post_type='.$post_type, __('Taxonomy Order', 'taxonomy-terms-order'), __('Taxonomy Order', 'taxonomy-terms-order'), $capability, 'to-interface-'.$post_type, 'TOPluginInterface' );
                }
        }

    function TO_applyorderfilter($orderby, $args)
        {
	        $options = tto_get_settings();
            
            //if admin make sure use the admin setting
            if (is_admin())
                {
                    if ($options['adminsort'] == "1")
                        return 't.term_order';
                        
                    return $orderby;    
                }
            
            //if autosort, then force the menu_order
            if ($options['autosort'] == 1   &&  (!isset($args['ignore_term_order']) ||  (isset($args['ignore_term_order'])  &&  $args['ignore_term_order']  !== TRUE) ))
                {
                    return 't.term_order';
                }
                
            return $orderby; 
        }

    add_filter('get_terms_orderby', 'TO_applyorderfilter', 10, 2);

    add_filter('get_terms_orderby', 'TO_get_terms_orderby', 1, 2);
    function TO_get_terms_orderby($orderby, $args)
        {
            if (isset($args['orderby']) && $args['orderby'] == "term_order" && $orderby != "term_order")
                return "t.term_order";
                
            return $orderby;
        }

    add_action( 'wp_ajax_update-taxonomy-order', 'TOsaveAjaxOrder' );
    function TOsaveAjaxOrder()
        {
            global $wpdb; 
            $taxonomy = stripslashes($_POST['taxonomy']);
            $data = stripslashes($_POST['order']);
            $unserialised_data = unserialize($data);
                    
            if (is_array($unserialised_data))
            foreach($unserialised_data as $key => $values ) 
                {
                    //$key_parent = str_replace("item_", "", $key);
                    $items = explode("&", $values);
                    unset($item);
                    foreach ($items as $item_key => $item_)
                        {
                            $items[$item_key] = trim(str_replace("item[]=", "",$item_));
                        }
                    
                    if (is_array($items) && count($items) > 0)
                    foreach( $items as $item_key => $term_id ) 
                        {
                            $wpdb->update( $wpdb->terms, array('term_order' => ($item_key + 1)), array('term_id' => $term_id) );
                        } 
                }
                
            do_action('tto/update-order');
                
            die();
        }


?>