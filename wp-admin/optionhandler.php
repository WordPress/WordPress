<?php
require_once('../wp-config.php');
/**
** get_option_widget()
** parameters:
** option_result - result set containing option_id, option_name, option_type,
**                 option_value, option_description, option_admin_level
** editable      - flag to determine whether the returned widget will be editable
**/
function get_option_widget($option_result, $editable, $between)
{
    global $wpdb;
    $disabled = $editable ? '' : 'disabled';

    switch ($option_result->option_type) {
        case 1: // integer
        case 3: // string
        case 8: // float
        case 6:  // range -- treat same as integer for now!
            if (($option_result->option_type == 1) || ($option_result->option_type == 1)) {
                $width = 6;
            } else {
                $width = $option_result->option_width;
            }
            return <<<TEXTINPUT
                    <label for="$option_result->option_name">$option_result->option_name</label>$between
                    <input type="text" name="$option_result->option_name" size="$width" value="$option_result->option_value" $disabled/>
TEXTINPUT;
            //break;

        case 2: // boolean
            $true_selected = ($option_result->option_value == '1') ? 'selected="selected"' : '';
            $false_selected = ($option_result->option_value == '0') ? 'selected="selected"' : '';
            return <<<BOOLSELECT
                    <label for="$option_result->option_name">$option_result->option_name</label>$between
                    <select name="$option_result->option_name" $disabled>
                    <option value="1" $true_selected>true</option>
                    <option value="0" $false_selected>false</option>
                    </select>
BOOLSELECT;
            //break;

    } // end switch
    return $option_result->option_name . $editable;
} // end function get_option_widget


function validate_option($option, $name, $val) {
    global $wpdb;
    $msg = '';
    return $msg;
} // end validate_option
    
?>