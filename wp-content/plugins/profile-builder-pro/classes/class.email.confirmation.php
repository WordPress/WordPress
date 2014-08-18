<?php
 /*
Code taken from: Custom List Table Example (plugin)
Author: Matt Van Andel
Author URI: http://www.mattvanandel.com
*/

/*************************** LOAD THE BASE CLASS *******************************
 *******************************************************************************
 * The WP_List_Table class isn't automatically available to plugins, so we need
 * to check if it's available and load it if necessary.
 */
if(!class_exists('WP_List_Table')){
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}




/************************** CREATE A PACKAGE CLASS *****************************
 *******************************************************************************
 * Create a new list table package that extends the core WP_List_Table class.
 * WP_List_Table contains most of the framework for generating the table, but we
 * need to define and override some methods so that our data can be displayed
 * exactly the way we need it to be.
 * 
 * To display this example on a page, you will first need to instantiate the class,
 * then call $yourInstance->prepare_items() to handle any data manipulation, then
 * finally call $yourInstance->display() to render the table to the page.
 * 
 */
class wpp_list_unfonfirmed_email_table extends WP_List_Table {
    
    /** ************************************************************************
     * REQUIRED. Set up a constructor that references the parent constructor. We 
     * use the parent reference to set some default configs.
     ***************************************************************************/
    function __construct(){
        global $status, $page;
		global $wpdb;
                
        //Set parent defaults
        parent::__construct( array(
            'singular'  => 'user',     //singular name of the listed records
            'plural'    => 'users',    //plural name of the listed records
            'ajax'      => false        //does this table support ajax?
        ) );
        
    }
    
    
    /** ************************************************************************
     * Recommended. This method is called when the parent class can't find a method
     * specifically build for a given column. Generally, it's recommended to include
     * one method for each column you want to render, keeping your package class
     * neat and organized. For example, if the class needs to process a column
     * named 'username', it would first see if a method named $this->column_title() 
     * exists - if it does, that method will be used. If it doesn't, this one will
     * be used. Generally, you should try to use custom column methods as much as 
     * possible. 
     * 
     * Since we have defined a column_title() method later on, this method doesn't
     * need to concern itself with any column with a name of 'username'. Instead, it
     * needs to handle everything else.
     * 
     * For more detailed insight into how columns are handled, take a look at 
     * WP_List_Table::single_row_columns()
     * 
     * @param array $item A singular item (one full row's worth of data)
     * @param array $column_name The name/slug of the column to be processed
     * @return string Text or HTML to be placed inside the column <td>
     **************************************************************************/
    function column_default($item, $column_name){
        switch($column_name){
            case 'email':
            case 'registered':
                return $item[$column_name];
            default:
                return print_r($item,true); //Show the whole array for troubleshooting purposes
        }
    }
    
        
    /** ************************************************************************
     * Recommended. This is a custom column method and is responsible for what
     * is rendered in any column with a name/slug of 'username'. Every time the class
     * needs to render a column, it first looks for a method named 
     * column_{$column_title} - if it exists, that method is run. If it doesn't
     * exist, column_default() is called instead.
     * 
     * This example also illustrates how to implement rollover actions. Actions
     * should be an associative array formatted as 'slug'=>'link html' - and you
     * will need to generate the URLs yourself. You could even ensure the links
     * 
     * 
     * @see WP_List_Table::::single_row_columns()
     * @param array $item A singular item (one full row's worth of data)
     * @return string Text to be placed inside the column <td>
     **************************************************************************/
    function column_username($item){
		
		$GRavatar = get_avatar( $item['email'], 32, '' );
		
        //Build row actions
        $actions = array(
            'delete'    => sprintf('<a href="javascript:confirmECAction(\'%s\',\'%s\',\'%s\',\''.__('delete this user from the _signups table?', 'profilebuilder').'\')">'. __('Delete', 'profilebuilder') .'</a>',wppb_curpageurl(),'delete',$item['ID']),
            'confirm'    => sprintf('<a href="javascript:confirmECAction(\'%s\',\'%s\',\'%s\',\''.__('confirm this email yourself?', 'profilebuilder').'\')">'. __('Confirm Email', 'profilebuilder') .'</a>',wppb_curpageurl(),'confirm',$item['ID'])/* ,
            'resend'    => sprintf('<a href="javascript:confirmECAction(\'%s\',\'%s\',\'%s\',\''.__('resend the activation link?', 'profilebuilder').'\')">'. __('Resend Activation Email', 'profilebuilder') .'</a>',wppb_curpageurl(),'resend',$item['ID']) */
        );
        
        //Return the user row
        return sprintf('%1$s <strong>%2$s</strong> %3$s',
            /*$1%s*/ $GRavatar,
            /*$2%s*/ $item['username'],
            /*$3%s*/ $this->row_actions($actions)
        );
    }
    
