<?php
/*
 * Template Name: Custom Search 
 */

get_header();
?>


<?php
        
        if( $_GET['selector'] && !empty( $_GET['selector'] ) ){
            $column  = $_GET['selector'];
        }

        if( $_GET['search_text'] && !empty( $_GET['search_text'] ) ){
            $text = $_GET['search_text'];
        }
    
?>

<main id="site-content" role="main">

    <div class="section-inner">

    <?php

    if( $column &&  $text ):   

        echo '<h4>Searching for: <span class="highlighted-color">'. $text .'</span></h4>';

        $paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;

        $args = array(
            'post_type'         => 'properties',
            'posts_per_page'    => 20, 
            'paged'             => $paged,   
            'meta_query'        => array(
                                    array(
                                         'key' => $column, 
                                         'value' => $text,
                                         'compare' => 'LIKE'
                                     )
            )
        );

        $query = new WP_Query($args);    

        if ( $query->have_posts() ) :       

            while ( $query->have_posts() ) :
                $query->the_post(); 
        ?>
                    <div class="search-custom-result" >
                        <div class="search_title">
                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        </div>
                        <div class="search_address">
                            <?php echo ( get_field('address') ) ? get_field('address') : ''; ?>
                        </div>        
                    </div>           
            <?php endwhile;  ?>

            <nav class="pagination">
                <?php  wp_cma_pagination( $query ); ?>
            </nav>

            <?php wp_reset_postdata(); ?>

            
       <?php else: ?>

            <div>Text <strong><?php echo $text; ?></strong> not found!</div>

        <?php endif;

    endif;  // search not empty

    ?>

    </div>

</main><!-- #site-content -->

<?php get_template_part( 'template-parts/footer-menus-widgets' ); ?>

<?php get_footer(); ?>