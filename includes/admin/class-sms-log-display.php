<?php
/**
 * The admin-specific functionality of the plugin.
 * @link http://wordpress.org/plugins/cf7-international-sms-integration
 * @package CF7ISMS
 * @subpackage CF7ISMS/Admin
 * @since 1.0
 */
if ( ! defined( 'WPINC' ) ) { die; }

class CF7_International_SMS_Admin_Log_Display {
    
    public function __construct() {
        add_action(CF7_ISMS_DB.'_form_fields',array($this,'list_addons'),10,2);
    }
    
    public function list_addons($none,$form_id){
        if($form_id == 'smslogs'){
            tt_render_list_page();
        }
	}
}


if(!class_exists('WP_List_Table')){
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
} 


class TT_Example_List_Table extends WP_List_Table {
    
    
     
    /** ************************************************************************
     * REQUIRED. Set up a constructor that references the parent constructor. We 
     * use the parent reference to set some default configs.
     ***************************************************************************/
    function __construct(){
        global $status, $page; 
        
        //Set parent defaults
        parent::__construct( array(
            'singular'  => 'smslog',     //singular name of the listed records
            'plural'    => 'smslogs',    //plural name of the listed records
            'ajax'      => true        //does this table support ajax?
        ) );
        
    }

    
    function column_default($item, $column_name){
        switch($column_name){
            default:
                return print_r(1,true); //Show the whole array for troubleshooting purposes
        }
        
        
    }
    
    function column_smsfor($item){
        if(isset($item['smsfor'])){
            if($item['smsfor'] == 'customer'){
                return '<span class="cf7isms_badge cf7isms_customer_badge">Customer</span>';
            } else if($item['smsfor'] == 'admin') {
                return '<span class="cf7isms_badge cf7isms_admin_badge">Admin</span>';
            } else {
                return '<span class="cf7isms_badge cf7isms_unknown_badge">Unknown</span>';
            }    
        } else {
                return '<span class="cf7isms_badge cf7isms_unknown_badge">Unknown</span>';
            }
        
    }
    
    function column_datetime($item){
        return date_i18n( get_option( 'date_format' ).' '.get_option( 'time_format' ),$item['datetime']);
    }

    function column_rorder($item){
        $edit_link = admin_url("admin.php?page=wpcf7&post=".$item['rorder']."&action=edit");
        return __("Form No  ",CF7_ISMS_TXT).'<a href="'.$edit_link.'">#'.$item['rorder'].'</a>';
    }
    
    function column_sentby($item){
        if(is_string($item['sentby'])){
            return $item['sentby'];
        } else if(is_int($item['sentby'])){
            $user = get_user_by('id',$item['sentby']);
            if ( ! empty( $user ) ) { 
                $link = get_edit_user_link( $item['sentby'] ); 
                return '<a href="'.$link.'"> '.$user->data->display_name.' <small>('.$user->data->user_login.')</small> </a> <br/> <small> ID : #'. $item['sentby'].' </small>  <br/> <small> Email : '.$user->data->user_email.' </small> ';
            }
        }
    }
    
    function column_tonumber($item){
        return $item['smsto'];
    }

    function column_response($item){
        if(is_array($item['response'])){
            if(isset($item['response'][0])){
                $return = $item['response'][0];
                if(count($item['response']) > 1)
                    $return .= ' <br/> <br/> '.sprintf(__("%s * (%s) Errors Occured %s",CF7_ISMS_TXT),'<strong>',count($item['response']),'</strong>');
                
                return $return;
            } else {
                $return = '';
                foreach($item['response'] as $res){
                    if(!empty($res))
                        $return .= $res.' <br/>';
                }
                return $return;
            }
            
        } else if(is_string($item['response'])){
            return $item['response'];    
        }
    }
    
    function column_smsid($item){ 
        $view_url = admin_url('admin-ajax.php?action=cf7_isms_view_fullsms&smsid='.$item['smsid'].'&width=600&height=550');
        $viewhttp_url = admin_url('admin-ajax.php?action=cf7_isms_view_http&smsid='.$item['smsid'].'&width=600&height=550');
        $actions = array(
            'view'      => sprintf('<a href="%s" class="thickbox">View Details</a>',$view_url),
            'delete'    => sprintf('<a href="javascript:void(0);" class="cf7_isms_delete_log" data-smsid="%s" >Delete</a>',$item['smsid']),
            //'view_http'   => sprintf('<a href="%s" class="thickbox">View HTTP Request</a>',$viewhttp_url),
        );
        
        //Return the title contents
        return sprintf('%1$s %2$s',
            /*$1%s*/ $item['smsid'], 
            /*$3%s*/ $this->row_actions($actions)
        );
    }

    function column_cb($item){
        return sprintf(
            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
            /*$1%s*/ $this->_args['singular'],  //Let's simply repurpose the table's singular label ("movie")
            /*$2%s*/ $item['smsid']                //The value of the checkbox should be the record's id
        );
    }

    function get_columns(){
        $columns = array(
            'cb'        => '<input type="checkbox" />', //Render a checkbox instead of text
            'smsfor'        => '', //Render a checkbox instead of text
            'smsid'     => __('LOG ID',CF7_ISMS_TXT),
            'datetime'    => __('Time',CF7_ISMS_TXT),
            'rorder'  => __("CF Form ",CF7_ISMS_TXT),
            'sentby'  => __("Sent By",CF7_ISMS_TXT),
            'tonumber' => __("SMS To",CF7_ISMS_TXT), 
            'response' => __("Response",CF7_ISMS_TXT), 
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
            'rorder'     => array('rorder',false),     //true means it's already sorted
            'tonumber'    => array('tonumber',false),
            'datetime' => array('datetime',true),
        );
        return $sortable_columns;
    }

 
    function get_bulk_actions() {
        $actions = array(
           // 'delete'    => 'Delete'
        );
        return $actions;
    }
 
