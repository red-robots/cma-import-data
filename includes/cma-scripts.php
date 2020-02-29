<?php

/* Adding CSS and JS in plugins */

function cma_search_scripts()
{
    // Add Main CSS
    wp_enqueue_style( 'cma-search-datatables-style', plugins_url() . '/cma-import-data/css/datatables.min.css', null, true);
    wp_enqueue_style( 'cma-search-main-style', plugins_url() . '/cma-import-data/css/style.css', null, true);

    // Add Main JS
    wp_deregister_script('jquery');
    wp_register_script('jquery', plugins_url() .'/cma-import-data/js/jquery-3.4.1.min.js', false, '3.4.1', true);
    wp_enqueue_script('jquery');

   wp_enqueue_script( 'cma-search-datatables-script', plugins_url() . '/cma-import-data/js/datatables.min.js', array('jquery'), false, true);
   wp_enqueue_script( 'cma-search-main-script', plugins_url() . '/cma-import-data/js/main.js', array('jquery'), false, true);
}
add_action('wp_enqueue_scripts', 'cma_search_scripts');


function cma_admin_search_scripts(){
    wp_enqueue_style( 'cma-admin-search-main-style', plugins_url() . '/cma-import-data/css/admin-style.css', null, true);
}
add_action( 'admin_enqueue_scripts', 'cma_admin_search_scripts' );

/* Adding Menu in Admin side */
add_action('admin_menu', 'cma_admin_menu_option');
function cma_admin_menu_option()
{
    add_menu_page( 'CMA Search Data', 'CMA Search', 'manage_options', 'cma-search-data', 'cma_search_import_data', 'dashicons-schedule', 198 );
}


/* Add Properties Custom Post. This function can add multiple custom posts if needed. */
add_action('init', 'cma_cpt_init', 1);
function cma_cpt_init() {
    $post_types = array(
        array(
            'post_type' => 'properties',
            'menu_name' => 'Properties',
            'plural'    => 'Properties',
            'single'    => 'Property',
            'menu_icon' => 'dashicons-admin-multisite',
            'supports'  => array('title','editor','thumbnail')
        )
    );
    
    if($post_types) {
        foreach ($post_types as $p) {
            $p_type = ( isset($p['post_type']) && $p['post_type'] ) ? $p['post_type'] : ""; 
            $single_name = ( isset($p['single']) && $p['single'] ) ? $p['single'] : "Custom Post"; 
            $plural_name = ( isset($p['plural']) && $p['plural'] ) ? $p['plural'] : "Custom Post"; 
            $menu_name = ( isset($p['menu_name']) && $p['menu_name'] ) ? $p['menu_name'] : $p['plural']; 
            $menu_icon = ( isset($p['menu_icon']) && $p['menu_icon'] ) ? $p['menu_icon'] : "dashicons-admin-post"; 
            $supports = ( isset($p['supports']) && $p['supports'] ) ? $p['supports'] : array('title','editor','custom-fields','thumbnail'); 
            $taxonomies = ( isset($p['taxonomies']) && $p['taxonomies'] ) ? $p['taxonomies'] : array(); 
            $parent_item_colon = ( isset($p['parent_item_colon']) && $p['parent_item_colon'] ) ? $p['parent_item_colon'] : ""; 
            $menu_position = ( isset($p['menu_position']) && $p['menu_position'] ) ? $p['menu_position'] : 20; 
            
            if($p_type) {
                
                $labels = array(
                    'name' => _x($plural_name, 'post type general name'),
                    'singular_name' => _x($single_name, 'post type singular name'),
                    'add_new' => _x('Add New', $single_name),
                    'add_new_item' => __('Add New ' . $single_name),
                    'edit_item' => __('Edit ' . $single_name),
                    'new_item' => __('New ' . $single_name),
                    'view_item' => __('View ' . $single_name),
                    'search_items' => __('Search ' . $plural_name),
                    'not_found' =>  __('No ' . $plural_name . ' found'),
                    'not_found_in_trash' => __('No ' . $plural_name . ' found in Trash'), 
                    'parent_item_colon' => $parent_item_colon,
                    'menu_name' => $menu_name
                );
            
            
                $args = array(
                    'labels' => $labels,
                    'public' => true,
                    'publicly_queryable' => true,
                    'show_ui' => true, 
                    'show_in_menu' => true, 
                    'show_in_rest' => true,
                    'query_var' => true,
                    'rewrite' => true,
                    'capability_type' => 'post',
                    'has_archive' => false, 
                    'hierarchical' => false, // 'false' acts like posts 'true' acts like pages
                    'menu_position' => $menu_position,
                    'menu_icon'=> $menu_icon,
                    'supports' => $supports
                ); 
                
                register_post_type($p_type,$args); // name used in query
                
            }
            
        }
    }
}


