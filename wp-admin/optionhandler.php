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
    global $wpdb, $tableoptionvalues;
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
            $true_selected = ($option_result->option_value == '1') ? 'selected' : '';
            $false_selected = ($option_result->option_value == '0') ? 'selected' : '';
            return <<<BOOLSELECT
                    <label for="$option_result->option_name">$option_result->option_name</label>$between
                    <select name="$option_result->option_name" $disabled>
                    <option value="1" $true_selected>true</option>
                    <option value="0" $false_selected>false</option>
                    </select>
BOOLSELECT;
            //break;
            
        case 5: // select
            $ret = <<<SELECT
                    <label for="$option_result->option_name">$option_result->option_name</label>$between
                    <select name="$option_result->option_name" $disabled>
SELECT;

            $select = $wpdb->get_results("SELECT optionvalue, optionvalue_desc "
                                         ."FROM $tableoptionvalues "
                                         ."WHERE option_id = $option_result->option_id "
                                         ."ORDER BY optionvalue_seq");
            if ($select) {
                foreach($select as $option) {
                    $ret .= '<option value="'.$option->optionvalue.'"';
                    //error_log("comparing [$option_result->option_value] == [$option->optionvalue]");
                    if ($option_result->option_value == $option->optionvalue) {
                        $ret .=' selected';
                    }
                    $ret .= ">$option->optionvalue_desc</option>\n";
                }
            }
            $ret .= '</select>';
            return $ret;
            //break;
        
        case 7: // SQL select
            // first get the sql to run
            $sql = $wpdb->get_var("SELECT optionvalue FROM $tableoptionvalues WHERE option_id = $option_result->option_id");
            if (!$sql) {
                return $option_result->option_name . $editable;
            }

            // now we may need to do table name substitution
            eval("include('../wp-config.php');\$sql = \"$sql\";");

            $ret = <<<SELECT
                    <label for="$option_result->option_name">$option_result->option_name</label>$between
                    <select name="$option_result->option_name" $disabled>
SELECT;

            $select = $wpdb->get_results("$sql");
            if ($select) {
                foreach($select as $option) {
                    $ret .= '<option value="'.$option->value.'"';
                    //error_log("comparing [$option_result->option_value] == [$option->optionvalue]");
                    if ($option_result->option_value == $option->value) {
                        $ret .=' selected';
                    }
                    $ret .= ">$option->label</option>\n";
                }
            }
            $ret .= '</select>';
            return $ret;
            //break;

    } // end switch
    return $option_result->option_name . $editable;
} // end function get_option_widget


function validate_option($option, $name, $val) {
    global $wpdb, $tableoptionvalues;
    $msg = '';
    switch ($option->option_type) {
        case 6: // range
            // get range
            $range = $wpdb->get_row("SELECT optionvalue_max, optionvalue_min FROM $tableoptionvalues WHERE option_id = $option->option_id");
            if ($range) {
                if (($val < $range->optionvalue_min) || ($val > $range->optionvalue_max)) {
                    $msg = "$name is outside the valid range ($range->optionvalue_min - $range->optionvalue_max). ";
                }
            }
    } // end switch
    return $msg;
} // end validate_option
    
?>