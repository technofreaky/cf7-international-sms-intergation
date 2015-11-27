<?php
if(!class_exists('WP_List_Table')){
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class cf7si_history_listing_table extends WP_List_Table {
    function __construct(){
        global $status, $page;
        parent::__construct( array(
            'singular'  => 'movie',
            'plural'    => 'movies',
            'ajax'      => false
        ) );
    }

    function column_default($item, $column_name){ return print_r($item,true); }
    function column_formNAME($item){ return $item['formNAME'].'<br/> <span style="font-weight: bold; color: rgb(113, 113, 113);">(id:'.$item['formID'].')</span>'; }
    function column_tomobile($item){ return $item['to']; }
    function column_response($item){ return $item['response']; }
	function column_message($item){ return urldecode($item['message']); }
	function column_sentdatetime($item){ return $item['datetime'];}
	
    function get_columns(){
        $columns = array( 
            'formNAME'     => __('By Form',CF7SI_TXT),
            'tomobile'    => __('To Mobile',CF7SI_TXT),
			'message' => __('Message',CF7SI_TXT),
			'response'    => __('Response',CF7SI_TXT),
            'sentdatetime'  => __('Sent Date',CF7SI_TXT),
        );
        return $columns;
    }


    function get_sortable_columns() {
        $sortable_columns = array(
            'title'     => array('title',false),     //true means it's already sorted
            'rating'    => array('rating',false),
            'director'  => array('director',false)
        );
        return $sortable_columns;
    }

    function get_bulk_actions() { $actions = array(); return $actions; }


    function process_bulk_action() {
        if( 'delete'===$this->current_action() ) {
            wp_die('Items deleted (or they would be if we had items to delete)!');
        }
    }


    function prepare_items() {
        global $wpdb; //This is used only if making any database queries
        $per_page = 5;
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->process_bulk_action();
        $data = get_option('wpcf7is_history',array());
        $current_page = $this->get_pagenum();
        $total_items = count($data);
		$data = array_slice($data,(($current_page-1)*$per_page),$per_page);
        $this->items = $data;
        
        $this->set_pagination_args( array(
            'total_items' => $total_items,                  //WE have to calculate the total number of items
            'per_page'    => $per_page,                     //WE have to determine how many items to show on a page
            'total_pages' => ceil($total_items/$per_page)   //WE have to calculate the total number of pages
        ) );
    }
}

function cf7si_history_listing(){
    $testListTable = new cf7si_history_listing_table();
    $testListTable->prepare_items();
    ?>
        <h2>SMS History</h2>
        <p>SMS History will be auto cleared if it exceeds 100 records</p>
        <form id="movies-filter" method="get">
            <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
            <?php $testListTable->display() ?>
        </form>
    <?php
}