    /** ************************************************************************
     * REQUIRED if displaying checkboxes or using bulk actions! The 'cb' column
     * is given special treatment when columns are processed. It ALWAYS needs to
     * have it's own method.
     * 
     * @see WP_List_Table::::single_row_columns()
     * @param array $item A singular item (one full row's worth of data)
     * @return string Text to be placed inside the column <td>
     **************************************************************************/
    function column_cb($item){
        return sprintf(
            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
            /*$1%s*/ $this->_args['singular'],  //Let's simply repurpose the table's singular label
            /*$2%s*/ $item['ID']                //The value of the checkbox should be the record's id
        );
    }
    
    
    /** ************************************************************************
     * REQUIRED! This method dictates the table's columns and titles. This should
     * return an array where the key is the column slug (and class) and the value 
     * is the column's title text. If you need a checkbox for bulk actions, refer
     * to the $columns array below.
     * 
     * The 'cb' column is treated differently than the rest. If including a checkbox
     * column in your table you must create a column_cb() method. If you don't need
     * bulk actions or checkboxes, simply leave the 'cb' entry out of your array.
     * 
     * @see WP_List_Table::::single_row_columns()
     * @return array An associative array containing column information: 'slugs'=>'Visible Titles'
     **************************************************************************/
    function get_columns(){
        $columns = array(
            'cb'        	=> '<input type="checkbox" />', //Render a checkbox instead of text
            'username'     	=> __('Username', 'profilebuilder'),
            'email'    		=> __('E-mail', 'profilebuilder'),
            'registered'  	=> __('Registered', 'profilebuilder')
        );
        return $columns;
    }
    
