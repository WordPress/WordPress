<?php if ( ! defined( 'OT_VERSION' ) ) exit( 'No direct script access allowed' );
/**
 * OptionTree documentation page functions.
 *
 * @package   OptionTree
 * @author    Derek Herman <derek@valendesigns.com>
 * @copyright Copyright (c) 2013, Derek Herman
 * @since     2.0
 */

/**
 * Creating Options option type.
 *
 * @return    string
 *
 * @access    public
 * @since     2.0
 */
if ( ! function_exists( 'ot_type_creating_options' ) ) {
  
  function ot_type_creating_options() {
    
    /* format setting outer wrapper */
    echo '<div class="format-setting type-textblock wide-desc">';
      
      /* description */
      echo '<div class="description">';
        
        echo '<h4>'. __( 'Label', 'option-tree' ) . ':</h4>';
        echo '<p>' . __( 'The Label field should be a short but descriptive block of text 100 characters or less with no HTML.', 'option-tree' ) . '</p>';
        
        echo '<h4>'. __( 'ID', 'option-tree' ) . ':</h4>';
        echo '<p>' . __( 'The ID field is a unique alphanumeric key used to differentiate each theme option (underscores are acceptable). Also, the plugin will change all text you write in this field to lowercase and replace spaces and special characters with an underscore automatically.', 'option-tree' ) . '</p>';
        
        echo '<h4>'. __( 'Type', 'option-tree' ) . ':</h4>';
        echo '<p>' . __( 'You are required to choose one of the supported option types when creating a new option. Here is a list of the available option types. For more information about each type click the <code>Option Types</code> tab to the left.', 'option-tree' ) . '</p>';
        
        echo '<ul class="docs-ul">';
        foreach( ot_option_types_array() as $key => $value )
          echo '<li>' . $value . '</li>';
        echo '</ul>';
        
        echo '<h4>'. __( 'Description', 'option-tree' ) . ':</h4>';
        echo '<p>' . __( 'Enter a detailed description for the users to read on the Theme Options page, HTML is allowed. This is also where you enter content for both the Textblock & Textblock Titled option types.', 'option-tree' ) . '</p>';
        
        echo '<h4>'. __( 'Choices', 'option-tree' ) . ':</h4>';
        echo '<p>' . __( 'Click the "Add Choice" button to add an item to the choices array. This will only affect the following option types: Checkbox, Radio, Select & Select Image.', 'option-tree' ) . '</p>';
        
        echo '<h4>'. __( 'Settings', 'option-tree' ) . ':</h4>';
        echo '<p>' . __( 'Click the "Add Setting" button found inside a newly created setting to add an item to the settings array. This will only affect the List Item type.', 'option-tree' ) . '</p>';
        
        echo '<h4>'. __( 'Standard', 'option-tree' ) . ':</h4>';
        echo '<p>' . __( 'Setting the standard value for your option only works for some option types. Those types are one that have a single string value saved to them and not an array of values.', 'option-tree' ) . '</p>';
        
        echo '<h4>'. __( 'Rows', 'option-tree' ) . ':</h4>';
        echo '<p>' . __( 'Enter a numeric value for the number of rows in your textarea. This will only affect the following option types: CSS, Textarea, & Textarea Simple.', 'option-tree' ) . '</p>';
        
        echo '<h4>'. __( 'Post Type', 'option-tree' ) . ':</h4>';
        echo '<p>' . __( 'Add a comma separated list of post type like <code>post,page</code>. This will only affect the following option types: Custom Post Type Checkbox, & Custom Post Type Select. Below are the default post types available with WordPress and that are also compatible with OptionTree. You can also add your own custom <code>post_type</code>. At this time <code>any</code> does not seem to return results properly and is something I plan on looking into.', 'option-tree' ) . '</p>';
        
        echo '<ul class="docs-ul">';
          echo '<li><code>post</code></li>';
          echo '<li><code>page</code></li>';
          echo '<li><code>attachment</code></li>';
        echo '</ul>';
        
        echo '<h4>'. __( 'Taxonomy', 'option-tree' ) . ':</h4>';
        echo '<p>' . __( 'Add a comma separated list of any registered taxonomy like <code>category,post_tag</code>. This will only affect the following option types: Taxonomy Checkbox, & Taxonomy Select.', 'option-tree' ) . '</p>';
        
        echo '<h4>'. __( 'Min, Max, & Step', 'option-tree' ) . ':</h4>';
        echo '<p>' . __( 'Add a comma separated list of options in the following format <code>0,100,1</code> (slide from <code>0-100</code> in intervals of <code>1</code>). The three values represent the minimum, maximum, and step options and will only affect the Numeric Slider option type.', 'option-tree' ) . '</p>';
        
        echo '<h4>'. __( 'CSS Class', 'option-tree' ) . ':</h4>';
        echo '<p>' . __( 'Add and optional class to any option type.', 'option-tree' ) . '</p>';
        
        echo '<h4>'. __( 'Condition', 'option-tree' ) . ':</h4>';
        echo '<p>' . sprintf( __( 'Add a comma separated list (no spaces) of conditions in which the field will be visible, leave this setting empty to always show the field. In these examples, %s is a placeholder for your condition, which can be in the form of %s.', 'option-tree' ), '<code>value</code>', '<code>field_id:is(value)</code>, <code>field_id:not(value)</code>, <code>field_id:contains(value)</code>, <code>field_id:less_than(value)</code>, <code>field_id:less_than_or_equal_to(value)</code>, <code>field_id:greater_than(value)</code>, or <code>field_id:greater_than_or_equal_to(value)</code>' ) . '</p>';
        
        echo '<h4>'. __( 'Operator', 'option-tree' ) . ':</h4>';
        echo '<p>' . sprintf( __( 'Choose the logical operator to compute the result of the conditions. Your options are %s and %s.', 'option-tree' ), '<code>and</code>', '<code>or</code>' ) . '</p>';
        
      echo '</div>';
      
    echo '</div>';
    
  }
  
}

/**
 * ot_get_option() option type.
 *
 * This is a callback function to display text about ot_get_option().
 *
 * @return    string
 *
 * @access    public
 * @since     2.0
 */
