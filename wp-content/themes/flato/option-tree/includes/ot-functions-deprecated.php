<?php if ( ! defined( 'OT_VERSION' ) ) exit( 'No direct script access allowed' );
/**
 * OptionTree deprecated functions
 *
 * @package   OptionTree
 * @author    Derek Herman <derek@valendesigns.com>
 * @copyright Copyright (c) 2013, Derek Herman
 * @since     2.0
 */

/**
 * Displays or returns a value from the 'option_tree' array.
 *
 * @param       string    $item_id
 * @param       array     $options
 * @param       bool      $echo
 * @param       bool      $is_array
 * @param       int       $offset
 * @return      mixed     array or comma seperated lists of values
 *
 * @access      public
 * @since       1.0.0
 * @updated     2.0
 * @deprecated  2.0
 */
if ( ! function_exists( 'get_option_tree' ) ) {

  function get_option_tree( $item_id = '', $options = '', $echo = false, $is_array = false, $offset = -1 ) {
    /* load saved options */
    if ( ! $options )
      $options = get_option( 'option_tree' );
    
    /* no value return */
    if ( ! isset( $options[$item_id] ) || empty( $options[$item_id] ) )
      return;
    
    /* set content value & strip slashes */
    $content = option_tree_stripslashes( $options[$item_id] );
    
    /* is an array */
    if ( $is_array == true ) {
      /* saved as a comma seperated lists of values, explode into an array */
      if ( !is_array( $content ) )
        $content = explode( ',', $content );
    
      /* get an array value using an offset */
      if ( is_numeric( $offset ) && $offset >= 0 ) {
        $content = $content[$offset];
      } else if ( ! is_numeric( $offset ) && isset( $content[$offset] ) ) {
        $content = $content[$offset];
      }
    
    /* not an array */
    } else if ( $is_array == false ) {
      /* saved as array, implode and return a comma seperated lists of values */
      if ( is_array( $content ) )
        $content = implode( ',', $content ); /* This is fucked */
    }
    
    /* echo content */
    if ( $echo )
      echo $content;
    
    return $content;
  }

}

/**
 * Custom stripslashes from single value or array.
 *
 * @param       mixed $input
 * @return      mixed
 *
 * @access      public
 * @since       1.1.3
 * @deprecated  2.0
 */
if ( ! function_exists( 'option_tree_stripslashes' ) ) {

  function option_tree_stripslashes( $input ) {
    if ( is_array( $input ) ) {
      foreach( $input as &$val ) {
        if ( is_array( $val ) ) {
          $val = option_tree_stripslashes( $val );
        } else {
          $val = stripslashes( $val );
        }
      }
    } else {
      $input = stripslashes( $input );
    }
    return $input;
  }

}

/* End of file ot-functions-deprecated.php */
/* Location: ./includes/ot-functions-deprecated.php */