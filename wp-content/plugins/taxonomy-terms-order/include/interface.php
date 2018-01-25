<?php


    function TOPluginInterface()
        {
            global $wpdb, $wp_locale;
            
            $taxonomy = isset($_GET['taxonomy']) ? sanitize_key($_GET['taxonomy']) : '';
            $post_type = isset($_GET['post_type']) ? sanitize_key($_GET['post_type']) : '';
            if(empty($post_type))
                {
                    $screen = get_current_screen();
                    
                    if(isset($screen->post_type)    && !empty($screen->post_type))
                        $post_type  =   $screen->post_type;
                        else
                        {
                            switch($screen->parent_file)
                                {
                                    case "upload.php" :
                                                        $post_type  =   'attachment';
                                                        break;
                                                
                                    default:
                                                        $post_type  =   'post';   
                                }
                        }       
                } 
                                            
            $post_type_data = get_post_type_object($post_type);
            
            if (!taxonomy_exists($taxonomy))
                $taxonomy = '';

            ?>
            <div class="wrap">
                <div class="icon32" id="icon-edit"><br></div>
                <h2><?php _e( "Taxonomy Order", 'taxonomy-terms-order' ) ?></h2>

                <?php tto_info_box() ?>
                
                <div id="ajax-response"></div>
                
                <noscript>
                    <div class="error message">
                        <p><?php _e( "This plugin can't work without javascript, because it's use drag and drop and AJAX.", 'taxonomy-terms-order' ) ?></p>
                    </div>
                </noscript>

                <div class="clear"></div>
                
                <?php
                
                    $current_section_parent_file    =   '';
                    switch($post_type)
                        {
                            
                            case "attachment" :
                                            $current_section_parent_file    =   "upload.php";
                                            break;
                                            
                            default :
                                            $current_section_parent_file    =    "edit.php";
                                            break;
                        }
                
                
                ?>
                
                <form action="<?php echo $current_section_parent_file ?>" method="get" id="to_form">
                    <input type="hidden" name="page" value="to-interface-<?php echo esc_attr($post_type) ?>" />
                    <?php
                
                     if (!in_array($post_type, array('post', 'attachment'))) 
                        echo '<input type="hidden" name="post_type" value="'. esc_attr($post_type) .'" />';

                    //output all available taxonomies for this post type
                    
                    $post_type_taxonomies = get_object_taxonomies($post_type);
                
                    foreach ($post_type_taxonomies as $key => $taxonomy_name)
                        {
                            $taxonomy_info = get_taxonomy($taxonomy_name);  
                            if ($taxonomy_info->hierarchical !== TRUE) 
                                unset($post_type_taxonomies[$key]);
                        }
                        
                    //use the first taxonomy if emtpy taxonomy
                    if ($taxonomy == '' || !taxonomy_exists($taxonomy))
                        {
                            reset($post_type_taxonomies);   
                            $taxonomy = current($post_type_taxonomies);
                        }
                                            
                    if (count($post_type_taxonomies) > 1)
                        {
                
                            ?>
                            
                            <h2 class="subtitle"><?php echo ucfirst($post_type_data->labels->name) ?> <?php _e( "Taxonomies", 'taxonomy-terms-order' ) ?></h2>
                            <table cellspacing="0" class="wp-list-taxonomy">
                                <thead>
                                <tr>
                                    <th style="" class="column-cb check-column" id="cb" scope="col">&nbsp;</th><th style="" class="" id="author" scope="col"><?php _e( "Taxonomy Title", 'taxonomy-terms-order' ) ?></th><th style="" class="manage-column" id="categories" scope="col"><?php _e( "Total Posts", 'taxonomy-terms-order' ) ?></th>    </tr>
                                </thead>

   
                                <tbody id="the-list">
                                <?php
                                    
                                    $alternate = FALSE;
                                    foreach ($post_type_taxonomies as $post_type_taxonomy)
                                        {
                                            $taxonomy_info = get_taxonomy($post_type_taxonomy);

                                            $alternate = $alternate === TRUE ? FALSE :TRUE;
                                            
                                            $args = array(
                                                        'hide_empty'    =>  0,
                                                        'taxonomy'      =>  $post_type_taxonomy
                                                        );
                                            $taxonomy_terms = get_terms( $args );
                                                             
                                            ?>
                                                <tr valign="top" class="<?php if ($alternate === TRUE) {echo 'alternate ';} ?>" id="taxonomy-<?php echo esc_attr($taxonomy)  ?>">
                                                        <th class="check-column" scope="row"><input type="radio" onclick="to_change_taxonomy(this)" value="<?php echo $post_type_taxonomy ?>" <?php if ($post_type_taxonomy == $taxonomy) {echo 'checked="checked"';} ?> name="taxonomy">&nbsp;</th>
                                                        <td class="categories column-categories"><b><?php echo $taxonomy_info->label ?></b> (<?php echo  $taxonomy_info->labels->singular_name; ?>)</td>
                                                        <td class="categories column-categories"><?php echo count($taxonomy_terms) ?></td>
                                                </tr>
                                            
                                            <?php
                                        }
                                ?>
                                </tbody>
                            </table>
                            <br />
                            <?php
                        }
                            ?>

                <div id="order-terms">
                    
      
                    
                    <div id="post-body">                    
                        
                            <ul class="sortable" id="tto_sortable">
                                <?php 
                                    listTerms($taxonomy); 
                                ?>
                            </ul>
                            
                            <div class="clear"></div>
                    </div>
                    
                    <div class="alignleft actions">
                        <p class="submit">
                            <a href="javascript:;" class="save-order button-primary"><?php _e( "Update", 'taxonomy-terms-order' ) ?></a>
                        </p>
                    </div>
                    
                </div> 

                </form>
                
                <script type="text/javascript">
                    jQuery(document).ready(function() {
                        
                        var NestedSortableSerializedData;
                        jQuery("ul.sortable").sortable({
                                'tolerance':'intersect',
                                'cursor':'pointer',
                                'items':'> li',
                                'axi': 'y',
                                'placeholder':'placeholder',
                                'nested': 'ul'
                            });
                    });
                    
                    
                    jQuery(".save-order").bind( "click", function() {
                                
                                var mySortable = new Array();
                                jQuery(".sortable").each(  function(){
                                    
                                    var serialized = jQuery(this).sortable("serialize");
                                    
                                    var parent_tag = jQuery(this).parent().get(0).tagName;
                                    parent_tag = parent_tag.toLowerCase()
                                    if (parent_tag == 'li')
                                        {
                                            // 
                                            var tag_id = jQuery(this).parent().attr('id');
                                            mySortable[tag_id] = serialized;
                                        }
                                        else
                                        {
                                            //
                                            mySortable[0] = serialized;
                                        }
                                });
                                
                                //serialize the array
                                var serialize_data = serialize(mySortable);
                                                                                            
                                jQuery.post( ajaxurl, { action:'update-taxonomy-order', order: serialize_data, taxonomy : '<?php echo  $taxonomy ?>' }, function() {
                                    jQuery("#ajax-response").html('<div class="message updated fade"><p><?php _e( "Items Order Updated", 'taxonomy-terms-order' ) ?></p></div>');
                                    jQuery("#ajax-response div").delay(3000).hide("slow");
                                });
                            });
                </script>
                
            </div>
            <?php 
            
            
        }
    
    
    function listTerms($taxonomy) 
            {

                // Query pages.
                $args = array(
                            'orderby'       =>  'term_order',
                            'depth'         =>  0,
                            'child_of'      => 0,
                            'hide_empty'    =>  0
                );
                $taxonomy_terms = get_terms($taxonomy, $args);

                $output = '';
                if (count($taxonomy_terms) > 0)
                    {
                        $output = TOwalkTree($taxonomy_terms, $args['depth'], $args);    
                    }

                echo $output; 
                
            }
        
        function TOwalkTree($taxonomy_terms, $depth, $r) 
            {
                $walker = new TO_Terms_Walker; 
                $args = array($taxonomy_terms, $depth, $r);
                return call_user_func_array(array(&$walker, 'walk'), $args);
            }

?>