    /** ************************************************************************
     * Optional. If you want one or more columns to be sortable (ASC/DESC toggle), 
     * you will need to register it here. This should return an array where the 
     * key is the column that needs to be sortable, and the value is db column to 
     * sort by. Often, the key and value will be the same, but this is not always
     * the case (as the value is a column name from the database, not the list table).
     * 
     * This method merely defines which columns should be sortable and makes them
     * clickable - it does not handle the actual sorting. You still need to detect
     * the ORDERBY and ORDER querystring variables within prepare_items() and sort
     * your data accordingly (usually by modifying your query).
     * 
     * @return array An associative array containing all the columns that should be sortable: 'slugs'=>array('data_values',bool)
     **************************************************************************/
    function get_sortable_columns() {
        $sortable_columns = array(
            'username'     	=> array('username',false),     //true means it's already sorted
            'email'    		=> array('email',false),
            'registered'  	=> array('registered',false)
        );
        return $sortable_columns;
    }
    
    
    /** ************************************************************************
     * Optional. If you need to include bulk actions in your list table, this is
     * the place to define them. Bulk actions are an associative array in the format
     * 'slug'=>'Visible Title'
     * 
     * If this method returns an empty value, no bulk action will be rendered. If
     * you specify any bulk actions, the bulk actions box will be rendered with
     * the table automatically on display().
     * 
     * Also note that list tables are not automatically wrapped in <form> elements,
     * so you will need to create those manually in order for bulk actions to function.
     * 
     * @return array An associative array containing all the bulk actions: 'slugs'=>'Visible Titles'
     **************************************************************************/
    function get_bulk_actions() {
        $actions = array(
            'delete'    => __('Delete', 'profilebuilder'),
			'confirm'	=> __('Confirm Email', 'profilebuilder')
        );
        return $actions;
    }
    
    
    /** ************************************************************************
     * Optional. You can handle your bulk actions anywhere or anyhow you prefer.
     * For this example package, we will handle it in the class to keep things
     * clean and organized.
     * 
     * @see $this->prepare_items()
     **************************************************************************/
    function process_bulk_action() {
		global $current_user;
		global $wpdb;
		
		if (current_user_can('delete_users')){
			$iterator = 0;
			$bulkResult = mysql_query("SELECT * FROM ".$wpdb->prefix."signups WHERE active=0");			
		
			//Detect when a bulk action is being triggered...
			if( 'delete'===$this->current_action() ) {	
				while ($bulkRow=mysql_fetch_row($bulkResult)){
					if (in_array((string)$iterator, $_GET['user'])){
						$bulkResult1 = mysql_query("DELETE FROM ".$wpdb->prefix."signups WHERE user_login='".$bulkRow[3]."' AND user_email='".$bulkRow[4]."'");
						if (!$bulkResult1){
							$message = $bulkRow[3] . __("couldn't be deleted.", "profilebuilder");
							?>
							<script type="text/javascript">
								confirmECActionBulk('<?php echo get_bloginfo('url').'/wp-admin/users.php?page=unconfirmed_emails';?>', '<?php echo $message;?>');
							</script>
							<?php
						}
					}
					$iterator++;
				}
				
				$message = __("All users have been successfully deleted.", "profilebuilder");
				?>
				<script type="text/javascript">
					confirmECActionBulk('<?php echo get_bloginfo('url').'/wp-admin/users.php?page=unconfirmed_emails';?>', '<?php echo $message;?>');
				</script>
				<?php
			
			}elseif( 'confirm'===$this->current_action() ) {
				while ($bulkRow=mysql_fetch_row($bulkResult)){
					if (in_array((string)$iterator, $_GET['user'])){
						$ret = wppb_manual_activate_signup($bulkRow[8]);
					}
					$iterator++;
				}
				
				$message = __("The selected users have been activated.", "profilebuilder");
				?>
				<script type="text/javascript">
					confirmECActionBulk('<?php echo get_bloginfo('url').'/wp-admin/users.php?page=unconfirmed_emails';?>', '<?php echo $message;?>');
				</script>
				<?php
			}
			
		}else{
			$message = __("Sorry, but you don't have permission to do that!", "profilebuilder");
			?>
			<script type="text/javascript">
				confirmECActionBulk('<?php echo get_bloginfo('url').'/wp-admin/';?>', '<?php echo $message;?>');
			</script>
			<?php
		}
        
    }
    
    
    /** ************************************************************************
     * REQUIRED! This is where you prepare your data for display. This method will
     * usually be used to query the database, sort and filter the data, and generally
     * get it ready to be displayed. At a minimum, we should set $this->items and
     * $this->set_pagination_args(), although the following properties and methods
     * are frequently interacted with here...
     * 
     * @global WPDB $wpdb
     * @uses $this->_column_headers
     * @uses $this->items
     * @uses $this->get_columns()
     * @uses $this->get_sortable_columns()
     * @uses $this->get_pagenum()
     * @uses $this->set_pagination_args()
     **************************************************************************/
    function prepare_items() {
        global $wpdb;
		
		$this->dataArray = array();
		$iterator = 0;
		
		$result = mysql_query("SELECT * FROM ".$wpdb->prefix."signups WHERE active=0");
		if($result)
			while ($row=mysql_fetch_row($result)){
				$tempArray = array('ID' => $iterator, 'username' => $row[3], 'email' => $row[4], 'registered'  => $row[5]);

				array_push($this->dataArray, $tempArray);
				$iterator++;
			} 

        /**
         * First, lets decide how many records per page to show
         */
        $per_page = apply_filters('wppb_email_confirmation_user_per_page_number', 20);
        
        
        /**
         * REQUIRED. Now we need to define our column headers. This includes a complete
         * array of columns to be displayed (slugs & titles), a list of columns
         * to keep hidden, and a list of columns that are sortable. Each of these
         * can be defined in another method (as we've done here) before being
         * used to build the value for our _column_headers property.
         */
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        
        
        /**
         * REQUIRED. Finally, we build an array to be used by the class for column 
         * headers. The $this->_column_headers property takes an array which contains
         * 3 other arrays. One for all columns, one for hidden columns, and one
         * for sortable columns.
         */
        $this->_column_headers = array($columns, $hidden, $sortable);
        
        
        /**
         * Optional. You can handle your bulk actions however you see fit. In this
         * case, we'll handle them within our package just to keep things clean.
         */
        $this->process_bulk_action();
        
        
        /**
         * Instead of querying a database, we're going to fetch the example data
         * property we created for use in this plugin. This makes this example 
         * package slightly different than one you might build on your own. In 
         * this example, we'll be using array manipulation to sort and paginate 
         * our data. In a real-world implementation, you will probably want to 
         * use sort and pagination data to build a custom query instead, as you'll
         * be able to use your precisely-queried data immediately.
         */
        $data = $this->dataArray;
                
        
        /**
         * This checks for sorting input and sorts the data in our array accordingly.
         * 
         * In a real-world situation involving a database, you would probably want 
         * to handle sorting by passing the 'orderby' and 'order' values directly 
         * to a custom query. The returned data will be pre-sorted, and this array
         * sorting technique would be unnecessary.
         */
        function usort_reorder($a,$b){
            $orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'username'; //If no sort, default to username
            $order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'asc'; //If no order, default to asc
            $result = strcmp($a[$orderby], $b[$orderby]); //Determine sort order
            return ($order==='asc') ? $result : -$result; //Send final sort direction to usort
        }
        usort($data, 'usort_reorder');

        /**
         * REQUIRED for pagination. Let's figure out what page the user is currently 
         * looking at. We'll need this later, so you should always include it in 
         * your own package classes.
         */
        $current_page = $this->get_pagenum();
        
        /**
         * REQUIRED for pagination. Let's check how many items are in our data array. 
         * In real-world use, this would be the total number of items in your database, 
         * without filtering. We'll need this later, so you should always include it 
         * in your own package classes.
         */
        $total_items = count($data);
        
        
        /**
         * The WP_List_Table class does not handle pagination for us, so we need
         * to ensure that the data is trimmed to only the current page. We can use
         * array_slice() to 
         */
        $data = array_slice($data,(($current_page-1)*$per_page),$per_page);
        
        
        
        /**
         * REQUIRED. Now we can add our *sorted* data to the items property, where 
         * it can be used by the rest of the class.
         */
        $this->items = $data;
        
        
        /**
         * REQUIRED. We also have to register our pagination options & calculations.
         */
        $this->set_pagination_args( array(
            'total_items' => $total_items,                  //WE have to calculate the total number of items
            'per_page'    => $per_page,                     //WE have to determine how many items to show on a page
            'total_pages' => ceil($total_items/$per_page)   //WE have to calculate the total number of pages
        ) );
    }
    
}