    function process_bulk_action() { 
        if( 'delete'===$this->current_action() ) {
            $sendback = remove_query_arg( array('action', 'action2', 'paged', 'locked', 'smslog'), wp_get_referer() );
            if(isset($_REQUEST['smslog'])){
                $ids = $_REQUEST['smslog'];
                global $cf7ismslog;
                
                $deleted = array();
                $errors = array();
                
                foreach($ids as $id){
                    $del_status = $cf7ismslog->delete_log($id);
                    if($del_status){
                        $deleted[] = $id;
                    } else {
                        $errors[] = $id;
                    } 
                }
                
                
                if(!empty($deleted)){
                    $message = __("Following SMS Logs Deleted Successfully : %s ");
                    $ids = implode(',',$deleted);
                    $message = sprintf($message,$ids);
                    cf7_isms_admin_update( $message,1,'cf7ismslogdelok');
                }
                
                if(!empty($errors)){
                    $message = __("Unable to delete the following sms logs  : %s  <br/> it may be already deleted.");
                    $ids = implode(',',$errors);
                    $message = sprintf($message,$ids);
                    cf7_isms_admin_error( $message,1,'cf7ismslogdelfail');
                }
                
                wp_redirect($sendback);
 
            }
        }
        
    }
    
     
    
	public function single_row( $item ) {
        $status = isset($item['status']) ? $item['status'] : 'unknown';
		echo '<tr class="cf7_isms_'.$status.'">';
		$this->single_row_columns( $item );
		echo '</tr>';
	}
    
	/**
	 * Extra controls to be displayed between bulk actions and pagination
	 *
	 * @since 3.1.0
	 * @access protected
	 *
	 * @param string $which
	 */
	protected function extra_tablenav( $which ) {
        if($which != 'top'){return;}
        $value = isset($_REQUEST['per_page']) ? $_REQUEST['per_page'] : 5;
        echo 'Items Per Page : <input step="1" min="1" max="999" class="screen-per-page" name="per_page" id="items_perpage" maxlength="3" value="'.$value.'" type="number">';
    }
    
    
	/**
	 * Generate the table navigation above or below the table
	 *
	 * @since 3.1.0
	 * @access protected
	 * @param string $which
	 */
	protected function display_tablenav( $which ) { 
		?>
	<div class="tablenav <?php echo esc_attr( $which ); ?>">

		<?php if ( $this->has_items() ): ?>
		<div class="alignleft actions bulkactions">
			<?php $this->extra_tablenav( $which ); 
                  $this->bulk_actions( $which ); 
        
            $view_log = isset($_REQUEST['viewlog']) ? $_REQUEST['viewlog'] : '';
            if(!empty($view_log)){
                $url = cf7_isms_settings_page_link('smslogs');
                echo '<strong>Currently Viewing : </strong>  <a href="'.$url.'">'.$view_log.' -x</a>';
            }
            
            ?>
		</div>
		<?php endif;
		
		$this->pagination( $which );
?>

		<br class="clear" />
	</div>
<?php
	}
    
	/**
	 * An internal method that sets all the necessary pagination arguments
	 *
	 * @since 3.1.0
	 * @access protected
	 *
	 * @param array|string $args Array or string of arguments with information about the pagination.
	 */
	protected function set_pagination_args( $args ) {
		$args = wp_parse_args( $args, array(
			'total_items' => 0,
			'total_pages' => 0,
			'per_page' => 0,
		) );

		if ( !$args['total_pages'] && $args['per_page'] > 0 )
			$args['total_pages'] = ceil( $args['total_items'] / $args['per_page'] );

        $this->_pagination_args = $args;
	}
    
    function prepare_items() {
        global $wpdb,$cf7ismslog; //This is used only if making any database queries

        $per_page =  isset($_REQUEST['per_page']) ? $_REQUEST['per_page'] : 10;
        $view_log = isset($_REQUEST['viewlog']) ? $_REQUEST['viewlog'] : '';
        $data_org = $cf7ismslog->get_logs();
        $data = array();

        function usort_reorder($a,$b){
            $orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'datetime'; //If no sort, default to title
            $order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'decs'; //If no order, default to asc
            $result = strcmp($a[$orderby], $b[$orderby]); //Determine sort order
            return ($order==='asc') ? $result : -$result; //Send final sort direction to usort
        }
        
        if(!empty($view_log)){
            if(isset($data_org[$view_log])){
                $data[$view_log] = $data_org[$view_log];
            }
        } else {
            $data = $cf7ismslog->get_logs();
            usort($data, 'usort_reorder');
        }
        

        $current_page = $this->get_pagenum();
        $total_items = count($data);
        $data = array_slice($data,(($current_page-1)*$per_page),$per_page);
        $this->items = $data;
        
        $this->set_pagination_args( array(
            'total_items' => $total_items,                  //WE have to calculate the total number of items
            'per_page'    => $per_page,                     //WE have to determine how many items to show on a page
            'total_pages' => ceil($total_items/$per_page)   //WE have to calculate the total number of pages
        ) );
        
        
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->process_bulk_action();
    }
}

  
function tt_render_list_page(){ 
    $testListTable = new TT_Example_List_Table(); 
    $testListTable->prepare_items();
    add_thickbox();  
    $current_url = set_url_scheme( 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] );  
    ?>  
        </form>
        <form id="smslog-filter" method="get" action="<?php echo $current_url; ?> "> 
            <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
            <input type="hidden" name="tab" value="smslogs" />
            <?php $testListTable->display() ?> 
</form><form method="post">
    <?php
}

return new CF7_International_SMS_Admin_Log_Display;