function cma_search_import_data()
{
    $message = '';
    $isError = false;
    $isImported = false;
    $totalItems = 0;
    $itemsImported = array();

    if( array_key_exists( 'cma_search_submit_values', $_POST) ){

        $csv_file = $_FILES['cma_data_properties'];
                 
        if( ! ( $_FILES['cma_data_properties']['error'] > UPLOAD_ERR_OK ) ){

            $mimes = array('application/vnd.ms-excel','text/csv');
            if( ! in_array($_FILES['cma_data_properties']['type'], $mimes) ){
                 $message = 'Invalid File.';
            } else {

                $csv_to_array = array_map('str_getcsv', file($csv_file['tmp_name']));
                foreach ($csv_to_array as $key => $value) {
                  if ($key == 0)
                    continue;
                    
                  // coupon code does not exists
                    if( ! check_code_exists( $value[0] ) ) {

                        $my_post = array(
                             'post_title'   => $value[1],                 
                             'post_content' => $value[1],
                             'post_status'  => 'publish',
                             'post_type'    => 'properties',
                          );

                        $post_id = wp_insert_post($my_post);

                        if( $post_id ){
                            cma_update_post_meta( $post_id, 'coupon_code', $value[0] );
                            cma_update_post_meta( $post_id, 'community_name', $value[1] );
                            cma_update_post_meta( $post_id, 'address', $value[3] );
                            cma_update_post_meta( $post_id, 'manager_name', $value[2] );
                            cma_update_post_meta( $post_id, 'manager_email', $value[4] );
                            cma_update_post_meta( $post_id, 'manager_phone', $value[5] );
                            $itemsImported[] = $post_id;
                        }
                    } // coupon code does not exists

                    $isImported = true;
                    $totalItems = ($itemsImported) ? count($itemsImported) : 0;
                    $tmsg = ($totalItems>1) ? ' items':' item';
                    $message = 'CSV file successfully imported. ' . '(' . $totalItems . $tmsg. ')';
                    if($totalItems==0) {
                        $message = 'Item(s) imported already exists!';
                        $isError = TRUE;
                    }
                    
                } // foreach

            } // if valid CSV file
            
        }  else {
            $message = 'Please upload a valid CSV file.';
            $isError = TRUE;
        }  // File not empty
        
    }

    ?>
        <div class="wrap">
            <?php if ( isset($_GET['downloadjson']) && $_GET['downloadjson'] ) { forceDownLoad(plugins_url('cma-import-data').'/acf.json'); } ?>

            <h2>CMA Search Form</h2>

            <?php 
            $stat = ($isError) ? 'error':'updated';
            if( $message ): ?>
                <div id="message" class="<?php echo $stat; ?> settings-error notice is-dismissible"><p><strong><?php echo $message; ?></strong></p> <button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>
            <?php endif; ?>

            <p>Use this shortcode to display the search form for CMA.</p>
            <code>[cma-search-form]</code>
            <hr>

        
            <?php  
                // if( isset($_REQUEST['_wp_http_referer']) && $_REQUEST['_wp_http_referer'] ) {
                //     wp_redirect( $_REQUEST['_wp_http_referer'] );
                //     //exit;
                // }
            ?>

            <h2>CMA Import Properties Data</h2>
            <p>Import CSV file to populate the CMA custom post type <u>properties</u></p>
            <p><a href="<?php echo get_admin_url(); ?>admin.php?page=cma-search-data&downloadjson=1">Download this json file</a> and import it to ACF plugin.</p>
            <p style="margin:0 0 0"><strong>Custom Field Names:</strong></p>
            <ul id="acffields">
                <li>coupon_code</li>
                <li>community_name</li>
                <li>address</li>
                <li>manager_name</li>
                <li>manager_email</li>
                <li>manager_phone</li>
            </ul>
            
            <?php 
            include('import-form.php'); 
            //include('admin-results.php'); 
            ?>
            <?php if($isImported && $totalItems>0) { ?>
            <table class="wp-list-table widefat fixed striped cma-search">
                <thead>
                    <tr>
                        <th class="manage-column column-columnname" scope="col">coupon_code</th>
                        <th class="manage-column column-columnname" scope="col">community_name</th>
                        <th class="manage-column column-columnname" scope="col">address</th>
                        <th class="manage-column column-columnname" scope="col">manager_name</th>
                        <th class="manage-column column-columnname" scope="col">manager_email</th>
                        <th class="manage-column column-columnname" scope="col">manager_phone</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    foreach ($itemsImported as $postId) { ?>
                    <tr>
                        <td><?php echo get_field("coupon_code",$postId); ?></td>
                        <td><?php echo get_the_title($postId); ?></td>
                        <td><?php echo get_field("address",$postId); ?></td>
                        <td><?php echo get_field("manager_name",$postId); ?></td>
                        <td><?php echo get_field("manager_email",$postId); ?></td>
                        <td><?php echo get_field("manager_phone",$postId); ?></td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
            <?php } ?>
            
        </div>

    <?php

}


function cma_display_search_form()
{
    require_once plugin_dir_path( __FILE__ ) . 'custom-search.php';

    if( array_key_exists( 'search_text', $_GET)){
        require_once plugin_dir_path( __FILE__ ) . 'search-results.php';
    }
    
}
add_shortcode('cma-search-form', 'cma_display_search_form');

function cma_update_post_meta( $post_id, $field_name, $value = '' )
{
    if ( empty( $value ) || ! $value )
    {
        delete_post_meta( $post_id, $field_name );
    }
    elseif ( ! get_post_meta( $post_id, $field_name ) )
    {
        add_post_meta( $post_id, $field_name, $value );
    }
    else
    {
        update_post_meta( $post_id, $field_name, $value );
    }
}


function check_code_exists( $coupon_code )
{
    $args = array(
        'post_type'         => 'properties',
        'posts_per_page'    => -1,    
        'meta_query'        => array(
                                array(
                                     'key' => 'coupon_code', 
                                     'value' => $coupon_code,
                                     'compare' => '='
                                 )
        )
    );

    $query = new WP_Query($args);

    return ( $query->have_posts() ) ? true : false;
}


function forceDownLoad($filename)
{

    header("Pragma: public");
    header("Expires: 0"); // set expiration time
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Content-Type: application/force-download");
    header("Content-Type: application/octet-stream");
    header("Content-Type: application/download");
    header("Content-Disposition: attachment; filename=".basename($filename).";");
    header("Content-Transfer-Encoding: binary");
    header("Content-Length: ".filesize($filename));
    
    @readfile($filename);
    exit(0);
}


function activate(){ 
    flush_rewrite_rules();
}

function deactivate(){
    flush_rewrite_rules();
}


register_activation_hook( __FILE__, 'activate' );

register_deactivation_hook( __FILE__, 'deactivate' );


add_filter( 'page_template', 'wpa_search_page_template' );
function wpa_search_page_template( $page_template )
{
    if ( is_page( 'search' ) ) {
        $page_template = dirname( __FILE__ ) . '/search.php';
    }
    return $page_template;
}


function wp_cma_pagination( $custom_query ) {
    //global $wp_query;
        $big = 999999999; // need an unlikely integer
            echo paginate_links( array(
                'base'                  => '%_%',
                'format'                => '?paged=%#%',
                'current'               => max( 1, get_query_var('paged') ),
                'total'                 => $custom_query->max_num_pages,                
                'prev_next'             => true,
                'prev_text'             => __('« Previous'),
                'next_text'             => __('Next »'),
                'type'                  => 'plain',
                
        ) );
}


function wpa66273_disable_canonical_redirect( $query ) {
    if( 'search' == $query->query_vars['pagename'] )
        remove_filter( 'template_redirect', 'redirect_canonical' );
}
add_action( 'parse_query', 'wpa66273_disable_canonical_redirect' );