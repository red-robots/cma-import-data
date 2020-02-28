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


function cma_search_import_data()
{
    $message = '';

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
                        }
                    } // coupon code does not exists

                    $message = 'CSV file successfully imported.';
                    
                } // foreach

            } // if valid CSV file
            
        }  else {
            $message = 'Please upload a valid CSV file.';
        }  // File not empty
        
    }

    ?>
        <div class="wrap">
            <h2>CMA Search Form</h2>
            <p>This plugin will display the search form for CMA.</p>
            <code>
                [cma-search-form]
            </code>
           <p>&nbsp;</p>

            <hr>

            <?php if( $message ): ?>
                <div class="updated_settings_error notice is-dismissable"><strong><?php echo $message; ?></strong></div>
            <?php endif; ?>

            <?php include('import-form.php'); ?>
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




function activate(){ 
    flush_rewrite_rules();
}

function deactivate(){
    flush_rewrite_rules();
}


register_activation_hook( __FILE__, 'activate' );

register_deactivation_hook( __FILE__, 'deactivate' );