if ( ! function_exists( 'ot_type_option_types' ) ) {
  
  function ot_type_option_types() {
    
    /* format setting outer wrapper */
    echo '<div class="format-setting type-textblock wide-desc">';
      
      /* description */
      echo '<div class="description">';
        
        echo '<h4>'. __( 'Background', 'option-tree' ) . ':</h4>';    
        echo '<p>' . sprintf( __( 'The Background option type is for adding background styles to your theme either dynamically via the CSS option type below or manually with %s. The Background option type has filters that allow you to remove fields or change the defaults. For example, you can filter %s to remove unwanted fields from all Background options or an individual one. You can also filter %s. These filters allow you to fine tune the select lists for your specific needs.', 'option-tree' ), '<code>ot_get_option()</code>', '<code>ot_recognized_background_fields</code>', '<code>ot_recognized_background_repeat</code>, <code>ot_recognized_background_attachment</code>, <code>ot_recognized_background_position</code>, ' . __( 'and', 'option-tree' ) . ' <code>ot_type_background_size_choices</code>' ) . '</p>';
        
        echo '<h4>'. __( 'Category Checkbox', 'option-tree' ) . ':</h4>';      
        echo '<p>' . __( 'The Category Checkbox option type displays a list of category IDs. It allows the user to check multiple category IDs and will return that value as an array for use in a custom function or loop.', 'option-tree' ) . '</p>';
        
        echo '<h4>'. __( 'Category Select', 'option-tree' ) . ':</h4>';    
        echo '<p>' . __( 'The Category Select option type displays a list of category IDs. It allows the user to select only one category ID and will return that value for use in a custom function or loop.', 'option-tree' ) . '</p>';
        
        echo '<h4>'. __( 'Checkbox', 'option-tree' ) . ':</h4>';       
        echo '<p>' . __( 'The Checkbox option type displays a group of choices. It allows the user to check multiple choices and will return that value as an array for use in a custom function or loop.', 'option-tree' ) . '</p>';
        
        echo '<h4>'. __( 'Colorpicker', 'option-tree' ) . ':</h4>'; 
        echo '<p>' . __( 'The Colorpicker option type saves a hexadecimal color code for use in CSS. Use it to modify the color of something in your theme.', 'option-tree' ) . '</p>';
        
        echo '<h4>'. __( 'CSS', 'option-tree' ) . ':</h4>'; 
        echo '<p>' . sprintf( __( 'The CSS option type is a textarea that when used properly can add dynamic CSS to your theme from within OptionTree. Unfortunately, due server limitations you will need to create a file named %s at the root level of your theme and change permissions using %s so the server can write to the file. I have had the most success setting this single file to %s but feel free to play around with permissions until everything is working. A good starting point is %s. When the server can save to the file, CSS will automatically be updated when you save your Theme Options.', 'option-tree' ), '<code>dynamic.css</code>', '<code>chmod</code>', '<code>0777</code>', '<code>0666</code>' ) . '</p>';
        
        echo '<p class="aside">' . sprintf( __( 'This example assumes you have an option with the ID of %1$s. Which means this option will automatically insert the value of %1$s into the %2$s when the Theme Options are saved.', 'option-tree' ), '<code>demo_background</code>', '<code>dynamic.css</code>' ) . '</p>';
        
        echo '<p>'. __( 'Input', 'option-tree' ) . ':</p>'; 
        echo '<pre><code>body {
  {{demo_background}}
  background-color: {{demo_background|background-color}};
}</code></pre>';

        echo '<p>'. __( 'Output', 'option-tree' ) . ':</p>'; 
        echo '<pre><code>/* BEGIN demo_background */
body {
  background: color image repeat attachment position;
  background-color: color;
}
/* END demo_background */</code></pre>';
        
        echo '<h4>'. __( 'Custom Post Type Checkbox', 'option-tree' ) . ':</h4>'; 
        echo '<p>' . sprintf( __( 'The Custom Post Type Select option type displays a list of IDs from any available WordPress post type or custom post type. It allows the user to check multiple post IDs for use in a custom function or loop. Requires at least one valid %1$s in the %1$s field.', 'option-tree' ), '<code>post_type</code>' ) . '</p>';
        
        echo '<h4>'. __( 'Custom Post Type Select', 'option-tree' ) . ':</h4>'; 
        echo '<p>' . sprintf( __( 'The Custom Post Type Select option type displays a list of IDs from any available WordPress post type or custom post type. It will return a single post ID for use in a custom function or loop. Requires at least one valid %1$s in the %1$s field.', 'option-tree' ), '<code>post_type</code>' ) . '</p>';
        
        echo '<h4>'. __( 'Date Picker', 'option-tree' ) . ':</h4>'; 
        echo '<p>' . __( 'The Date Picker option type is tied to a standard form input field which displays a calendar pop-up that allow the user to pick any date when focus is given to the input field. The returned value is a date formatted string.', 'option-tree' ) . '</p>';
        
        echo '<h4>'. __( 'Date Time Picker', 'option-tree' ) . ':</h4>'; 
        echo '<p>' . __( 'The Date Time Picker option type is tied to a standard form input field which displays a calendar pop-up that allow the user to pick any date and time when focus is given to the input field. The returned value is a date and time formatted string.', 'option-tree' ) . '</p>';
        
        echo '<h4>'. __( 'Gallery', 'option-tree' ) . ':</h4>'; 
        echo '<p>' . __( 'The Gallery option type saves a comma separated list of image attachment IDs. You will need to create a front-end function to display the images in your theme.', 'option-tree' ) . '</p>';
        
        echo '<h4>'. __( 'List Item', 'option-tree' ) . ':</h4>'; 
        echo '<p>' . __( 'The List Item option type replaced the Slider option type and allows for a great deal of customization. You can add settings to the List Item and those settings will be displayed to the user when they add a new List Item. Typical use is for creating sliding content or blocks of code for custom layouts.', 'option-tree' ) . '</p>';
        
        echo '<h4>'. __( 'Measurement', 'option-tree' ) . ':</h4>'; 
        echo '<p>' . sprintf( __( 'The Measurement option type is a mix of input and select fields. The text input excepts a value and the select lets you choose the unit of measurement to add to that value. Currently the default units are %s, %s, %s, and %s. However, you can change them with the %s filter.', 'option-tree' ), '<code>px</code>', '<code>%</code>', '<code>em</code>', '<code>pt</code>', '<code>ot_measurement_unit_types</code>' ) . '</p>';
        
        echo '<p>' . sprintf( __( 'Example filter to add new units to the Measurement option type. Added to %s.', 'option-tree' ), '<code>functions.php</code>' ) . '</p>';
        echo '<pre><code>function filter_measurement_unit_types( $array, $field_id ) {
  
  /* only run the filter on measurement with a field ID of my_measurement */
  if ( $field_id == \'my_measurement\' ) {
    $array[\'in\'] = \'inches\';
    $array[\'ft\'] = \'feet\';
  }
  
  return $array;
}
add_filter( \'ot_measurement_unit_types\', \'filter_measurement_unit_types\', 10, 2 );</code></pre>';

        echo '<p>' . __( 'Example filter to completely change the units in the Measurement option type. Added to <code>functions.php</code>.', 'option-tree' ) . '</p>';
        echo '<pre><code>function filter_measurement_unit_types( $array, $field_id ) {
  
  /* only run the filter on measurement with a field ID of my_measurement */
  if ( $field_id == \'my_measurement\' ) {
    $array = array(
      \'in\' => \'inches\',
      \'ft\' => \'feet\'
    );
  }
  
  return $array;
}
add_filter( \'ot_measurement_unit_types\', \'filter_measurement_unit_types\', 10, 2 );</code></pre>';
        
        echo '<h4>'. __( 'Numeric Slider', 'option-tree' ) . ':</h4>'; 
        echo '<p>' . __( 'The Numeric Slider option type displays a jQuery UI slider. It will return a single numerical value for use in a custom function or loop.', 'option-tree' ) . '</p>';
        
        echo '<h4>'. __( 'On/Off', 'option-tree' ) . ':</h4>'; 
        echo '<p>' . sprintf( __( 'The On/Off option type displays a simple switch that can be used to turn things on or off. The saved return value is either %s or %s.', 'option-tree' ), '<code>on</code>', '<code>off</code>' ) . '</p>';
        
        echo '<h4>'. __( 'Page Checkbox', 'option-tree' ) . ':</h4>'; 
        echo '<p>' . __( 'The Page Checkbox option type displays a list of page IDs. It allows the user to check multiple page IDs for use in a custom function or loop.', 'option-tree' ) . '</p>';
        
        echo '<h4>'. __( 'Page Select', 'option-tree' ) . ':</h4>'; 
        echo '<p>' . __( 'The Page Select option type displays a list of page IDs. It will return a single page ID for use in a custom function or loop.', 'option-tree' ) . '</p>';
        
        echo '<h4>'. __( 'Post Checkbox', 'option-tree' ) . ':</h4>'; 
        echo '<p>' . __( 'The Post Checkbox option type displays a list of post IDs. It allows the user to check multiple post IDs for use in a custom function or loop.', 'option-tree' ) . '</p>';
        
        echo '<h4>'. __( 'Post Select', 'option-tree' ) . ':</h4>'; 
        echo '<p>' . __( 'The Post Select option type displays a list of post IDs. It will return a single post ID for use in a custom function or loop.', 'option-tree' ) . '</p>';
        
        echo '<h4>'. __( 'Radio', 'option-tree' ) . ':</h4>'; 
        echo '<p>' . __( 'The Radio option type displays a group of choices. It allows the user to choose one and will return that value as a string for use in a custom function or loop.', 'option-tree' ) . '</p>';
        
        echo '<h4>'. __( 'Radio Image', 'option-tree' ) . ':</h4>'; 
        echo '<p>' . sprintf( __( 'the Radio Images option type is primarily used for layouts. However, you can filter the image list using %s. As well, you can add your own custom images using the choices array.', 'option-tree' ), '<code>ot_radio_images</code>' ) . '</p>';
        
        echo '<p>' . __( 'This example executes the <code>ot_radio_images</code> filter on layout images attached to the <code>my_radio_images</code> field. Added to <code>functions.php</code>.', 'option-tree' ) . '</p>';
        echo '<pre><code>function filter_radio_images( $array, $field_id ) {
  
  /* only run the filter where the field ID is my_radio_images */
  if ( $field_id == \'my_radio_images\' ) {
    $array = array(
      array(
        \'value\'   => \'left-sidebar\',
        \'label\'   => __( \'Left Sidebar\', \'option-tree\' ),
        \'src\'     => OT_URL . \'/assets/images/layout/left-sidebar.png\'
      ),
      array(
        \'value\'   => \'right-sidebar\',
        \'label\'   => __( \'Right Sidebar\', \'option-tree\' ),
        \'src\'     => OT_URL . \'/assets/images/layout/right-sidebar.png\'
      )
    );
  }
  
  return $array;
  
}
add_filter( \'ot_radio_images\', \'filter_radio_images\', 10, 2 );</code></pre>';
        
        echo '<h4>'. __( 'Select', 'option-tree' ) . ':</h4>'; 
        echo '<p>' . __( 'The Select option type is used to list anything you want that would be chosen from a select list.', 'option-tree' ) . '</p>';
        
        echo '<h4>'. __( 'Sidebar Select', 'option-tree' ) . ':</h4>';
        echo '<p>' . sprintf(  __( 'This option type makes it possible for users to select a WordPress registered sidebar to use on a specific area. By using the two provided filters, %s, and %s we can be selective about which sidebars are available on a specific content area.', 'option-tree' ), '<code>ot_recognized_sidebars</code>', '<code>ot_recognized_sidebars_{$field_id}</code>' ) . '</p>';
        echo '<p>' . sprintf( __( 'For example, if we create a WordPress theme that provides the ability to change the Blog Sidebar and we don\'t want to have the footer sidebars available on this area, we can unset those sidebars either manually or by using a regular expression if we have a common name like %s.', 'option-tree' ), '<code>footer-sidebar-$i</code>' ) . '</p>';
        
        echo '<h4>'. __( 'Slider', 'option-tree' ) . ':</h4>';
        echo '<p>' . __( 'The Slider option type is technically deprecated. Use the List Item option type instead, as it\'s infinitely more customizable. Typical use is for creating sliding image content.', 'option-tree' ) . '</p>';
        
        echo '<h4>'. __( 'Social Links', 'option-tree' ) . ':</h4>';
        echo '<p>' . sprintf( __( 'The Social Links option type utilizes a drag & drop interface to create a list of social links. There are a few filters that make extending this option type easy. You can set the %s filter to %s and turn off loading default values. Use the %s filter to change the default values that are loaded. To filter the settings array use the %s filter.', 'option-tree' ), '<code>ot_type_social_links_load_defaults</code>', '<code>false</code>', '<code>ot_type_social_links_defaults</code>', '<code>ot_social_links_settings</code>' ) . '</p>';
        
        echo '<h4>'. __( 'Tab', 'option-tree' ) . ':</h4>';      
        echo '<p>' . __( 'The Tab option type will break a section or metabox into tabbed content.', 'option-tree' ) . '</p>';
        
        echo '<h4>'. __( 'Tag Checkbox', 'option-tree' ) . ':</h4>';      
        echo '<p>' . __( 'The Tag Checkbox option type displays a list of tag IDs. It allows the user to check multiple tag IDs and will return that value as an array for use in a custom function or loop.', 'option-tree' ) . '</p>';
        
        echo '<h4>'. __( 'Tag Select', 'option-tree' ) . ':</h4>';    
        echo '<p>' . __( 'The Tag Select option type displays a list of tag IDs. It allows the user to select only one tag ID and will return that value for use in a custom function or loop.', 'option-tree' ) . '</p>';
        
        echo '<h4>'. __( 'Taxonomy Checkbox', 'option-tree' ) . ':</h4>';      
        echo '<p>' . __( 'The Taxonomy Checkbox option type displays a list of taxonomy IDs. It allows the user to check multiple taxonomy IDs and will return that value as an array for use in a custom function or loop.', 'option-tree' ) . '</p>';
        
        echo '<h4>'. __( 'Taxonomy Select', 'option-tree' ) . ':</h4>';    
        echo '<p>' . __( 'The Taxonomy Select option type displays a list of taxonomy IDs. It allows the user to select only one taxonomy ID and will return that value for use in a custom function or loop.', 'option-tree' ) . '</p>';
        
        echo '<h4>'. __( 'Text', 'option-tree' ) . ':</h4>'; 
        echo '<p>' . __( 'The Text option type is used to save string values. For example, any optional or required text that is of reasonably short character length.', 'option-tree' ) . '</p>';
        
        echo '<h4>'. __( 'Textarea', 'option-tree' ) . ':</h4>'; 
        echo '<p>' . sprintf( __( 'The Textarea option type is a large string value used for custom code or text in the theme and has a WYSIWYG editor that can be filtered to change the how it is displayed. For example, you can filter %s, %s, %s, and %s.', 'option-tree' ), '<code>wpautop</code>', '<code>media_buttons</code>', '<code>tinymce</code>', '<code>quicktags</code>' ) . '</p>';
        
        echo '<p class="aside">' . __( 'Example filters to alter the Textarea option type. Added to <code>functions.php</code>.', 'option-tree' ) . '</p>';
        
        echo '<p>' . __( 'This example keeps WordPress from executing the <code>wpautop</code> filter on the line breaks. The default is <code>true</code> which means it wraps line breaks with an HTML <code>p</code> tag.', 'option-tree' ) . '</p>';
        echo '<pre><code>function filter_textarea_wpautop( $content, $field_id ) {
  
  /* only run the filter on the textarea with a field ID of my_textarea */
  if ( $field_id == \'my_textarea\' ) {
    return false;
  }
  
  return $content;
  
}
add_filter( \'ot_wpautop\', \'filter_textarea_wpautop\', 10, 2 );</code></pre>';

        echo '<p>' . __( 'This example keeps WordPress from executing the <code>media_buttons</code> filter on the textarea WYSIWYG. The default is <code>true</code> which means show the buttons.', 'option-tree' ) . '</p>';
        echo '<pre><code>function filter_textarea_media_buttons( $content, $field_id ) {
  
  /* only run the filter on the textarea with a field ID of my_textarea */
  if ( $field_id == \'my_textarea\' ) {
    return false;
  }
  
  return $content;
  
}
add_filter( \'ot_media_buttons\', \'filter_textarea_media_buttons\', 10, 2 );</code></pre>';
        
        echo '<p>' . __( 'This example keeps WordPress from executing the <code>tinymce</code> filter on the textarea WYSIWYG. The default is <code>true</code> which means show the tinymce.', 'option-tree' ) . '</p>';
        echo '<pre><code>function filter_textarea_tinymce( $content, $field_id ) {
  
  /* only run the filter on the textarea with a field ID of my_textarea */
  if ( $field_id == \'my_textarea\' ) {
    return false;
  }
  
  return $content;
  
}
add_filter( \'ot_tinymce\', \'filter_textarea_tinymce\', 10, 2 );</code></pre>';

        echo '<p>' . __( 'This example alters the <code>quicktags</code> filter on the textarea WYSIWYG. The default is <code>array( \'buttons\' => \'strong,em,link,block,del,ins,img,ul,ol,li,code,spell,close\' )</code> which means show those quicktags. It also means you can filter in your own custom quicktags.', 'option-tree' ) . '</p>';
        echo '<pre><code>function filter_textarea_quicktags( $content, $field_id ) {
  
  /* only run the filter on the textarea with a field ID of my_textarea */
  if ( $field_id == \'my_textarea\' ) {
    return array( \'buttons\' => \'strong,em,link,block,del,ins,img,ul,ol,li,code,more,spell,close,fullscreen\' );
  } else if ( $field_id == \'my_other_textarea\' ) {
    return false; /* show no quicktags */
  }
  
  return $content;
  
}
add_filter( \'ot_quicktags\', \'filter_textarea_quicktags\', 10, 1 );</code></pre>';

        echo '<h4>'. __( 'Textarea Simple', 'option-tree' ) . ':</h4>'; 
        echo '<p>' . __( 'The Textarea Simple option type is a large string value used for custom code or text in the theme. The Textarea Simple does not have a WYSIWYG editor.', 'option-tree' ) . '</p>';
        
        echo '<p class="aside">' . sprintf( __( 'This example tells WordPress to execute the %s filter on the line breaks. The default is %s which means it does not wraps line breaks with an HTML %s tag. Added to %s.', 'option-tree' ), '<code>wpautop</code>', '<code>false</code>', '<code>p</code>', '<code>functions.php</code>' ) . '</p>';
        echo '<pre><code>function filter_textarea_simple_wpautop( $content, $field_id ) {
  
  /* only run the filter on the textarea with a field ID of my_textarea */
  if ( $field_id == \'my_textarea\' ) {
    return true;
  }
  
  return $content;
  
}
add_filter( \'ot_wpautop\', \'filter_textarea_simple_wpautop\', 10, 2 );</code></pre>';
        
        echo '<h4>'. __( 'Textblock', 'option-tree' ) . ':</h4>'; 
        echo '<p>' . __( 'The Textblock option type is used only on the Theme Option page. It will allow you to create & display HTML, but has no title above the text block. You can then use the Textblock to add a more detailed set of instruction on how the options are used in your theme. You would never use this in your themes template files as it does not save a value.', 'option-tree' ) . '</p>';
        
        echo '<h4>'. __( 'Textblock Titled', 'option-tree' ) . ':</h4>'; 
        echo '<p>' . __( 'The Textblock Titled option type is used only on the Theme Option page. It will allow you to create & display HTML, and has a title above the text block. You can then use the Textblock Titled to add a more detailed set of instruction on how the options are used in your theme. You would never use this in your themes template files as it does not save a value.', 'option-tree' ) . '</p>';
        
        echo '<h4>'. __( 'Typography', 'option-tree' ) . ':</h4>';    
        echo '<p>' . sprintf( __( 'The Typography option type is for adding typography styles to your theme either dynamically via the CSS option type above or manually with %s. The Typography option type has filters that allow you to remove fields or change the defaults. For example, you can filter %s to remove unwanted fields from all Background options or an individual one. You can also filter %s. These filters allow you to fine tune the select lists for your specific needs.', 'option-tree' ), '<code>ot_get_option()</code>', '<code>ot_recognized_typography_fields</code>', '<code>ot_recognized_font_families</code>, <code>ot_recognized_font_sizes</code>, <code>ot_recognized_font_styles</code>, <code>ot_recognized_font_variants</code>, <code>ot_recognized_font_weights</code>, <code>ot_recognized_letter_spacing</code>, <code>ot_recognized_line_heights</code>, <code>ot_recognized_text_decorations</code> ' . __( 'and', 'option-tree' ) . ' <code>ot_recognized_text_transformations</code>' ) . '</p>';
        
        echo '<p class="aside">' . __( 'This example would filter <code>ot_recognized_font_families</code> to build your own font stack. Added to <code>functions.php</code>.', 'option-tree' ) . '</p>';
        echo '<pre><code>function filter_ot_recognized_font_families( $array, $field_id ) {
  
  /* only run the filter when the field ID is my_google_fonts_headings */
  if ( $field_id == \'my_google_fonts_headings\' ) {
    $array = array(
      \'sans-serif\'    => \'sans-serif\',
      \'open-sans\'     => \'"Open Sans", sans-serif\',
      \'droid-sans\'    => \'"Droid Sans", sans-serif\'
    );
  }
  
  return $array;
  
}
add_filter( \'ot_recognized_font_families\', \'filter_ot_recognized_font_families\', 10, 2 );</code></pre>';

        echo '<h4>'. __( 'Upload', 'option-tree' ) . ':</h4>'; 
        echo '<p>' . sprintf( __( 'The Upload option type is used to upload any WordPress supported media. After uploading, users are required to press the "%s" button in order to populate the input with the URI of that media. There is one caveat of this feature. If you import the theme options and have uploaded media on one site the old URI will not reflect the URI of your new site. You will have to re-upload or %s any media to your new server and change the URIs if necessary.', 'option-tree' ), apply_filters( 'ot_upload_text', __( 'Send to OptionTree', 'option-tree' ) ), 'FTP' ) . '</p>';
        
      echo '</div>';
      
    echo '</div>';
    
  }
  
}

/**
 * ot_get_option() option type.
 *
 * This is a callback function to display text about ot_get_option().
 *
 * @return    string
 *
 * @access    public
 * @since     2.0
 */
if ( ! function_exists( 'ot_type_ot_get_option' ) ) {
  
  function ot_type_ot_get_option() {
    
    /* format setting outer wrapper */
    echo '<div class="format-setting type-textblock wide-desc">';
      
      /* description */
      echo '<div class="description">';
        
        echo '<h4>'. __( 'Description', 'option-tree' ) . ':</h4>';
        
        echo '<p>' . __( 'This function returns a value from the "option_tree" array of saved values or the default value supplied. The returned value would be mixed. Meaning it could be a string, integer, boolean, or array.', 'option-tree' ) . '</p>';
        
        echo '<h4>' . __( 'Usage', 'option-tree' ) . ':</h4>';
        
        echo '<p><code>&lt;?php ot_get_option( $option_id, $default ); ?&gt;</code></p>';
        
        echo '<h4>' . __( 'Parameters', 'option-tree' ) . ':</h4>';
        
        echo '<code>$option_id</code>';
        
        echo '<p>(<em>' . __( 'string', 'option-tree' ) . '</em>) (<em>' . __( 'required', 'option-tree' ) . '</em>) ' . __( 'Enter the options unique identifier.', 'option-tree' ) . '<br />' . __( 'Default:', 'option-tree' ) . ' <em>' . __( 'None', 'option-tree' ) . '</em></p>';
        
        echo '<code>$default</code>';
        
        echo '<p>(<em>' . __( 'string', 'option-tree' ) . '</em>) (<em>' . __( 'optional', 'option-tree' ) . '</em>) ' . __( 'Enter a default return value. This is just incase the request returns null.', 'option-tree' ) . '<br />' . __( 'Default', 'option-tree' ) . ': <em>' . __( 'None', 'option-tree' ) . '</em></p>';
        
      echo '</div>';
      
    echo '</div>';
    
  }
  
}

/**
 * get_option_tree() option type.
 *
 * This is a callback function to display text about get_option_tree().
 *
 * @return    string
 *
 * @access    public
 * @since     2.0
 */
if ( ! function_exists( 'ot_type_get_option_tree' ) ) {
  
  function ot_type_get_option_tree() {
    
    /* format setting outer wrapper */
    echo '<div class="format-setting type-textblock wide-desc">';
      
      /* description */
      echo '<div class="description">';
        
        echo '<p class="deprecated">' . __( 'This function has been deprecated. That means it has been replaced by a new function or is no longer supported, and may be removed from future versions. All code that uses this function should be converted to use its replacement.', 'option-tree' ) . '</p>';
        
        echo '<p>' . __( 'Use', 'option-tree' ) . '<code>ot_get_option()</code>' . __( 'instead', 'option-tree' ) . '.</p>';
        
        echo '<h4>'. __( 'Description', 'option-tree' ) . ':</h4>';
        
        echo '<p>' . __( 'This function returns, or echos if asked, a value from the "option_tree" array of saved values.', 'option-tree' ) . '</p>';
        
        echo '<h4>' . __( 'Usage', 'option-tree' ) . ':</h4>';
        
        echo '<p><code>&lt;?php get_option_tree( $item_id, $options, $echo, $is_array, $offset ); ?&gt;</code></p>';
        
        echo '<h4>' . __( 'Parameters', 'option-tree' ) . ':</h4>';
        
        echo '<code>$item_id</code>';
        
        echo '<p>(<em>' . __( 'string', 'option-tree' ) . '</em>) (<em>' . __( 'required', 'option-tree' ) . '</em>) ' . __( 'Enter a unique Option Key to get a returned value or array.', 'option-tree' ) . '<br />' . __( 'Default:', 'option-tree' ) . ' <em>' . __( 'None', 'option-tree' ) . '</em></p>';
        
        echo '<code>$options</code>';
        
        echo '<p>(<em>' . __( 'array', 'option-tree' ) . '</em>) (<em>' . __( 'optional', 'option-tree' ) . '</em>) ' . __( 'Used to cut down on database queries in template files.', 'option-tree' ) . '<br />' . __( 'Default', 'option-tree' ) . ': <em>' . __( 'None', 'option-tree' ) . '</em></p>';
        
        echo '<code>$echo</code>';
        
        echo '<p>(<em>' . __( 'boolean', 'option-tree' ) . '</em>) (<em>' . __( 'optional', 'option-tree' ) . '</em>) ' . __( 'Echo the output.', 'option-tree' ) . '<br />' . __( 'Default', 'option-tree' ) . ': FALSE</p>';
        
        echo '<code>$is_array</code>';
        
        echo '<p>(<em>' . __( 'boolean', 'option-tree' ) . '</em>) (<em>' . __( 'optional', 'option-tree' ) . '</em>) ' . __( 'Used to indicate the $item_id is an array of values.', 'option-tree' ) . '<br />' . __( 'Default', 'option-tree' ) . ': FALSE</p>';
        
        echo '<code>$offset</code>';
        
        echo '<p>(<em>' . __( 'integer', 'option-tree' ) . '</em>) (<em>' . __( 'optional', 'option-tree' ) . '</em>) ' . __( 'Numeric offset key for the $item_id array, -1 will return all values (an array starts at 0).', 'option-tree' ) . '<br />' . __( 'Default', 'option-tree' ) . ': -1</p>';
        
      echo '</div>';
      
    echo '</div>';
    
  }
  
}

/**
 * Examples option type.
 *
 * @return    string
 *
 * @access    public
 * @since     2.0
 */
if ( ! function_exists( 'ot_type_examples' ) ) {
  
  function ot_type_examples() {
    
    /* format setting outer wrapper */
    echo '<div class="format-setting type-textblock wide-desc">';
      
      /* description */
      echo '<div class="description">';
        
        echo '<p class="aside">' . __( 'If you\'re using the plugin version of OptionTree it is highly recommended to include a <code>function_exists</code> check in your code, as described in the examples below. If you\'ve integrated OptionTree directly into your themes root directory, you will <strong>not</strong> need to wrap your code with <code>function_exists</code>, as you\'re guaranteed to have the <code>ot_get_option()</code> function available.', 'option-tree' ) . '</p>';
        
        echo '<h4>' . __( 'String Examples', 'option-tree' ) . ':</h4>';
        
        echo '<p>' . __( 'Returns the value of <code>test_input</code>.', 'option-tree' ) . '</p>';
        
        echo '<pre><code>if ( function_exists( \'ot_get_option\' ) ) {
  $test_input = ot_get_option( \'test_input\' );
}</code></pre>';

        echo '<p>' . __( 'Returns the value of <code>test_input</code>, but also has a default value if it returns empty.', 'option-tree' ) . '</p>';
        
        echo '<pre><code>if ( function_exists( \'ot_get_option\' ) ) {
  $test_input = ot_get_option( \'test_input\', \'default input value goes here.\' );
}</code></pre>';
        
        echo '<h4>' . __( 'Array Examples', 'option-tree' ) . ':</h4>';
        
        echo '<p>' . __( 'Assigns the value of <code>navigation_ids</code> to the variable <code>$ids</code>. It then echos an unordered list of links (navigation) using <code>wp_list_pages()</code>.', 'option-tree' ) . '</p>';

        echo '<pre><code>if ( function_exists( \'ot_get_option\' ) ) {
  /* get an array of page id\'s */
  $ids = ot_get_option( \'navigation_ids\', array() );

  /* echo custom navigation using wp_list_pages() */
  if ( ! empty( $ids ) )
    echo \'&lt;ul&gt;\';
    wp_list_pages(
      array(
        \'include\'   => $ids,
        \'title_li\'  => \'\'
      )
    );
    echo \'&lt;/ul&gt;\';
  }
  
}</code></pre>';   
        
        echo '<p>' . __( 'The next two examples demonstrate how to use the <strong>Measurement</strong> option type. The Measurement option type is an array with two key/value pairs. The first is the value of measurement and the second is the unit of measurement.', 'option-tree' ) . '</p>';
        
        echo '<pre><code>if ( function_exists( \'ot_get_option\' ) ) {
  /* get the array */
  $measurement = ot_get_option( \'measurement_option_type_id\' );
  
  /* only echo values if they actually exist, else echo some default value */
  if ( isset( measurement[0] ) && $measurement[1] ) {
    echo $measurement[0].$measurement[1];
  } else {
    echo \'10px\';
  }
  
}</code></pre>';

        echo '<pre><code>if ( function_exists( \'ot_get_option\' ) ) {
  /* get the array, and have a default just incase */
  $measurement = ot_get_option( \'measurement_option_type_id\', array( \'10\', \'px\' ) );
  
  /* implode array into a string value */
  if ( ! empty( measurement ) ) {
    echo implode( \'\', $measurement );
  }
  
}</code></pre>';    
          
        echo '<p>' . __( 'This example displays a very basic slider loop.', 'option-tree' ) . '</p>';
        
        echo '<pre><code>if ( function_exists( \'ot_get_option\' ) ) {
  
  /* get the slider array */
  $slides = ot_get_option( \'my_slider\', array() );
  
  if ( ! empty( $slides ) ) {
    foreach( $slides as $slide ) {
      echo \'
      &lt;li&gt;
        &lt;a href="\' . $slide[\'link\'] . \'"&gt;&lt;img src="\' . $slide[\'image\'] . \'" alt="\' . $slide[\'title\'] . \'" /&gt;&lt;/a&gt;
        &lt;div class="description">\' . $slide[\'description\'] . \'&lt;/div&gt;
      &lt;/li&gt;\';
    }
  }
  
}</code></pre>';
        
      echo '</div>';
      
    echo '</div>';
    
  }
  
}

/**
 * Layouts Overview option type.
 *
 * @return    string
 *
 * @access    public
 * @since     2.0
 */
if ( ! function_exists( 'ot_type_layouts_overview' ) ) {
  
  function ot_type_layouts_overview() {
    
    /* format setting outer wrapper */
    echo '<div class="format-setting type-textblock wide-desc">';
      
      /* description */
      echo '<div class="description">';
        
        echo '<h4>'. __( 'It\'s Super Simple', 'option-tree' ) . '</h4>';
        
        echo '<p>' . __( 'Layouts make your theme awesome! With theme options data that you can save/import/export you can package themes with different color variations, or make it easy to do A/B testing on text and so much more. Basically, you save a snapshot of your data as a layout.', 'option-tree' ) . '</p>';
        
        echo '<p>' . __( 'Once you have created all your different layouts, or theme variations, you can save them to a separate text file for repackaging with your theme. Alternatively, you could just make different variations for yourself and change your theme with the click of a button, all without deleting your previous options data.', 'option-tree' ) . '</p>';

        echo '<p class="aside">' . __( ' Adding a layout is ridiculously easy, follow these steps and you\'ll be on your way to having a WordPress super theme.', 'option-tree' ) . '</p>';
        
        echo '<h4>' . __( 'For Developers', 'option-tree' ) . ':</h4>';
        
        echo '<h5>' . __( 'Creating a Layout', 'option-tree' ) . ':</h5>';
        echo '<ul class="docs-ul">';
          echo '<li>'. __( 'Go to the <code>OptionTre->Settings->Layouts</code> tab.', 'option-tree' ) . '</li>';
          echo '<li>' . __( 'Enter a name for your layout in the text field and hit "Save Layouts", you\'ve created your first layout.', 'option-tree' ) . '</li>';
          echo '<li>' . __( 'Adding a new layout is as easy as repeating the steps above.', 'option-tree' ) . '</li>';
        echo '</ul>';
        
        echo '<h5>' . __( 'Activating a Layout', 'option-tree' ) . ':</h5>';
        echo '<ul class="docs-ul">';
          echo '<li>'. __( 'Go to the <code>OptionTre->Settings->Layouts</code> tab.', 'option-tree' ) . '</li>';
          echo '<li>' . __( 'Click on the activate layout button in the actions list.', 'option-tree' ) . '</li>';
        echo '</ul>';
        
        echo '<h5>' . __( 'Deleting a Layout', 'option-tree' ) . ':</h5>';
        echo '<ul class="docs-ul">';
          echo '<li>'. __( 'Go to the <code>OptionTre->Settings->Layouts</code> tab.', 'option-tree' ) . '</li>';
          echo '<li>' . __( 'Click on the delete layout button in the actions list.', 'option-tree' ) . '</li>';
        echo '</ul>';
        
        echo '<h5>' . __( 'Edit Layout Data', 'option-tree' ) . ':</h5>';
        echo '<ul class="docs-ul">';
          echo '<li>'. __( 'Go to the <code>Appearance->Theme Options</code> page.', 'option-tree' ) . '</li>';
          echo '<li>' . __( 'Modify and save your theme options and the layout will be updated automatically.', 'option-tree' ) . '</li>';
          echo '<li>' . __( 'Saving theme options data will update the currently active layout, so before you start saving make sure you want to modify the current layout.', 'option-tree' ) . '</li>';
          echo '<li>' . __( 'If you want to edit a new layout, first create it then save your theme options.', 'option-tree' ) . '</li>';
        echo '</ul>';

        echo '<h4>' . __( 'End-Users Mode', 'option-tree' ) . ':</h4>';
        
        echo '<h5>' . __( 'Creating a Layout', 'option-tree' ) . ':</h5>';
        echo '<ul class="docs-ul">';
          echo '<li>'. __( 'Go to the <code>Appearance->Theme Options</code> page.', 'option-tree' ) . '</li>';
          echo '<li>' . __( 'Enter a name for your layout in the text field and hit "New Layout", you\'ve created your first layout.', 'option-tree' ) . '</li>';
          echo '<li>' . __( 'Adding a new layout is as easy as repeating the steps above.', 'option-tree' ) . '</li>';
        echo '</ul>';
        
        echo '<h5>' . __( 'Activating a Layout', 'option-tree' ) . ':</h5>';
        echo '<ul class="docs-ul">';
          echo '<li>'. __( 'Go to the <code>Appearance->Theme Options</code> page.', 'option-tree' ) . '</li>';
          echo '<li>' . __( 'Choose a layout from the select list and click the "Activate Layout" button.', 'option-tree' ) . '</li>';
        echo '</ul>';
        
        echo '<h5>' . __( 'Deleting a Layout', 'option-tree' ) . ':</h5>';
        echo '<ul class="docs-ul">';
          echo '<li>'. __( 'End-Users mode does not allow deleting layouts.', 'option-tree' ) . '</li>';
        echo '</ul>';
        
        echo '<h5>' . __( 'Edit Layout Data', 'option-tree' ) . ':</h5>';
        echo '<ul class="docs-ul">';
          echo '<li>'. __( 'Go to the <code>Appearance->Theme Options</code> tab.', 'option-tree' ) . '</li>';
          echo '<li>' . __( 'Modify and save your theme options and the layout will be updated automatically.', 'option-tree' ) . '</li>';
          echo '<li>' . __( 'Saving theme options data will update the currently active layout, so before you start saving make sure you want to modify the current layout.', 'option-tree' ) . '</li>';
        echo '</ul>';
        
      echo '</div>';
      
    echo '</div>';
    
  }
  
}

/**
 * Meta Boxes option type.
 *
 * @return    string
 *
 * @access    public
 * @since     2.0
 */
if ( ! function_exists( 'ot_type_meta_boxes' ) ) {
  
  function ot_type_meta_boxes() {
    
    /* format setting outer wrapper */
    echo '<div class="format-setting type-textblock wide-desc">';
      
      /* description */
      echo '<div class="description">';
        
        echo '<h4>'. __( 'How-to-guide', 'option-tree' ) . '</h4>';
        
        echo '<p>' . __( 'There are a few simple steps you need to take in order to use OptionTree\'s built in Meta Box API. In the code below I\'ll show you a basic demo of how to create your very own custom meta box using any number of the option types you have at your disposal. If you would like to see some demo code, there is a directory named <code>theme-mode</code> inside the <code>assets</code> directory that contains a file named <code>demo-meta-boxes.php</code> you can reference.', 'option-tree' ) . '</p>';
        
        echo '<p>' . __( 'It\'s important to note that Meta Boxes do not support WYSIWYG editors at this time and if you set one of your options to Textarea it will automatically revert to a Textarea Simple until a valid solution is found. WordPress released this statement regarding the wp_editor() function:', 'option-tree' ) . '</p>';
        
        echo '<blockquote>' . __( 'Once instantiated, the WYSIWYG editor cannot be moved around in the DOM. What this means in practical terms, is that you cannot put it in meta-boxes that can be dragged and placed elsewhere on the page.', 'option-tree' ) . '</blockquote>';
        
        echo '<h5>' . __( 'Create and include your custom meta boxes file.', 'option-tree' ) . '</h5>';
        echo '<ul class="docs-ul">';
          echo '<li>'. __( 'Create a file and name it anything you want, maybe <code>meta-boxes.php</code>.', 'option-tree' ) . '</li>';
          echo '<li>'. __( 'As well, you\'ll probably want to create a directory named <code>includes</code> to put your <code>meta-boxes.php</code> into which will help keep you file structure nice and tidy.', 'option-tree' ) . '</li>';
          echo '<li>' . __( 'Add the following code to your <code>functions.php</code>.', 'option-tree' ) . '</li>';
        echo '</ul>';
        
        echo '<pre><code>/**
 * Meta Boxes
 */
load_template( trailingslashit( get_template_directory() ) . \'includes/meta-boxes.php\' );
</code></pre>';
        
        echo '<ul class="docs-ul">';
          echo '<li>' . __( 'Add a variation of the following code to your <code>meta-boxes.php</code>. You\'ll obviously need to fill it in with all your custom array values. It\'s important to note here that we use the <code>admin_init</code> filter because if you were to call the <code>ot_register_meta_box</code> function before OptionTree was loaded the sky would fall on your head.', 'option-tree' ) . '</li>';
        echo '</ul>';
        
        echo "<pre><code>/**
 * Initialize the meta boxes. 
 */
add_action( 'admin_init', 'custom_meta_boxes' );

function custom_meta_boxes() {

  &#36;my_meta_box = array(
    'id'        => 'my_meta_box',
    'title'     => 'My Meta Box',
    'desc'      => '',
    'pages'     => array( 'post' ),
    'context'   => 'normal',
    'priority'  => 'high',
    'fields'    => array(
      array(
        'id'          => 'background',
        'label'       => 'Background',
        'desc'        => '',
        'std'         => '',
        'type'        => 'background',
        'class'       => '',
        'choices'     => array()
      )
    )
  );
  
  ot_register_meta_box( &#36;my_meta_box );

}</code></pre>";  
        
      echo '</div>';
      
    echo '</div>';
    
  }
  
}

/**
 * Theme Mode option type.
 *
 * @return    string
 *
 * @access    public
 * @since     2.0
 */
if ( ! function_exists( 'ot_type_theme_mode' ) ) {
  
  function ot_type_theme_mode() {
  
    /* format setting outer wrapper */
    echo '<div class="format-setting type-textblock wide-desc">';
      
      /* description */
      echo '<div class="description">';
        
        echo '<h4>'. __( 'How-to-guide', 'option-tree' ) . '</h4>';
        
        echo '<p>' . __( 'There are a few simple steps you need to take in order to use OptionTree as a theme included module. In the code below I\'ll show you a basic demo of how to include the entire plugin as a module, which will allow you to have the most up-to-date version of OptionTree without ever needing to hack the core of the plugin. If you would like to see some demo code, there is a directory named <code>theme-mode</code> inside the <code>assets</code> directory that contains a file named <code>demo-theme-options.php</code> you can reference.', 'option-tree' ) . '</p>';
        
        echo '<h5>' . __( 'Step 1: Include the plugin & turn on theme mode.', 'option-tree' ) . '</h5>';
        echo '<ul class="docs-ul">';
          echo '<li>' . sprintf( __( 'Download the latest version of %s and unarchive the %s directory.', 'option-tree' ), '<a href="http://wordpress.org/extend/plugins/option-tree/" rel="nofollow" target="_blank">' . __( 'OptionTree', 'option-tree' ) . '</a>', '<code>.zip</code>' ) . '</li>';
          echo '<li>' . sprintf( __( 'Put the %s directory in the root of your theme. For example, the server path would be %s.', 'option-tree' ), '<code>option-tree</code>', '<code>/wp-content/themes/theme-name/option-tree/</code>' ) . '</li>';
          echo '<li>' . sprintf( __( 'Add the following code to the beginning of your %s.', 'option-tree' ), '<code>functions.php</code>' ) . '</li>';
        echo '</ul>';
        
        echo '<pre><code>/**
 * Required: set \'ot_theme_mode\' filter to true.
 */
add_filter( \'ot_theme_mode\', \'__return_true\' );

/**
 * Required: include OptionTree.
 */
load_template( trailingslashit( get_template_directory() ) . \'option-tree/ot-loader.php\' );
</code></pre>';
        
        echo '<p>' . sprintf( __( 'For a list of all the OptionTree UI display filters refer to the %s file found in the %s directory of this plugin. This file is the starting point for developing themes with Theme Mode.', 'option-tree' ), '<code>demo-functions.php</code>', '<code>/assets/theme-mode/</code>' ) . '</p>';
        
        echo '<p class="aside">' . __( 'You now have OptionTree built into your theme and anytime an update is available replace the old version with the new one.', 'option-tree' ) . '</p>';
        
        echo '<h5>' . __( 'Step 2: Create Theme Options without using the UI Builder.', 'option-tree' ) . '</h5>';
        echo '<ul class="docs-ul">';
          echo '<li>'. __( 'Create a file and name it anything you want, maybe <code>theme-options.php</code>, or use the built in file export to create it for you. Remember, you should always check the file for errors before including it in your theme.', 'option-tree' ) . '</li>';
          echo '<li>'. __( 'As well, you\'ll probably want to create a directory named <code>includes</code> to put your <code>theme-options.php</code> into which will help keep you file structure nice and tidy.', 'option-tree' ) . '</li>';
          echo '<li>' . __( 'Add the following code to your <code>functions.php</code>.', 'option-tree' ) . '</li>';
        echo '</ul>';
        
        echo '<pre><code>/**
 * Theme Options
 */
load_template( trailingslashit( get_template_directory() ) . \'includes/theme-options.php\' );
</code></pre>';
        
        echo '<ul class="docs-ul">';
          echo '<li>' . __( 'Add a variation of the following code to your <code>theme-options.php</code>. You\'ll obviously need to fill it in with all your custom array values for contextual help (optional), sections (required), and settings (required).', 'option-tree' ) . '</li>';
        echo '</ul>';
        
        echo '<p>' . __( 'The code below is a boilerplate to get your started. For a full list of the available option types click the "Option Types" tab above. Also a quick note, you don\'t need to put OptionTree in theme mode to manually create options but you will want to hide the docs and settings as each time you load the admin area the settings be written over with the code below if they\'ve changed in any way. However, this ensures your settings do not get tampered with by the end-user.', 'option-tree' ) . '</p>';
        
        echo "<pre><code>/**
 * Initialize the options before anything else. 
 */
add_action( 'admin_init', 'custom_theme_options', 1 );

/**
 * Build the custom settings & update OptionTree.
 */
function custom_theme_options() {
  /**
   * Get a copy of the saved settings array. 
   */
  &#36;saved_settings = get_option( 'option_tree_settings', array() );
  
  /**
   * Custom settings array that will eventually be 
   * passes to the OptionTree Settings API Class.
   */
  &#36;custom_settings = array(
    'contextual_help' => array(
      'content'       => array( 
        array(
          'id'        => 'general_help',
          'title'     => 'General',
          'content'   => '&lt;p&gt;Help content goes here!&lt;/p&gt;'
        )
      ),
      'sidebar'       => '&lt;p&gt;Sidebar content goes here!&lt;/p&gt;',
    ),
    'sections'        => array(
      array(
        'id'          => 'general',
        'title'       => 'General'
      )
    ),
    'settings'        => array(
      array(
        'id'          => 'my_checkbox',
        'label'       => 'Checkbox',
        'desc'        => '',
        'std'         => '',
        'type'        => 'checkbox',
        'section'     => 'general',
        'class'       => '',
        'choices'     => array(
          array( 
            'value' => 'yes',
            'label' => 'Yes' 
          )
        )
      ),
      array(
        'id'          => 'my_layout',
        'label'       => 'Layout',
        'desc'        => 'Choose a layout for your theme',
        'std'         => 'right-sidebar',
        'type'        => 'radio-image',
        'section'     => 'general',
        'class'       => '',
        'choices'     => array(
          array(
            'value'   => 'left-sidebar',
            'label'   => 'Left Sidebar',
            'src'     => OT_URL . '/assets/images/layout/left-sidebar.png'
          ),
          array(
            'value'   => 'right-sidebar',
            'label'   => 'Right Sidebar',
            'src'     => OT_URL . '/assets/images/layout/right-sidebar.png'
          ),
          array(
            'value'   => 'full-width',
            'label'   => 'Full Width (no sidebar)',
            'src'     => OT_URL . '/assets/images/layout/full-width.png'
          ),
          array(
            'value'   => 'dual-sidebar',
            'label'   => __( 'Dual Sidebar', 'option-tree' ),
            'src'     => OT_URL . '/assets/images/layout/dual-sidebar.png'
          ),
          array(
            'value'   => 'left-dual-sidebar',
            'label'   => __( 'Left Dual Sidebar', 'option-tree' ),
            'src'     => OT_URL . '/assets/images/layout/left-dual-sidebar.png'
          ),
          array(
            'value'   => 'right-dual-sidebar',
            'label'   => __( 'Right Dual Sidebar', 'option-tree' ),
            'src'     => OT_URL . '/assets/images/layout/right-dual-sidebar.png'
          )
        )
      ),
      array(
        'id'          => 'my_slider',
        'label'       => 'Images',
        'desc'        => '',
        'std'         => '',
        'type'        => 'list-item',
        'section'     => 'general',
        'class'       => '',
        'choices'     => array(),
        'settings'    => array(
          array(
            'id'      => 'slider_image',
            'label'   => 'Image',
            'desc'    => '',
            'std'     => '',
            'type'    => 'upload',
            'class'   => '',
            'choices' => array()
          ),
          array(
            'id'      => 'slider_link',
            'label'   => 'Link to Post',
            'desc'    => 'Enter the posts url.',
            'std'     => '',
            'type'    => 'text',
            'class'   => '',
            'choices' => array()
          ),
          array(
            'id'      => 'slider_description',
            'label'   => 'Description',
            'desc'    => 'This text is used to add fancy captions in the slider.',
            'std'     => '',
            'type'    => 'textarea',
            'class'   => '',
            'choices' => array()
          )
        )
      )
    )
  );
  
  /* settings are not the same update the DB */
  if ( &#36;saved_settings !== &#36;custom_settings ) {
    update_option( 'option_tree_settings', &#36;custom_settings ); 
  }
  
}
</code></pre>";
        
      echo '</div>';
      
    echo '</div>';
  
  }
  
}

/* End of file ot-functions-docs-page.php */
/* Location: ./includes/ot-functions-docs-page.php */