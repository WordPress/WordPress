<?php
require_once('../b2config.php');
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
    switch ($option_result->option_type)
    {
        case 1: // integer
        case 3: // string
        case 6: // range -- treat same as integer for now!
        {
            if (($option_result->option_type == 1) || ($option_result->option_type == 1))
                $width = 6;
            else
                $width = $option_result->option_width;
            return <<<TEXTINPUT
                    <label for="$option_result->option_name">$option_result->option_name</label>$between
                    <input type="text" name="$option_result->option_name" size="$width" value="$option_result->option_value" />
TEXTINPUT;
            //break;
        }
        case 2: // boolean
        {
            $true_selected = ($option_result->option_value == 'true') ? 'selected' : '';
            $false_selected = ($option_result->option_value == 'false') ? 'selected' : '';
            return <<<BOOLSELECT
                    <label for="$option_result->option_name">$option_result->option_name</label>$between
                    <select name="$option_result->option_name">
                    <option $true_selected>true</option>
                    <option $false_selected>false</option>
                    </select>
BOOLSELECT;
            //break;
        }
        case 5: // select
        {
            $ret = <<<SELECT
                    <label for="$option_result->option_name">$option_result->option_name</label>$between
                    <select name="$option_result->option_name">
SELECT;

            $select = $wpdb->get_results("SELECT optionvalue, optionvalue_desc, optionvalue_max, optionvalue_min "
                                         ."FROM $tableoptionvalues "
                                         ."WHERE option_id = $option_result->option_id "
                                         ."ORDER BY optionvalue_seq");
            if ($select)
            {
                foreach($select as $option)
                {
                    $ret .= '<option value="'.$option->optionvalue.'"';
                    //error_log("comparing [$option_result->option_value] == [$option->optionvalue]");
                    if ($option_result->option_value == $option->optionvalue)
                    {
                        $ret .=' selected';
                    }
                    $ret .= ">$option->optionvalue_desc</option>\n";
                }
            }
            $ret .= '</select>';
            return $ret;
            //break;
        }

    } // end switch
    return $option_result->option_name . $editable;
} // end function get_option_widget
?>