/** ************************ REGISTER THE PAGE ****************************
 *******************************************************************************
 * Now we just need to define an admin page.
 */
function wppb_add_ec_submenu_page() {
	if (is_multisite()){
		add_submenu_page( 'users.php', 'Unconfirmed Email Address', 'Unconfirmed Email Address', 'manage_options', 'unconfirmed_emails', 'wppb_unconfirmed_email_address_custom_menu_page' );
		remove_submenu_page( 'users.php', 'unconfirmed_emails' ); //hide the page in the admin menu
	
	}else{
		$wppb_generalSettings = get_option('wppb_general_settings', 'not_found');
		if($wppb_generalSettings != 'not_found')
			if(!empty($wppb_generalSettings['emailConfirmation']) && ($wppb_generalSettings['emailConfirmation'] == 'yes'))
				add_submenu_page( 'users.php', 'Unconfirmed Email Address', 'Unconfirmed Email Address', 'manage_options', 'unconfirmed_emails', 'wppb_unconfirmed_email_address_custom_menu_page' );
				remove_submenu_page( 'users.php', 'unconfirmed_emails' ); //hide the page in the admin menu
	}
}
add_action('admin_menu', 'wppb_add_ec_submenu_page');



/***************************** RENDER PAGE ********************************
 *******************************************************************************
 * This function renders the admin page. Although it's
 * possible to call prepare_items() and display() from the constructor, there
 * are often times where you may need to include logic here between those steps,
 * so we've instead called those methods explicitly. It keeps things flexible, and
 * it's the way the list tables are used in the WordPress core.
 */
function wppb_unconfirmed_email_address_custom_menu_page(){
    
    //Create an instance of our package class...
    $listTable = new wpp_list_unfonfirmed_email_table();
    //Fetch, prepare, sort, and filter our data...
    $listTable->prepare_items();
    
    ?>
    <div class="wrap">
        
        <div class="wrap"><div id="icon-users" class="icon32"></div><h2><?php _e('Users with Unconfirmed Email Address', 'profilebuilder');?></h2></div>
		
		<ul class="subsubsub">
			<li class="all"><a href="users.php"><?php _e('All Users', 'profilebuilder');?></a></li>
		</ul>
        
        <!-- Forms are NOT created automatically, so you need to wrap the table in one to use features like bulk actions -->
        <form id="movies-filter" method="get">
            <!-- For plugins, we also need to ensure that the form posts back to our current page -->
            <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
            <!-- Now we can render the completed list table -->
            <?php $listTable->display() ?>
        </form>
        
    </div>
    <?php
}