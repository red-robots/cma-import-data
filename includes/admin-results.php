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

    <?php
    $paged = ( get_query_var( 'paged' ) ) ? absint( get_query_var( 'paged' ) ) : 1;
    $args = array(
        'posts_per_page'   => -1,
        'orderby'          => 'ID',
        'order'            => 'ASC',
        'post_type'        => 'properties',
        'post_status'      => 'publish',
    );
    $entries = new WP_Query($args);
    ?>
    <tbody>
        <?php if ( $entries->have_posts() ) {  ?>
            <?php while ( $entries->have_posts() ) : $entries->the_post();  ?>
            <tr>
                <td><?php echo get_field("coupon_code"); ?></td>
                <td><?php the_title(); ?></td>
                <td><?php echo get_field("address"); ?></td>
                <td><?php echo get_field("manager_name"); ?></td>
                <td><?php echo get_field("manager_email"); ?></td>
                <td><?php echo get_field("manager_phone"); ?></td>
            </tr>
            <?php endwhile; wp_reset_postdata(); ?>
        <?php } ?>
    </tbody>
</table>