<?php

function slider_select($atts, $values, $current_value = null, $select_one = false, $unidimensional = false) {
    if(is_string($atts)){
        $html_atts = 'name="'. $atts . '"';
    } else if(is_array($atts)){
        foreach($atts as $key => $value){
            $html_atts[] = $key .'="' . $value . '"';
        }
        $html_atts = implode(' ', $html_atts);
    }
    $select = '<select ' . $html_atts . '>';
    if($select_one){
        $select .= '<option value="" ' . ('' == $current_value ? 'selected="selected"' : '') . '>' . __('Select one', 'wp-slider') . '</option>';
    }
    foreach ($values as $value => $title) {
        if($unidimensional === true){
            $value = $title;
        }
        $select .= '<option value="' . $value . '" ' . ($value == $current_value ? 'selected="selected"' : '') . '>' . __($title, 'wp-slider') . '</option>';
    }
    $select .= '</select>';
    return $select;
}

function slider_add_menu_page() {
    global $wp_slider;
    switch ($_GET['controller']) {
        case '':
        case 'manage-slider':

            switch ($_GET['action']) {
                case 'edit':
                    $slider_id = $_GET['slider_id']
?>
                    <div class="wrap">
                        <h2>WP Slider</h2><br/>
    <?php
                    $_GET['delete_id'] = isset($_GET['delete_id']) ? $_GET['delete_id'] : 0;
                    if (!empty($_POST) || intval($_GET['delete_id'] != 0)):
                        
                        $result = true;
                        
                        if ($_POST['form'] == 'slider') {
                            $slider_id = $wp_slider->save_slider($_POST);
                        } else if ($_POST['form'] == 'slider-elements') {
                            $wp_slider->save_slider_elements($_POST);
                        }
                        if(intval($_GET['delete_id'] != 0)){
                            $wp_slider->delete_slider_element('id = ' . $_GET['delete_id']);
                        }
    ?>
                        <div class="updated fade" id="message"><p><strong><?php _e('Options saved'); ?></strong></p></div>
    <?php endif; ?>
    <?php
                        $slider = $wp_slider->get_slider($slider_id);
    ?>
                        <form action="" method="post" id="form-slider">
                            <div id="col-container">
                                <div id="col-right">
                                    <div id="poststuff">

                                        <div class="postbox">
                                            <h3><?php _e('Slider example', 'wp-slider'); ?></h3>
                                            <div class="inside">
                                                <br/>
                                                <div id="slider-example">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <img alt="" src="../wp-content/plugins/wp-slider/example/slide-<?php echo $i; ?>.jpg" />
                                <?php endfor; ?>
                                </div>
                                <br/>
                            </div>
                        </div>
                          <div class="postbox">
                              <h3><?php _e('Slider code', 'wp-slider'); ?></h3>
                              <div class="inside">
                                  <?php _e('Use the following code to insert this slider in your template:','wp-slider'); ?>
                                  <code style="padding:10px 10px; margin:9px; display:block; text-align:center;">
                                      <?php echo "&lt?php if(function_exists('wp_slider')): wp_slider('{$slider['name']}'); endif; ?&gt" ;?>
                                  </code>
                              </div>
                          </div>
                    </div>                                    
                </div>
                <div id="col-left">
                    <div id="poststuff">
                        <div class="postbox">
                            <input type="hidden" name="id" value="<?php echo $slider['id']; ?>" />
                            <input type="hidden" name="form" value="slider" />
                            <h3>Slider settings</h3>
                            <div class="inside">
                                <table class="form-table">
                                    <tbody>
                                        <tr>
                                            <th align="left"><label for="name"><?php _e('Name', 'wp-slider'); ?>:</label></th>
                                            <td align="left"><input type="text" value="<?php echo $slider['name']; ?>" name="name" id="name" size="20"></td>
                                        </tr>
                                        <tr>
                                            <th align="left"><label for="width"><?php _e('Width', 'wp-slider'); ?>:</label></th>
                                            <td align="left"><input type="text" value="<?php echo $slider['options']['width']; ?>" name="width" id="width" size="10"> px</td>
                                        </tr>
                                        <tr>
                                            <th align="left"><label for="height"><?php _e('Height', 'wp-slider'); ?>:</label></th>
                                            <td align="left"><input type="text" value="<?php echo $slider['options']['height']; ?>" name="height" id="height" size="10"> px</td>
                                        </tr>                                        
                                        <tr>
                                            <th align="left"><label for="effect"><?php _e('Effect', 'wp-slider'); ?>:</label></th>
                                            <td align="left"><?php echo slider_select(array('name' => 'effect', 'id' => 'effect', 'class' => 'examplified'), $wp_slider->effects, $slider['effect']['effect'], true, true); ?></td>
                                        </tr>
                                        <tr>
                                        <th align="left"><label for="easing"><?php _e('Easing', 'wp-slider'); ?>:</label></th>
                                        <td align="left"><?php echo slider_select(array('name' => 'easing', 'id' => 'easing', 'class' => 'examplified'), $wp_slider->easings, $slider['effect']['easing'], true, true); ?></td>
                                        </tr>
                                                <tr>
                                                    <th align="left"><label for="frecuency"><?php _e('Frecuency', 'wp-slider'); ?>:</label></th>
                                                    <td align="left"><input type="text" class="examplified" value="<?php echo $slider['effect']['frecuency']; ?>" name="frecuency" id="frecuency" size="10"> <?php _e('seconds', 'wp-slider'); ?></td>
                                                </tr>
                                                <tr>
                                                    <th align="left"><label for="delay"><?php _e('Delay', 'wp-slider'); ?>:</label></th>
                                                    <td align="left"><input type="text" class="examplified" value="<?php echo $slider['effect']['delay']; ?>" name="delay" id="delay" size="10"> <?php _e('seconds', 'wp-slider'); ?></td>
                                                </tr>
                                                <tr>
                                                    <th align="left"><label for="before"><?php _e('onBefore function name', 'wp-slider'); ?>:</label></th>
                                                    <td align="left"><input type="text" value="<?php echo $slider['effect']['before']; ?>" name="before" id="before"></td>
                                                </tr>

                                                <tr>
                                                    <th align="left"><label for="after"><?php _e('onAfter function name', 'wp-slider'); ?>:</label></th>
                                                    <td align="left"><input type="text" value="<?php echo $slider['effect']['after']; ?>" name="after" id="after"></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <div class="submit"><input type="submit" value="Save Changes" class="button-primary action"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
    <?php if (isset($_GET['slider_id'])): ?>
    <?php $slider_elements = $wp_slider->get_slider_elements($_GET['slider_id']); ?>
                                                <div class="tablenav">
                                                    <div class="alignleft actions">
                                                        <select name="bulkaction" id="bulkaction">
                                                            <option value="" selected="selected"><?php _e('No action', 'wp-slider'); ?></option>
                                                            <option value="delete_all"><?php _e('Delete all', 'wp-slider'); ?></option>
                                                        </select>
                                                        <input type="submit" name="showThickbox" class="button-secondary" value="Apply" onclick="if ( !checkSelected() ) return false;">
                                                        <input type="button" class="button-secondary action" onclick="showAddSlide(); return false;" value="Add new slide">
                                                    </div>
                                                </div>
                                                <form method="post" action="" enctype="multipart/form-data">
                                                    <input type="hidden" name="form" value="slider-elements" />
                                                    <table cellspacing="0" class="widefat fixed">
                                                        <thead>
                                                            <tr>
                                                                <th class="manage-column check-column" id="cb" scope="col"><input type="checkbox" onclick="checkAll(document.getElementById('updategallery'));" name="checkall"></th>
                                                                <th class="manage-column" style="width:210px" scope="col"><?php _e('Filename', 'wp-slider'); ?></th>
                                                                <th class="manage-column" scope="col"><?php _e('Title', 'wp-slider'); ?> / <?php _e('Content', 'wp-slider'); ?></th>
                                                                <th class="manage-column" scope="col"><?php _e('URL', 'wp-slider'); ?></th>
                                                                <th class="manage-column column-rating" scope="col"><?php _e('Order', 'wp-slider'); ?></th>
                                                                <th class="manage-column column-rating" scope="col"><?php _e('Status', 'wp-slider'); ?></th>
                                                                <th class="manage-column column-visible" scope="col"><?php _e('Action', 'wp-slider'); ?></th>
                                                            </tr>
                                                        </thead>
                                                        <tfoot>
                                                            <tr>
                                                                <th class="manage-column check-column" id="cb" scope="col"><input type="checkbox" onclick="checkAll(document.getElementById('updategallery'));" name="checkall"></th>
                                                                <th class="manage-column" style="width:210px" scope="col"><?php _e('Filename', 'wp-slider'); ?></th>
                                                                <th class="manage-column" scope="col"><?php _e('Title', 'wp-slider'); ?> / <?php _e('Content', 'wp-slider'); ?></th>
                                                                <th class="manage-column" scope="col"><?php _e('URL', 'wp-slider'); ?></th>
                                                                <th class="manage-column column-rating" scope="col"><?php _e('Order', 'wp-slider'); ?></th>
                                                                <th class="manage-column column-rating" scope="col"><?php _e('Status', 'wp-slider'); ?></th>
                                                                <th class="manage-column column-visible" scope="col"><?php _e('Action', 'wp-slider'); ?></th>
                                                            </tr>
                                                        </tfoot>
                                                        <tbody>
                                                            <tr style="display:none;" valign="top" id="new-slide-element">
                                                                <th scope="row" class="check-column"><input type="hidden" name="id" value="<?php echo $slider['id']; ?>" /><input type="hidden" value="<?php echo $element['id']; ?>" name="sid[<?php echo $element['id']; ?>]"></th>
                                                                <td><input type="file" name="filename[0]"/></td>
                                                                <td><input class="multilanguage-input" name="title[0]" type="text" style="width: 95%; margin-bottom: 2px;"><br/><textarea class="multilanguage-input" rows="2" style="width: 95%;" name="description[0]"></textarea></td>
                                                                <td><input name="url[0]" type="text" value="http://" style="width: 95%; margin-bottom: 2px;"><br/><?php _e('Target', 'wp-slider'); ?>: <?php
                                                $arr_target = array(
                                                    '_blank' => 'blank',
                                                    '_parent' => 'parent',
                                                    '_self' => 'self'
                                                );
                                                echo slider_select('target[0]', $arr_target/*, $element['target']*/);
    ?></td>
                                            <td><input class="small-text" type="text" style="width: 95%; margin-bottom: 2px;" name="order[0]" value="0"></td>
                                            <td></td>
                                            <td><a href="#" onclick="jQuery(this).parents('tr').hide(); return false;"><?php _e('Cancel'); ?></a></td>
                                        </tr>

                <?php if (count($slider_elements) > 0): ?>
                <?php foreach ($slider_elements as $element): ?>
                                                        <tr valign="top" id="slide-element-<?php echo $element['id']; ?>">
                                                            <th scope="row" class="check-column"><input type="checkbox" value="<?php echo $element['id']; ?>" name="doaction[]"><input type="hidden" value="<?php echo $element['id']; ?>" name="sid[<?php echo $element['id']; ?>]"></th>
                                                            <td><input style="display:none;" type="file" name="filename[<?php echo $element['id']; ?>]" /><input type="hidden" name="current_filename[<?php echo $element['id']; ?>]" value="<?php echo $element['filename']; ?>" /> <span class="filename"><?php echo $element['filename']; ?></span> (<a href="#" class="change-file"><?php _e('change'); ?></a>)</td>
                                                            <td><input class="multilanguage-input" name="title[<?php echo $element['id']; ?>]" type="text" value="<?php echo $element['title']; ?>" style="width: 95%; margin-bottom: 2px;"><br/><textarea class="multilanguage-input" rows="2" style="width: 95%;" name="description[<?php echo $element['id']; ?>]"><?php echo stripslashes($element['description']); ?></textarea></td>
                                                            <td><input name="url[<?php echo $element['id']; ?>]" type="text" value="<?php echo ($element['url'] == '') ? 'http://' : $element['url']; ?>" style="width: 95%; margin-bottom: 2px;"><br/><?php _e('Target', 'wp-slider'); ?>: <?php
                                                        $arr_target = array(
                                                            '_blank' => 'blank',
                                                            '_parent' => 'parent',
                                                            '_self' => 'self'
                                                        );
                                                        echo slider_select('target[' . $element['id'] . ']', $arr_target, $element['target']);
                ?></td>
                                                    <td><input class="small-text" type="text" value="<?php echo $element['order']; ?>" style="width: 95%; margin-bottom: 2px;" name="order[<?php echo $element['id']; ?>]"></td>
                                                    <td><?php
                                                        $arr_status = array(
                                                            'active' => 'active',
                                                            'inactive' => 'inactive'
                                                        );
                                                        echo slider_select('status[' . $element['id'] . ']', $arr_status, $element['status']);
                ?></td>
                                                    <td><a onclick="return deleteSlide('<?php _e('Delete this slider?', 'wp-slider'); ?>')" href="<?php echo str_replace('&delete_id='.$_GET['delete_id'],'',$_SERVER['REQUEST_URI']);?>&delete_id=<?php echo $element['id'];?>"><?php _e('Delete', 'wp-slider'); ?></a></td>
                                                </tr>
                <?php endforeach; ?>
                <?php else: ?>
                                                            <tr><td colspan="7"><?php _e('There are no slides yet', 'wp-slider'); ?></td></tr>
                <?php endif; ?>
                                                        </tbody>
                                                    </table>
                                                    <div class="submit">
                                                        <input type="submit" value="<?php _e('Save Changes','wp-slider'); ?>" class="button-primary action">
                                                    </div>
                                                </form>


    <?php endif; ?>
                                                        </div>
<?php
                                                            break;

                                                        /* Default action */
                                                        default:
                                                            $sliders = $wp_slider->get_sliders();
?>
<?php
                                                            if (intval($_GET['delete_id']) != 0):
                                                                $wp_slider->delete_slider($_GET['delete_id']);
?>
                                                                <div class="updated fade" id="message"><p><strong><?php _e('Options saved.','wp-slider'); ?></strong></p></div>
<?php endif; ?>
                                                                <div class="wrap">
                                                                    <h2>WP Slider</h2>
<p>WP Slider is a jQuery based image slide show script that can display specific images on your WordPress based website with a lots of transition effects. For this purpose it uses the jQuery's plugin jQuery Cycle.</p>
                                                                    <p>Powerful, elegant and easy to use.</p>
                                                                    <br/>
                                                                    <iframe src="//www.facebook.com/plugins/likebox.php?href=https%3A%2F%2Fwww.facebook.com%2Fpages%2FFractalia-Applications-lab%2F256488141090473&amp;width=315&amp;height=62&amp;colorscheme=light&amp;show_faces=false&amp;border_color&amp;stream=false&amp;header=false&amp;appId=289529901105513" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:315px; height:62px;" allowTransparency="true"></iframe>
                                                                    <br/><br/>
                                                                    <form accept-charset="utf-8" action="admin.php?page=wp-slider/wp-slider.php" method="POST">
                                                                        <div class="tablenav">
                                                                            <div class="alignleft actions">
                                                                                <select id="bulkaction" name="bulkaction">
                                                                                    <option value="no_action">No action</option>                                                                                    
                                                                                </select>
                                                                                <input type="submit" onclick="if ( !checkSelected() ) return false;" value="Apply" class="button-secondary" name="showThickbox">
                                                                                <input type="button" value="<?php _e('Add new slider','wp-slider'); ?>" onclick="showAddSlider(); return false;" class="button-primary action" name="doaction">
                                                                            </div>


                                                                        </div>
                                                                        <table cellspacing="0" class="widefat">
                                                                            <thead>
                                                                                <tr>
                                                                                    <th class="column-cb check-column" scope="col">
                                                                                        <input type="checkbox" name="checkall" onclick="checkAll(document.getElementById('editgalleries'));">
                                                                                    </th>
                                                                                    <th scope="col">ID</th>
                                                                                    <th scope="col"><?php _e('Name', 'wp-slider'); ?></th>
                                                                                    <th scope="col">Key</th>
                                                                                    <th scope="col"><?php _e('Action', 'wp-slider'); ?></th>
                                                                                </tr>
                                                                            </thead>
                                                                            <tfoot>
                                                                                <tr>
                                                                                    <th class="column-cb check-column" scope="col">
                                                                                        <input type="checkbox" name="checkall" onclick="checkAll(document.getElementById('editgalleries'));">
                                                                                    </th>
                                                                                    <th scope="col">ID</th>
                                                                                    <th scope="col"><?php _e('Name', 'wp-slider'); ?></th>
                                                                                    <th scope="col">Key</th>
                                                                                    <th scope="col"><?php _e('Action', 'wp-slider'); ?></th>
                                                                                </tr>
                                                                            </tfoot>
                                                                            <tbody>
                <?php if (count($sliders) > 0): ?>
                <?php foreach ($sliders as $slider): ?>
                                                                        <tr id="slider-<?php $slider['id']; ?>">
                                                                            <th class="cb column-cb check-column" scope="row">
                                                                                <input type="checkbox" value="1" name="doaction[]">
                                                                            </th>
                                                                            <td scope="row"><?php echo $slider['id']; ?></td>
                                                                            <td><a title="Modifier" class="edit" href="?page=wp-slider/wp-slider.php&controller=manage-slider&action=edit&slider_id=<?php echo $slider['id']; ?>"><?php echo $slider['name']; ?></a></td>
                                                                            <td><?php echo $slider['key']; ?></td>
                                                                            <td>
                                                                                <a onclick="return deleteSlide('<?php _e('Delete this slider?', 'wp-slider'); ?>')" href="<?php echo str_replace('&delete_id='.$_GET['delete_id'],'',$_SERVER['REQUEST_URI']);?>&delete_id=<?php echo $slider['id']; ?>"><?php _e('Delete', 'wp-slider'); ?></a>
                                                                            </td>
                                                                        </tr>
                <?php endforeach; ?>
                <?php else: ?>
                                                                            <tr><td colspan="5"><?php _e('There are not sliders yet', 'wp-slider'); ?></td></tr>
                <?php endif; ?>
                                                                        </tbody>
                                                                    </table>
                                                                    <div class="tablenav"></div>
                                                                </form>
                                                            </div>
<?php
                                                                            break;
                                                                    }

                                                                    break;
                                                            }
                                                        }
?>
