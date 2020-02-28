<?php


if( $_GET['selector'] && !empty( $_GET['selector'] ) ){
    $column  = $_GET['selector'];
}

if( $_GET['search_text'] && !empty( $_GET['search_text'] ) ){
    $text = $_GET['search_text'];
}

if( $column && $text){
    $args = array(
        'post_type'         => 'properties',
        'posts_per_page'    => -1,    
        'meta_query'        => array(
                                array(
                                     'key' => $column, 
                                     'value' => $text,
                                     'compare' => 'LIKE'
                                 )
        )
    );

    $query = new WP_Query($args);

    echo '<h4>Searching for: <span class="highlighted-color">'. $text .'</span></h4>';

    if ( $query->have_posts() ) :     

    ?>
        <table id="cma_search">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Address</th>
                </tr>
            </thead>
            <tbody>

    <?php  

            while ( $query->have_posts() ) :
                $query->the_post(); 
    ?>

                <tr>
                    <td class="search_title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></td>
                    <td class="search_address"><?php echo ( get_field('address') ) ? get_field('address') : ''; ?></td>
                </tr>
           
    <?php   
            endwhile;
            wp_reset_query(); ?>
             </tbody>
            </table>

    <?php else: ?>

        <div>Text <strong><?php echo $text; ?></strong> not found!</div>

    <?php endif;
}

